<?php
require_once '../src/Config.php';
require_once '../src/Auth.php';

Auth::check();

$stmt = $pdo->query("SELECT * FROM criteria ORDER BY code ASC");
$criteria = $stmt->fetchAll();

$page_title = "Relatorio Kriteria - CVTL DSS";
$current_page = "report_criteria";
include '../templates/header.php';
include '../templates/sidebar.php';
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4 d-print-none">
    <h1 class="h3 mb-0 text-gray-800">Relatorio Kriteria</h1>
    <div class="mt-3 mt-sm-0">
        <div class="btn-group shadow-sm w-100">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print fa-sm"></i> <span class="d-none d-md-inline ml-1">Imprime</span>
            </button>
            <button onclick="exportTableToExcel('critTableContent', 'Relatorio_Kriteria')" class="btn btn-success">
                <i class="fas fa-file-excel fa-sm"></i> <span class="d-none d-md-inline ml-1">Excel</span>
            </button>
            <button onclick="exportTableToWord('critTableContent', 'Relatorio_Kriteria')" class="btn btn-info">
                <i class="fas fa-file-word fa-sm"></i> <span class="d-none d-md-inline ml-1">Word</span>
            </button>
        </div>
    </div>
</div>

<div id="critTableContent">
<div class="card shadow mb-4 print-content" id="critTable">
    <div class="card-body">
        <!-- Report Header -->
        <div class="text-center mb-5">
            <?php
            $logo_path = '../assets/images/cvtl_logo.png';
            $logo_data = '';
            if (file_exists($logo_path)) {
                $type = pathinfo($logo_path, PATHINFO_EXTENSION);
                $data = file_get_contents($logo_path);
                $logo_data = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
            ?>
            <img src="<?php echo $logo_data; ?>" alt="CVTL Logo" style="width: 80px;" class="mb-3">
            <h4 class="font-weight-bold text-dark mb-1">CRUZ VERMELHA DE TIMOR-LESTE (CVTL)</h4>
            <h5 class="text-dark mb-3">SISTEMA APOIO DESIZAUN DOASAUN RAAN</h5>
            <div style="border-bottom: 2px solid #333; margin: 20px 0;"></div>
            <h5 class="font-weight-bold text-uppercase">Relatorio Dadus Kriteria Analysis</h5>
            <p class="text-muted small">Data: <?php echo date('d/m/Y H:i'); ?></p>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                <thead class="bg-light text-dark text-center">
                    <tr>
                        <th width="10%">No</th>
                        <th width="15%">Kodu</th>
                        <th width="45%">Naran Kriteria</th>
                        <th width="15%">Atributu</th>
                        <th width="15%">Peza (Weight)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    foreach ($criteria as $crit): 
                    ?>
                        <tr>
                            <td class="text-center"><?php echo $no++; ?></td>
                            <td class="text-center"><strong><?php echo $crit['code']; ?></strong></td>
                            <td><?php echo $crit['name']; ?></td>
                            <td class="text-center text-capitalize"><?php echo $crit['type']; ?></td>
                            <td class="text-center font-weight-bold"><?php echo ($crit['weight'] * 100); ?>%</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Footer Signature -->
        <div class="row d-none d-print-flex signature-section">
            <div class="col-8"></div>
            <div class="col-4 text-center">
                <p>Dili, <?php echo date('d/m/Y'); ?></p>
                <p class="mb-5">Aprova husi,</p>
                <br><br>
                <p class="font-weight-bold mb-0">( ........................................ )</p>
                <p class="small text-muted">Administrator CVTL</p>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .sidebar, .navbar, .d-print-none, .scroll-to-top, .sticky-footer {
        display: none !important;
    }
    #content-wrapper {
        margin-left: 0 !important;
        background-color: white !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    .container-fluid {
        padding: 0 !important;
    }
    body {
        background-color: white !important;
        color: black !important;
    }
    .print-content {
        width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    .table thead th {
        background-color: #f8f9fc !important;
        color: black !important;
        border-color: #333 !important;
    }
    .table td, .table th {
        border-color: #333 !important;
    }
    .signature-section {
        page-break-inside: avoid;
        margin-top: 50px !important;
    }
}
</style>

<?php include '../templates/footer.php'; ?>
