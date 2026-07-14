<?php
require_once '../src/Config.php';
require_once '../src/ArasMethod.php';
require_once '../src/Auth.php';

Auth::check();

$aras = new ArasMethod($pdo);
$results = $aras->calculate();

// Count Evaluated vs Unevaluated
$stmt = $pdo->query("SELECT COUNT(*) FROM alternatives WHERE is_ideal = 0");
$total_alts = $stmt->fetchColumn();

$crit_count = count($results['criteria']);
$evaluated_count = count($results['alternatives']);
$unevaluated_count = $total_alts - $evaluated_count;

$page_title = "Relatorio Geral - CVTL DSS";
$current_page = "reports";
include '../templates/header.php';
include '../templates/sidebar.php';
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4 d-print-none">
    <h1 class="h3 mb-0 text-gray-800">Relatorio Geral</h1>
    <div class="mt-3 mt-sm-0">
        <div class="btn-group shadow-sm w-100">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print fa-sm"></i> <span class="d-none d-md-inline ml-1">Imprime</span>
            </button>
            <button onclick="exportTableToExcel('reportContent', 'Relatorio_Geral_CVTL')" class="btn btn-success">
                <i class="fas fa-file-excel fa-sm"></i> <span class="d-none d-md-inline ml-1">Excel</span>
            </button>
            <button onclick="exportTableToWord('reportContent', 'Relatorio_Geral_CVTL')" class="btn btn-info">
                <i class="fas fa-file-word fa-sm"></i> <span class="d-none d-md-inline ml-1">Word</span>
            </button>
        </div>
    </div>
</div>

<div id="reportContent">
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
            <h4 class="font-weight-bold text-dark mb-1 text-uppercase">Cruz Vermelha de Timor-Leste (CVTL)</h4>
            <h5 class="text-dark mb-3">SISTEMA APOIO DESIZAUN DOASAUN RAAN BAZEIA BA METODE ARAS</h5>
            <div style="border-bottom: 2px solid #333; margin: 20px 0;"></div>
            <h5 class="font-weight-bold text-uppercase">Relatorio Geral Analize ho Kalkulasaun</h5>
            <p class="text-muted small">Data Relatorio: <?php echo date('d/m/Y H:i'); ?></p>
        </div>

        <!-- 1. SUMMARY STATS -->
        <div class="mb-5">
            <h6 class="font-weight-bold text-primary border-bottom pb-2">I. Rezumu Dadus Sistema</h6>
            <div class="row mt-3">
                <div class="col-md-6">
                    <table class="table table-sm table-borderless">
                        <tr><th width="60%">Total Dadus Alternativu</th><td>: <?php echo $total_alts; ?> Pesoas</td></tr>
                        <tr><th>Total Kriteria Analysis</th><td>: <?php echo $crit_count; ?> Kriteria</td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-sm table-borderless">
                        <tr><th width="60%">Alternativu ne'ebé Prinse Kriteria</th><td class="text-success">: <?php echo $evaluated_count; ?> Pesoas</td></tr>
                        <tr><th>Alternativu ne'ebé Seidauk Prinse</th><td class="text-danger">: <?php echo $unevaluated_count; ?> Pesoas</td></tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- 2. CRITERIA LIST -->
        <div class="mb-5">
            <h6 class="font-weight-bold text-primary border-bottom pb-2">II. Dadus Kriteria Analysis</h6>
            <div class="table-responsive">
                <table class="table table-bordered table-sm mt-2">
                    <thead class="bg-light text-center">
                        <tr>
                            <th>Kodu</th>
                            <th>Naran Kriteria</th>
                            <th>Atributu</th>
                            <th>Peza (Weight)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results['criteria'] as $crit): ?>
                            <tr>
                                <td class="text-center"><?php echo $crit['code']; ?></td>
                                <td><?php echo $crit['name']; ?></td>
                                <td class="text-center text-capitalize"><?php echo $crit['type']; ?></td>
                                <td class="text-center font-weight-bold"><?php echo ($crit['weight'] * 100); ?>%</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 3. CRISP MATRIX (DECISION) -->
        <div class="mb-5">
            <h6 class="font-weight-bold text-primary border-bottom pb-2">III. Matris Desizaun (Dadus Crisp)</h6>
            <div class="table-responsive">
                <table class="table table-bordered table-sm mt-2">
                    <thead class="bg-light text-center">
                        <tr>
                            <th>Alternativu</th>
                            <?php foreach ($results['criteria'] as $crit): ?>
                                <th><?php echo $crit['code']; ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results['matrix'] as $altCode => $row): ?>
                            <tr>
                                <td class="text-center font-weight-bold <?php echo $altCode == 'A0' ? 'text-primary' : ''; ?>">
                                    <?php echo $altCode == 'A0' ? 'A0 (Optimal)' : $altCode; ?>
                                </td>
                                <?php foreach ($results['criteria'] as $crit): ?>
                                    <td class="text-center"><?php echo $row[$crit['code']]; ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 4. NORMALIZATION MATRIX -->
        <div class="mb-5">
            <h6 class="font-weight-bold text-primary border-bottom pb-2">IV. Matris Normalizasaun</h6>
            <div class="table-responsive">
                <table class="table table-bordered table-sm mt-2">
                    <thead class="bg-light text-center">
                        <tr>
                            <th>Alternativu</th>
                            <?php foreach ($results['criteria'] as $crit): ?>
                                <th><?php echo $crit['code']; ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results['normalized'] as $altCode => $row): ?>
                            <tr>
                                <td class="text-center font-weight-bold <?php echo $altCode == 'A0' ? 'text-primary' : ''; ?>">
                                    <?php echo $altCode == 'A0' ? 'A0 (Optimal)' : $altCode; ?>
                                </td>
                                <?php foreach ($results['criteria'] as $crit): ?>
                                    <td class="text-center"><?php echo number_format($row[$crit['code']], 4); ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 5. FINAL RANKING -->
        <div class="mb-5">
            <h6 class="font-weight-bold text-primary border-bottom pb-2">V. Rezultadu Ranking Final</h6>
            <div class="table-responsive">
                <table class="table table-bordered table-striped mt-2">
                    <thead class="bg-light text-center">
                        <tr>
                            <th>Rank</th>
                            <th>Kodu</th>
                            <th>Naran Alternativu</th>
                            <th>Valór Si</th>
                            <th>Valór Ki</th>
                            <th>Persentagen (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $rank = 1;
                        foreach ($results['ranking'] as $item): 
                            $percentage = $item['ki'] * 100;
                        ?>
                            <tr>
                                <td class="text-center font-weight-bold"><?php echo $rank++; ?></td>
                                <td class="text-center"><?php echo $item['code']; ?></td>
                                <td class="font-weight-bold"><?php echo $item['name']; ?></td>
                                <td class="text-center"><?php echo number_format($item['si'], 4); ?></td>
                                <td class="text-center font-weight-bold text-primary"><?php echo number_format($item['ki'], 4); ?></td>
                                <td class="text-center font-weight-bold"><?php echo number_format($percentage, 2); ?>%</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer Signature -->
        <div class="row d-none d-print-flex signature-section mt-5">
            <div class="col-8">
                <p class="small">Imprime husi: <strong><?php echo $_SESSION['full_name']; ?></strong></p>
            </div>
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
    h6 {
        background-color: #f1f3f9 !important;
        padding: 5px !important;
    }
}
</style>

<?php include '../templates/footer.php'; ?>
