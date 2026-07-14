<?php
require_once '../src/Config.php';
require_once '../src/ArasMethod.php';
require_once '../src/Auth.php';

Auth::check();

$aras = new ArasMethod($pdo);
$results = $aras->calculate();

// Stats for header
$stmt = $pdo->query("SELECT COUNT(*) FROM alternatives WHERE is_ideal = 0");
$total_alternatives = $stmt->fetchColumn();

$crit_count = count($results['criteria']);
$evaluated_count = count($results['alternatives']);

$page_title = "Relatorio Ranking - CVTL DSS";
$current_page = "report_ranking";
include '../templates/header.php';
include '../templates/sidebar.php';
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4 d-print-none">
    <h1 class="h3 mb-0 text-gray-800">Relatorio Ranking</h1>
    <div class="mt-3 mt-sm-0">
        <div class="btn-group shadow-sm w-100">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print fa-sm"></i> <span class="d-none d-md-inline ml-1">Imprime</span>
            </button>
            <button onclick="exportTableToExcel('rankingTableContent', 'Relatorio_Ranking_CVTL')" class="btn btn-success">
                <i class="fas fa-file-excel fa-sm"></i> <span class="d-none d-md-inline ml-1">Excel</span>
            </button>
            <button onclick="exportTableToWord('rankingTableContent', 'Relatorio_Ranking_CVTL')" class="btn btn-info">
                <i class="fas fa-file-word fa-sm"></i> <span class="d-none d-md-inline ml-1">Word</span>
            </button>
        </div>
    </div>
</div>

<div id="rankingTableContent">
<div class="card shadow mb-4 print-content">
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
            <h5 class="font-weight-bold">RELATORIO FINAL REZULTADU RANKING</h5>
            <p class="text-muted small">Data Relatorio: <?php echo date('d/m/Y H:i'); ?></p>
        </div>

        <!-- Summary Stats -->
        <div class="row mb-4">
            <div class="col-6">
                <table class="table table-sm table-borderless">
                    <tr>
                        <th width="50%">Total Alternativu</th>
                        <td>: <?php echo $total_alternatives; ?> Pesoas</td>
                    </tr>
                    <tr>
                        <th>Alternativu ne'ebé Analiza</th>
                        <td>: <?php echo $evaluated_count; ?> Pesoas</td>
                    </tr>
                </table>
            </div>
            <div class="col-6 text-right">
                <p class="small">Imprime husi: <strong><?php echo $_SESSION['full_name']; ?></strong></p>
            </div>
        </div>

        <!-- Final Ranking Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                <thead class="bg-light text-dark text-center">
                    <tr>
                        <th width="10%">Rank</th>
                        <th width="15%">Kodu</th>
                        <th width="50%">Naran Alternativu</th>
                        <th width="25%">Persentagen (%)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $rank = 1;
                    foreach ($results['ranking'] as $item): 
                        if ($item['code'] == 'A0') continue;
                        $percentage = $item['ki'] * 100;
                    ?>
                        <tr>
                            <td class="text-center font-weight-bold text-dark" style="font-size: 1.1rem;"><?php echo $rank++; ?></td>
                            <td class="text-center"><?php echo $item['code']; ?></td>
                            <td class="font-weight-bold"><?php echo $item['name']; ?></td>
                            <td class="text-center">
                                <span class="font-weight-bold text-primary"><?php echo number_format($percentage, 2); ?>%</span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Footer Signature -->
        <div class="row d-none d-print-flex signature-section mt-5">
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
