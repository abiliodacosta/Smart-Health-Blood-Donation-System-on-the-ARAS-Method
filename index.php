<?php
require_once 'src/Config.php';
require_once 'src/Auth.php';
require_once 'src/ArasMethod.php';

Auth::check();

// Fetch Stats
$stmt = $pdo->query("SELECT COUNT(*) FROM alternatives WHERE is_ideal = 0");
$total_alternatives = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM criteria");
$total_criteria = $stmt->fetchColumn();

// Fetch Top Rankings for Chart
$aras = new ArasMethod($pdo);
$results = $aras->calculate();
$top_donors = array_slice($results['ranking'], 0, 5); // Get top 5

$page_title = "Dashboard - CVTL DSS";
$current_page = "index";
include 'templates/header.php';
include 'templates/sidebar.php';
?>

<!-- Page Heading -->
<div class="mb-4">
    <h1 class="h3 mb-1 text-gray-800">Dashboard</h1>
    <p class="text-gray-600">Inovasaun Smart-Health Doasaun Raan iha Cruz Vermelha de Timor-Leste bazeia ba Métode ARAS</p>
</div>

<!-- Content Row -->
<div class="row">

    <!-- Total Alternatives Card -->
    <div class="col-xl-4 col-md-6 mb-4">
        <a href="pages/alternatives" class="text-decoration-none card-hover-modern">
            <div class="card shadow-sm h-100 py-3 px-2 rounded-xl border-0 bg-gradient-primary-card text-white">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-2" style="opacity: 0.8; letter-spacing: 1px;">
                                Total Alternativu</div>
                            <div class="h3 mb-0 font-weight-bold text-white"><?php echo $total_alternatives; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-3x" style="opacity: 0.3;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Total Criteria Card -->
    <div class="col-xl-4 col-md-6 mb-4">
        <a href="pages/criteria" class="text-decoration-none card-hover-modern">
            <div class="card shadow-sm h-100 py-3 px-2 rounded-xl border-0 bg-gradient-success-card text-white">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-2" style="opacity: 0.8; letter-spacing: 1px;">
                                Total Kriteria Analysis</div>
                            <div class="h3 mb-0 font-weight-bold text-white"><?php echo $total_criteria; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-list fa-3x" style="opacity: 0.3;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Total Ranking Card -->
    <div class="col-xl-4 col-md-6 mb-4">
        <a href="pages/ranking" class="text-decoration-none card-hover-modern">
            <div class="card shadow-sm h-100 py-3 px-2 rounded-xl border-0 bg-gradient-warning-card text-white">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-2" style="opacity: 0.8; letter-spacing: 1px;">
                                Total Ranking (Evaluated)</div>
                            <div class="h3 mb-0 font-weight-bold text-white"><?php echo count($results['ranking']); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-trophy fa-3x" style="opacity: 0.3;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<style>
    .rounded-xl { border-radius: 1rem !important; }
    .bg-gradient-primary-card { background: linear-gradient(135deg, #4f46e5 0%, #312e81 100%); }
    .bg-gradient-success-card { background: linear-gradient(135deg, #10b981 0%, #064e3b 100%); }
    .bg-gradient-warning-card { background: linear-gradient(135deg, #f59e0b 0%, #78350f 100%); }
    
    .card-hover-modern:hover .card {
        transform: translateY(-8px);
        box-shadow: 0 1.5rem 2.5rem rgba(0, 0, 0, 0.2) !important;
        transition: all 0.3s ease;
    }
    .card-hover-modern .card {
        transition: all 0.3s ease;
    }
</style>



<div class="row">
    <!-- Chart -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Top 5 Alternativu (Ranking)</h6>
            </div>
            <div class="card-body">
                <div class="chart-bar" style="height: 320px;">
                    <canvas id="myRankingChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Instructions Card -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Informasaun Sistema</h6>
            </div>
            <div class="card-body">
                <p>Sistema ne'e utiliza Métode <strong>ARAS (Additive Ratio Assessment)</strong> hodi halo analiza ba doasaun raan iha CVTL.</p>
                <div class="small font-weight-bold text-primary mb-2">Pasu Kalkulasaun:</div>
                <ul class="small">
                    <li>Input dadus alternativu</li>
                    <li>Input Kriteria</li>
                    <li>Halo Avaliasaun</li>
                    <li>Normalisasaun Matris</li>
                    <li>Multiplika ho peza kriteria</li>
                    <li>Kalkula Optimality (Si) & Utility (Ki)</li>
                    <li>Rezultado Ranking</li>
                    <li>Relatorio</li>
                </ul>
                <a href="pages/ranking" class="btn btn-sm btn-primary btn-block shadow-sm">
                    <i class="fas fa-trophy mr-1"></i> Haree Rezultadu Ranking
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    var ctx = document.getElementById("myRankingChart");
    var myBarChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [<?php foreach ($top_donors as $d) echo "'" . $d['name'] . "',"; ?>],
            datasets: [{
                label: "Degree of Utility (Ki)",
                backgroundColor: "#4f46e5",
                hoverBackgroundColor: "#4338ca",
                borderColor: "#4f46e5",
                data: [<?php foreach ($top_donors as $d) echo number_format($d['ki'], 4) . ","; ?>],
            }],
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 1
                }
            }
        }
    });
</script>

<?php include 'templates/footer.php'; ?>