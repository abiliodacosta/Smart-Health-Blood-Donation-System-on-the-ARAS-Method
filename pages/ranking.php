<?php
require_once '../src/Config.php';
require_once '../src/ArasMethod.php';
require_once '../src/Auth.php';

Auth::check();

$aras = new ArasMethod($pdo);
$results = $aras->calculate();

$page_title = "Rezultadu Ranking - CVTL DSS";
$current_page = "ranking";
include '../templates/header.php';
include '../templates/sidebar.php';
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4 fade-in">
    <h1 class="h3 mb-0 text-gray-800">Rezultadu Ranking</h1>
</div>

<?php
// Extract Top 3
$ranking_data = [];
foreach ($results['ranking'] as $item) {
    if ($item['code'] == 'A0') continue;
    $ranking_data[] = $item;
}
$top3 = array_slice($ranking_data, 0, 3);
?>

<div class="glass-card mb-4 fade-in" style="border-left: 5px solid var(--primary); padding: 1.5rem;">
    <div class="d-flex flex-column flex-md-row align-items-center text-center text-md-left">
        <div class="mr-md-3 mb-3 mb-md-0">
            <div class="icon-circle bg-primary text-white shadow-sm" style="width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                <i class="fas fa-info-circle"></i>
            </div>
        </div>
        <div>
            <h5 class="font-weight-bold text-primary mb-1">Informasaun Importante</h5>
            <p class="text-muted mb-0">Informasaun ba Alternativu sira ne'ebé nia Persentagem menus husi <strong>55%</strong> Sei Labele Hasai Ran, Husi <strong>56%</strong> ba leten foin bele hasai Ran.</p>
        </div>
    </div>
</div>

<?php if (count($top3) >= 3): ?>
<div class="podium-container fade-in">
    <?php 
    $ranks = [1, 2, 3];
    foreach ($ranks as $r): 
        $data = $top3[$r-1];
    ?>
        <div class="podium-item p-rank-<?php echo $r; ?>">
            <div class="podium-medal">
                <i class="fas <?php echo $r == 1 ? 'fa-crown' : 'fa-medal'; ?>"></i>
            </div>
            <div class="podium-img">
                <i class="fas fa-user"></i>
            </div>
            <span class="podium-name"><?php echo $data['name']; ?></span>
            <div class="small text-muted mb-1"><?php echo $data['code']; ?></div>
            <div class="podium-score"><?php echo number_format($data['ki'] * 100, 2); ?>%</div>
            <div class="small font-weight-bold text-uppercase mt-2" style="letter-spacing: 1px; color: <?php echo $r==1 ? '#fbbf24' : ($r==2 ? '#94a3b8' : '#b45309'); ?>">
                <?php echo $r == 1 ? '1st Winner' : ($r == 2 ? '2nd Winner' : '3rd Winner'); ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<div class="card shadow mb-4 fade-in" style="border-radius: 1.5rem; overflow: hidden; border: none;">
    <div class="card-header py-3 bg-white border-bottom">
        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-list-ol mr-2"></i> Lista Ranking Alternativu Seluk</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead class="bg-light text-primary">
                    <tr>
                        <th class="border-0">Rank</th>
                        <th class="border-0 text-center">Kodu</th>
                        <th class="border-0">Naran Alternativu</th>
                        <th class="border-0">Percentagem</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $rank = 1;
                    $shown = 0;
                    foreach ($results['ranking'] as $item):
                        if ($item['code'] == 'A0') continue;
                        
                        // Skip top 3 for the table
                        if ($rank <= 3) {
                            $rank++;
                            continue;
                        }
                        $shown++;
                    ?>
                        <tr>
                            <td class="align-middle">
                                <div class="badge-ranking bg-secondary">
                                    <?php echo $rank; ?>
                                </div>
                            </td>
                            <td class="text-center align-middle"><span class="badge badge-primary"><?php echo $item['code']; ?></span></td>
                            <td class="align-middle font-weight-bold"><?php echo $item['name']; ?></td>
                            <td class="align-middle">
                                <div class="d-flex align-items-center">
                                    <div class="progress progress-sm flex-grow-1 mr-3" style="height: 10px; border-radius: 5px; background-color: #eaecf4;">
                                        <div class="progress-bar bg-success" role="progressbar"
                                            style="width: <?php echo $item['ki'] * 100; ?>%; border-radius: 5px;"
                                            aria-valuenow="<?php echo $item['ki'] * 100; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <span class="font-weight-bold text-success"><?php echo number_format($item['ki'] * 100, 2); ?>%</span>
                                </div>
                            </td>
                        </tr>
                    <?php $rank++;
                    endforeach; ?>
                    <?php if ($shown == 0): ?>
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted small">Seidauk iha alternativu seluk iha lista ne'e.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="alert alert-success shadow-sm" style="border-radius: 1rem;">
    <i class="fas fa-check-circle mr-1"></i>
    Alternativu ne'ebé iha valór <strong>Degree of Utility (Ki)</strong> ne'ebé boot liu mak sai nu'udar desizaun ne'ebé di'ak liu.
</div>

<?php include '../templates/footer.php'; ?>