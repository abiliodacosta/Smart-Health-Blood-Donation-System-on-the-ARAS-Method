<?php
// api/kalkulasi_aras.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Koneksi Database
$host = "localhost";
$db_name = "dss_aras_db";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=" . $host . ";dbname=" . $db_name, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("set names utf8");
} catch(PDOException $exception) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Connection error: " . $exception->getMessage()]);
    exit();
}

try {
    // 1. Ambil Data Kriteria
    $stmtCrit = $conn->prepare("SELECT id, code, name, weight, type FROM criteria ORDER BY id ASC");
    $stmtCrit->execute();
    $criterias = [];
    $total_weight = 0;
    while ($row = $stmtCrit->fetch(PDO::FETCH_ASSOC)) {
        $row['weight'] = (float)$row['weight'];
        $criterias[$row['id']] = $row;
        $total_weight += $row['weight'];
    }

    if(empty($criterias)) {
        throw new Exception("Data Kriteria kosong. Harap isi kriteria terlebih dahulu.");
    }

    // Normalisasi bobot agar jumlahnya = 1 (Opsional, tapi praktik yang baik di ARAS)
    if($total_weight > 0) {
        foreach($criterias as $k => $c) {
            $criterias[$k]['weight'] = $c['weight'] / $total_weight;
        }
    }

    // 2. Ambil Data Alternatif
    $stmtAlt = $conn->prepare("SELECT id, code, name FROM alternatives ORDER BY id ASC");
    $stmtAlt->execute();
    $alternatives = [];
    while ($row = $stmtAlt->fetch(PDO::FETCH_ASSOC)) {
        $alternatives[$row['id']] = $row;
    }

    if(empty($alternatives)) {
        throw new Exception("Data Alternatif kosong.");
    }

    // 3. Ambil Data Evaluasi (Matriks Keputusan Awal)
    $stmtEval = $conn->prepare("SELECT alternative_id, criteria_id, value FROM evaluations");
    $stmtEval->execute();
    $evaluations = [];
    while ($row = $stmtEval->fetch(PDO::FETCH_ASSOC)) {
        $evaluations[$row['alternative_id']][$row['criteria_id']] = (float)$row['value'];
    }

    // Pastikan semua nilai terisi. Jika kosong, set 0.
    $X = []; // Matriks X
    foreach($alternatives as $alt_id => $alt) {
        foreach($criterias as $crit_id => $crit) {
            $X[$alt_id][$crit_id] = isset($evaluations[$alt_id][$crit_id]) ? $evaluations[$alt_id][$crit_id] : 0;
        }
    }

    // 4. Menentukan Nilai Optimal (X0)
    $X0 = [];
    foreach($criterias as $crit_id => $crit) {
        $type = strtolower($crit['type']); // 'cost' atau 'benefit'
        $values = array_column($X, $crit_id);
        if ($type == 'cost') {
            $X0[$crit_id] = min($values);
        } else {
            $X0[$crit_id] = max($values);
        }
    }
    
    // Gabungkan X0 ke dalam Matriks (sebagai baris indeks 0)
    $X_full = $X;
    $X_full[0] = $X0; // Baris 0 = nilai optimal

    // 5. Normalisasi Matriks (R)
    $R = [];
    $sum_denom = [];
    // Hitung denominator
    foreach($criterias as $crit_id => $crit) {
        $type = strtolower($crit['type']);
        $sum = 0;
        foreach($X_full as $row) {
            $val = $row[$crit_id];
            if ($type == 'cost') {
                $sum += ($val != 0) ? (1 / $val) : 0;
            } else {
                $sum += $val;
            }
        }
        $sum_denom[$crit_id] = $sum;
    }

    // Hitung nilai normalisasi R
    foreach($X_full as $alt_id => $row) {
        foreach($criterias as $crit_id => $crit) {
            $type = strtolower($crit['type']);
            $val = $row[$crit_id];
            $denom = $sum_denom[$crit_id];
            
            if ($denom == 0) {
                $R[$alt_id][$crit_id] = 0;
            } else {
                if ($type == 'cost') {
                    $R[$alt_id][$crit_id] = ($val != 0) ? ((1 / $val) / $denom) : 0;
                } else {
                    $R[$alt_id][$crit_id] = $val / $denom;
                }
            }
        }
    }

    // 6. Matriks Ternormalisasi Berbobot (V)
    $V = [];
    foreach($R as $alt_id => $row) {
        foreach($criterias as $crit_id => $crit) {
            $V[$alt_id][$crit_id] = $row[$crit_id] * $crit['weight'];
        }
    }

    // 7. Menghitung Fungsi Optimalitas (Si)
    $S = [];
    foreach($V as $alt_id => $row) {
        $sum_S = 0;
        foreach($row as $v) {
            $sum_S += $v;
        }
        $S[$alt_id] = $sum_S;
    }

    // Nilai S optimal (dari baris 0)
    $S0 = $S[0];
    if($S0 == 0) {
        throw new Exception("Nilai optimal S0 = 0, tidak bisa membagi dengan 0.");
    }

    // 8. Derajat Utilitas (Ki) dan Perangkingan
    $results = [];
    foreach($alternatives as $alt_id => $alt) {
        $Ki = $S[$alt_id] / $S0;
        
        $results[] = [
            "alternative_id" => $alt_id,
            "code" => $alt['code'],
            "name" => $alt['name'],
            "si" => $S[$alt_id],
            "ki" => $Ki
        ];
    }

    // Urutkan DESC berdasarkan Ki
    usort($results, function($a, $b) {
        return $b['ki'] <=> $a['ki'];
    });

    // Tambahkan rank
    $rank = 1;
    foreach($results as &$res) {
        $res['rank'] = $rank++;
    }

    echo json_encode([
        "status" => "success",
        "message" => "Perhitungan ARAS berhasil",
        "data" => $results
    ]);

} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
