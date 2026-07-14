<?php
require_once '../src/Config.php';
require_once '../src/Auth.php';

Auth::check();

$stmt = $pdo->query("SELECT * FROM criteria ORDER BY code ASC");
$criteria = $stmt->fetchAll();

$stmt = $pdo->query("SELECT * FROM alternatives ORDER BY is_ideal DESC, code ASC");
$all_alts = $stmt->fetchAll();

$alternatives = [];
$alt_data = [];
foreach ($all_alts as $alt) {
    $stmt = $pdo->prepare("SELECT criteria_id, value FROM evaluations WHERE alternative_id = ?");
    $stmt->execute([$alt['id']]);
    $scores = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
    // Only include if fully evaluated
    if (count($scores) >= count($criteria)) {
        $alternatives[] = $alt;
        $alt_data[$alt['id']] = $scores;
    }
}

$page_title = "Dadus Cripsi - CVTL DSS";
$current_page = "cripsi";
include '../templates/header.php';
include '../templates/sidebar.php';
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dadus Script (Nilai Alternatif)</h1>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Matriks Keputusan Awal</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                <thead class="bg-light text-center">
                    <tr>
                        <th rowspan="2" style="vertical-align: middle;">Alternativu</th>
                        <th colspan="<?php echo count($criteria); ?>">Kriteria</th>
                    </tr>
                    <tr>
                        <?php foreach ($criteria as $c) echo "<th>{$c['code']}</th>"; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($alternatives as $alt): ?>
                        <tr>
                            <td><strong><?php echo $alt['code']; ?> - <?php echo $alt['name']; ?></strong></td>
                            <?php foreach ($criteria as $c): ?>
                                <td class="text-center"><?php echo number_format($alt_data[$alt['id']][$c['id']] ?? 0, 2); ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../templates/footer.php'; ?>
