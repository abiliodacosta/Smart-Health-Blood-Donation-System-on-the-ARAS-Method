<?php
require_once '../src/Config.php';
require_once '../src/Auth.php';

Auth::check();

$stmt = $pdo->query("SELECT * FROM criteria ORDER BY code ASC");
$criteria = $stmt->fetchAll();

// Fetch all alternatives including Ideal Value
$stmt = $pdo->query("SELECT * FROM alternatives ORDER BY is_ideal DESC, code ASC");
$all_alts = $stmt->fetchAll();

$evaluated = [];
$unevaluated = [];

foreach ($all_alts as $alt) {
    $stmt = $pdo->prepare("SELECT criteria_id, value FROM evaluations WHERE alternative_id = ?");
    $stmt->execute([$alt['id']]);
    $scores = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    if (count($scores) >= count($criteria)) {
        $evaluated[] = ['alt' => $alt, 'scores' => $scores];
    } else {
        $unevaluated[] = $alt;
    }
}

$msg = $_GET['msg'] ?? '';

$page_title = "Dadus Avaliasaun - CVTL DSS";
$current_page = "evaluations";
include '../templates/header.php';
include '../templates/sidebar.php';
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dadus Avaliasaun (Scores)</h1>
    <button class="btn btn-sm btn-primary shadow-sm" onclick="openAddModal()">
        <i class="fas fa-plus fa-sm text-white-50"></i> Aumenta Avaliasaun
    </button>
</div>

<?php if ($msg): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo $msg; ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Lista Alternativu ne'ebé Avalia Ona</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead class="bg-light">
                    <tr>
                        <th>Code</th>
                        <th>Naran Kompletu</th>
                        <th>Status</th>
                        <th>Asaun</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($evaluated as $item): ?>
                        <tr>
                            <td><strong><?php echo $item['alt']['code']; ?></strong></td>
                            <td><?php echo $item['alt']['name']; ?></td>
                            <td><span class="badge badge-success"><i class="fas fa-check-circle"></i> Avalia Ona</span></td>
                            <td>
                                <div class="d-flex" style="gap: 5px;">
                                    <button class="btn btn-sm btn-primary" title="View Detail" onclick='openViewModal(<?php echo json_encode($item['alt']); ?>, <?php echo json_encode($item['scores']); ?>)'>
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-info" title="Edita Valór" onclick='openEditModal(<?php echo json_encode($item['alt']); ?>, <?php echo json_encode($item['scores']); ?>)'>
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <?php if (!$item['alt']['is_ideal']): ?>
                                        <a href="javascript:void(0)"
                                            class="btn btn-sm btn-danger" title="Hamos Avaliasaun" onclick="confirmDelete('../process?action=delete_evaluation&id=<?php echo $item['alt']['id']; ?>&redirect=pages/evaluations')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($evaluated)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">Seidauk iha dadus avaliasaun. Favor hili "Aumenta Avaliasaun" atu hahú.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- View Detail Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-id-card mr-2"></i> Detallu Avaliasaun Alternativu</h5>
                <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="p-3 bg-light border-left-primary rounded mb-4">
                    <h6 class="mb-1 font-weight-bold">Alternativu: <span id="vAltName" class="text-primary"></span></h6>
                    <p class="mb-0 small text-muted">Kodu: <span id="vAltCode"></span></p>
                </div>

                <h6 class="font-weight-bold text-gray-800 mb-3"><i class="fas fa-star text-warning mr-1"></i> Valór Avaliasaun</h6>
                <div class="row" id="vScoresContainer">
                    <!-- Scores injected here -->
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Taka</button>
            </div>
        </div>
    </div>
</div>

