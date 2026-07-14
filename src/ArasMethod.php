<?php

class ArasMethod {
    private $pdo;
    private $criteria = [];
    private $alternatives = [];
    private $matrix = [];
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->loadData();
    }

    private function loadData() {
        // Load criteria
        $stmt = $this->pdo->query("SELECT * FROM criteria ORDER BY code ASC");
        $this->criteria = $stmt->fetchAll();
        $crit_count = count($this->criteria);

        // Load all alternatives
        $stmt = $this->pdo->query("SELECT * FROM alternatives WHERE is_ideal = 0 ORDER BY code ASC");
        $all_alts = $stmt->fetchAll();

        // Load matrix and filter fully evaluated ones
        $this->alternatives = [];
        $this->matrix = [];
        foreach ($all_alts as $alt) {
            $stmt = $this->pdo->prepare("SELECT criteria_id, value FROM evaluations WHERE alternative_id = ?");
            $stmt->execute([$alt['id']]);
            $scores = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
            
            if (count($scores) >= $crit_count && $crit_count > 0) {
                $this->alternatives[] = $alt;
                foreach ($this->criteria as $crit) {
                    $this->matrix[$alt['code']][$crit['code']] = $scores[$crit['id']] ?? 0;
                }
            }
        }

        // Calculate A0 (Optimal Solution) dynamically
        if (!empty($this->matrix)) {
            foreach ($this->criteria as $crit) {
                $code = $crit['code'];
                $values = array_column($this->matrix, $code);
                if ($crit['type'] == 'benefit') {
                    $this->matrix['A0'][$code] = !empty($values) ? max($values) : 0;
                } else {
                    $this->matrix['A0'][$code] = !empty($values) ? min($values) : 0;
                }
            }
        }
    }

    public function calculate() {
        $results = [];
        
        // 1. Normalization
        $normalizedMatrix = [];
        $sums = [];

        // Pre-calculate sums for normalization
        foreach ($this->criteria as $crit) {
            $code = $crit['code'];
            $sums[$code] = 0;
            if ($crit['type'] == 'benefit') {
                foreach ($this->matrix as $altCode => $row) {
                    $sums[$code] += $row[$code];
                }
            } else {
                // Cost: use reciprocal
                foreach ($this->matrix as $altCode => $row) {
                    $sums[$code] += (1 / ($row[$code] ?: 1)); // Avoid div by zero
                }
            }
        }

        foreach ($this->matrix as $altCode => $row) {
            foreach ($this->criteria as $crit) {
                $code = $crit['code'];
                if ($crit['type'] == 'benefit') {
                    $normalizedMatrix[$altCode][$code] = $row[$code] / ($sums[$code] ?: 1);
                } else {
                    $normalizedMatrix[$altCode][$code] = (1 / ($row[$code] ?: 1)) / ($sums[$code] ?: 1);
                }
            }
        }

        // 2. Weighted Matrix
        $weightedMatrix = [];
        foreach ($normalizedMatrix as $altCode => $row) {
            foreach ($this->criteria as $crit) {
                $code = $crit['code'];
                $weightedMatrix[$altCode][$code] = $row[$code] * $crit['weight'];
            }
        }

        // 3. Optimality Function (Si)
        $optimalityFunction = [];
        foreach ($weightedMatrix as $altCode => $row) {
            $optimalityFunction[$altCode] = array_sum($row);
        }

        // 4. Degree of Utility (Ki)
        $s0 = $optimalityFunction['A0'];
        $degreeOfUtility = [];
        foreach ($optimalityFunction as $altCode => $si) {
            $degreeOfUtility[$altCode] = $si / ($s0 ?: 1);
        }

        // 5. Ranking
        $ranking = [];
        foreach ($this->alternatives as $alt) {
            $ranking[] = [
                'id' => $alt['id'],
                'code' => $alt['code'],
                'name' => $alt['name'],
                'si' => $optimalityFunction[$alt['code']] ?? 0,
                'ki' => $degreeOfUtility[$alt['code']] ?? 0
            ];
        }

        // Add A0 if needed for display in some contexts, but usually ranking is for real alts
        // Sort by Ki descending
        usort($ranking, function($a, $b) {
            return $b['ki'] <=> $a['ki'];
        });

        return [
            'matrix' => $this->matrix,
            'normalized' => $normalizedMatrix,
            'weighted' => $weightedMatrix,
            'si' => $optimalityFunction,
            'ki' => $degreeOfUtility,
            'criteria' => $this->criteria,
            'alternatives' => $this->alternatives,
            'ranking' => $ranking
        ];
    }
}
?>
