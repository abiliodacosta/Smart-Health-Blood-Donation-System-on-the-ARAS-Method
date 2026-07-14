<?php
require_once '../src/Config.php';
require_once '../src/ArasMethod.php';
require_once '../src/Auth.php';

Auth::check();

$aras = new ArasMethod($pdo);
$results = $aras->calculate();

$page_title = "Dadus Normalisasaun - CVTL DSS";
$current_page = "normalization";
include '../templates/header.php';
include '../templates/sidebar.php';
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dadus Normalisasaun</h1>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Normalized Decision Matrix (R)</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-sm" id="dataTable" width="100%" cellspacing="0">
                <thead class="bg-light">
                    <tr>
                        <th>Alt</th>
                        <?php foreach ($results['criteria'] as $c) echo "<th>{$c['code']}</th>"; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results['normalized'] as $altCode => $row): ?>
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

<div class="alert alert-light border shadow-sm">
    <h6><i class="fas fa-calculator mr-2"></i> Formula Normalisasaun:</h6>
    <ul class="small mb-0">
        <li>Atributu <strong>Benefit</strong>: $r_{ij} = \frac{x_{ij}}{\sum x_{ij}}$</li>
        <li>Atributu <strong>Cost</strong>: $x_{ij} \to \frac{1}{x_{ij}}$, depois halo normalizasaun hanesan benefit.</li>
    </ul>
</div>

<?php include '../templates/footer.php'; ?>