<!-- Evaluation Modal -->
<div class="modal fade" id="evalModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="evalModalTitle">Avaliasaun Alternativu</h5>
                <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="../process?action=save_scores" method="POST">
                <input type="hidden" name="redirect" value="pages/evaluations">
                <div class="modal-body">
                    <!-- Selection for new evaluation -->
                    <div id="selectionArea" class="form-group mb-4">
                        <label class="font-weight-bold">Hili Alternativu atu Avalia</label>
                        <select name="alternative_id" id="evalAltSelect" class="form-control form-control-lg border-left-primary" required>
                            <option value="">-- Hili Alternativu --</option>
                            <?php foreach ($unevaluated as $u): ?>
                                <option value="<?php echo $u['id']; ?>"><?php echo $u['code']; ?> - <?php echo $u['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Display for editing existing evaluation -->
                    <div id="displayArea" class="row mb-4 d-none">
                        <div class="col-12">
                            <div class="p-3 bg-light border-left-info rounded">
                                <h6 class="mb-1 font-weight-bold">Edita Avaliasaun ba: <span id="evalAltName" class="text-info"></span></h6>
                                <p class="mb-0 small text-muted">Kodu: <span id="evalAltCode"></span></p>
                                <input type="hidden" name="alternative_id_edit" id="evalAltId">
                            </div>
                        </div>
                    </div>

                    <h6 class="font-weight-bold text-gray-800 mb-3 border-bottom pb-2"><i class="fas fa-star text-warning mr-1"></i> Hili Valór Kriteria</h6>
                    <div class="row">
                        <?php
                        // Standardized scale 10 to 100
                        $standard_options = array_map(function ($i) {
                            return ['label' => 'Valór ' . $i, 'value' => $i];
                        }, range(100, 10, -10));

                        // Specific scale for K4 (10 to 50)
                        $k4_options = array_map(function ($i) {
                            return ['label' => 'Valór ' . $i, 'value' => $i];
                        }, range(50, 10, -10));

                        foreach ($criteria as $c):
                            $is_k4 = ($c['code'] == 'K4');
                            $options = $is_k4 ? $k4_options : $standard_options;
                        ?>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="small font-weight-bold text-gray-700 text-truncate d-block" title="<?php echo $c['name']; ?>">
                                        <?php echo $c['name']; ?> (<?php echo $c['code']; ?>)
                                    </label>
                                    
                                    <select name="scores[<?php echo $c['id']; ?>]" 
                                            id="score_<?php echo $c['id']; ?>" 
                                            class="form-control" 
                                            required>
                                        <option value="">-- Hili --</option>
                                        <?php foreach ($options as $opt): ?>
                                            <option value="<?php echo $opt['value']; ?>"><?php echo $opt['label']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Kansela</button>
                    <button type="submit" class="btn btn-primary shadow-sm"><i class="fas fa-save mr-1"></i> Rai Avaliasaun</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const criteriaMap = <?php
                        $cmap = [];
                        foreach ($criteria as $c) $cmap[$c['id']] = $c['name'] . " (" . $c['code'] . ")";
                        echo json_encode($cmap);
                        ?>;

    function openViewModal(alt, scores) {
        $('#vAltName').text(alt.name);
        $('#vAltCode').text(alt.code);

        let container = $('#vScoresContainer');
        container.empty();

        for (let critId in criteriaMap) {
            let val = scores[critId] || '0.00';
            container.append(`
                <div class="col-md-4 mb-2">
                    <div class="p-2 border rounded bg-light">
                        <div class="small text-muted text-uppercase text-truncate" style="font-size: 0.65rem;">${criteriaMap[critId]}</div>
                        <div class="font-weight-bold text-primary">${val}</div>
                    </div>
                </div>
            `);
        }

        $('#viewModal').modal('show');
    }

    function openAddModal() {
        $('#evalModalTitle').text('Aumenta Avaliasaun Foun');
        $('#selectionArea').removeClass('d-none');
        $('#displayArea').addClass('d-none');

        // Set name to alternative_id for the SELECT
        $('#evalAltSelect').attr('name', 'alternative_id').attr('required', true).val('');
        // Remove name from hidden input
        $('#evalAltId').attr('name', 'disabled_id').val('');

        // Reset scores
        $('select[id^="score_"]').val('');

        $('#evalModal').modal('show');
    }

    function openEditModal(alt, scores) {
        $('#evalModalTitle').text('Edita Avaliasaun');
        $('#selectionArea').addClass('d-none');
        $('#displayArea').removeClass('d-none');

        // Remove name from SELECT
        $('#evalAltSelect').attr('name', 'disabled_select').attr('required', false).val('');
        // Set name to alternative_id for the HIDDEN INPUT
        $('#evalAltId').attr('name', 'alternative_id').val(alt.id);

        $('#evalAltName').text(alt.name);
        $('#evalAltCode').text(alt.code);

        // Fill scores
        for (let critId in scores) {
            $('#score_' + critId).val(scores[critId]);
        }

        $('#evalModal').modal('show');
    }
</script>

<?php include '../templates/footer.php'; ?>