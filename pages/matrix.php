<?php
require_once '../src/Config.php';
require_once '../src/ArasMethod.php';
require_once '../src/Auth.php';

Auth::check();

$aras = new ArasMethod($pdo);
$results = $aras->calculate();

$page_title = "Matris Kalkulasaun - CVTL DSS";
$current_page = "matrix";
include '../templates/header.php';
include '../templates/sidebar.php';
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Matris Kalkulasaun (Terbobot)</h1>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Weighted Normalized Matrix (V)</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-sm" id="dataTable" width="100%" cellspacing="0">
                <thead class="bg-light text-center">
                    <tr>
                        <th>Alt</th>
                        <?php foreach ($results['criteria'] as $c): ?>
                            <th class="text-center"><?php echo $c['code']; ?> <br> <small>(<?php echo $c['weight']*100; ?>%)</small></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results['weighted'] as $altCode => $row): ?>
                        <tr>
                            <td><strong><?php echo $altCode; ?> <?php echo $altCode == 'A0' ? '(Ideal)' : ''; ?></strong></td>
                            <?php foreach ($row as $val) echo "<td>" . number_format($val, 4) . "</td>"; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="alert alert-info">
    <i class="fas fa-info-circle mr-1"></i> 
    Matris terbobot foti husi rezultadu normalisasaun ne'ebé multiplika ho peza (weight) husi kriteria ida-idak.
</div>

<?php include '../templates/footer.php'; ?>
