<?php
require_once '../src/Config.php';
require_once '../src/ArasMethod.php';
require_once '../src/Auth.php';

Auth::check();

$aras = new ArasMethod($pdo);
$results = $aras->calculate();

$page_title = "Valór Si & Ki - CVTL DSS";
$current_page = "si-ki";
include '../templates/header.php';
include '../templates/sidebar.php';
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Valór Optimality (Si) & Utility (Ki)</h1>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 bg-gradient-primary">
        <h6 class="m-0 font-weight-bold text-white">Final Calculation Results</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead class="bg-light text-center">
                    <tr>
                        <th width="15%">Alternativu</th>
                        <th width="40%">Optimality (Si)</th>
                        <th width="45%">Degree of Utility (Ki)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Sort for display by code
                    $displayData = $results['ranking'];
                    // Add A0 to display data if it's not in ranking
                    if (isset($results['si']['A0'])) {
                        array_unshift($displayData, [
                            'code' => 'A0',
                            'name' => 'Optimal Solution (A0)',
                            'si' => $results['si']['A0'],
                            'ki' => $results['ki']['A0'] ?? 1.0000
                        ]);
                    }
                    
                    foreach ($displayData as $item): ?>
                        <tr>
                            <td class="text-center font-weight-bold">
                                <?php echo $item['code']; ?>
                                <?php if($item['code'] == 'A0') echo '<br><small class="text-primary">(Ideal)</small>'; ?>
                            </td>
                            <td class="text-center py-3">
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($item['si'], 4); ?></div>
                                <div class="text-xs text-muted text-uppercase">Valór Si</div>
                            </td>
                            <td class="text-center py-3">
                                <div class="h5 mb-0 font-weight-bold text-primary"><?php echo number_format($item['ki'], 4); ?></div>
                                <div class="text-xs text-muted text-uppercase">Valór Ki (<?php echo number_format($item['ki'] * 100, 2); ?>%)</div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card border-left-info shadow mb-4">
            <div class="card-body">
                <h6 class="font-weight-bold text-info"><i class="fas fa-info-circle mr-2"></i> Saida mak Si?</h6>
                <p class="small mb-0 text-gray-700">
                    <strong>Optimality Function (Si)</strong> mak suma husi valór kriteria terbobot ba alternativu ida-idak. Valór ne'e hatudu nabilan (performance) husi alternativu ida bazeia ba kriteria hotu.
                </p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-left-warning shadow mb-4">
            <div class="card-body">
                <h6 class="font-weight-bold text-warning"><i class="fas fa-percentage mr-2"></i> Saida mak Ki?</h6>
                <p class="small mb-0 text-gray-700">
                    <strong>Degree of Utility (Ki)</strong> mak rasiu entre Si husi alternativu ida ho Si husi alternativu ideal (A0). Valór ne'e mak uza hodi determina ranking final.
                </p>
            </div>
        </div>
    </div>
</div>

<?php include '../templates/footer.php'; ?>
