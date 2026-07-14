<?php
require_once '../src/Config.php';
require_once '../src/Auth.php';

Auth::check();

$stmt = $pdo->query("SELECT * FROM criteria ORDER BY code ASC");
$criteria = $stmt->fetchAll();

$stmt = $pdo->query("SELECT * FROM alternatives ORDER BY is_ideal DESC, code ASC");
$alternatives = $stmt->fetchAll();

$alt_data = [];
foreach ($alternatives as $alt) {
    $stmt = $pdo->prepare("SELECT criteria_id, value FROM evaluations WHERE alternative_id = ?");
    $stmt->execute([$alt['id']]);
    $scores = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    $alt_data[$alt['id']] = $scores;
}

$msg = $_GET['msg'] ?? '';

$page_title = "Dadus Alternativu - CVTL DSS";
$current_page = "alternatives";
include '../templates/header.php';
include '../templates/sidebar.php';
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dadus Alternativu</h1>
    <button class="btn btn-sm btn-primary shadow-sm" onclick="openModal('add')">
        <i class="fas fa-plus fa-sm text-white-50"></i> Aumenta Alternativu
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
        <h6 class="m-0 font-weight-bold text-primary">Lista Alternativu</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead class="bg-light">
                    <tr>
                        <th>Code</th>
                        <th>Naran Kompletu</th>
                        <th>Sexu</th>
                        <th>Tinan</th>
                        <th>Hela Fatin</th>
                        <th>Telefone</th>
                        <th>Ran</th>
                        <th>Asaun</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($alternatives as $alt): ?>
                        <tr>
                            <td><strong><?php echo $alt['code']; ?></strong></td>
                            <td><?php echo $alt['name']; ?></td>
                            <td><?php echo $alt['sexu']; ?></td>
                            <td><?php echo $alt['tinan']; ?></td>
                            <td><?php echo $alt['hela_fatin']; ?></td>
                            <td><?php echo $alt['telefone']; ?></td>
                            <td><span class="badge badge-danger"><?php echo $alt['tipu_ran']; ?></span></td>
                            <td>
                                <div class="d-flex" style="gap: 5px;">
                                    <button class="btn btn-sm btn-primary" title="View Detail" onclick='openViewModal(<?php echo json_encode($alt); ?>)'>
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-info" title="Edit" onclick='openModal("edit", <?php echo json_encode($alt); ?>)'>
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <?php if (!$alt['is_ideal']): ?>
                                    <a href="javascript:void(0)" 
                                       class="btn btn-sm btn-danger" title="Hamos" onclick="confirmDelete('../process?action=delete_alternative&id=<?php echo $alt['id']; ?>&redirect=pages/alternatives')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
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
                <h5 class="modal-title font-weight-bold"><i class="fas fa-id-card mr-2"></i> Detallu Dadus Alternativu</h5>
                <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr><td width="40%" class="text-muted">Kodu:</td><td id="vCode" class="font-weight-bold"></td></tr>
                            <tr><td class="text-muted">Naran:</td><td id="vName"></td></tr>
                            <tr><td class="text-muted">Sexu:</td><td id="vSexu"></td></tr>
                            <tr><td class="text-muted">Tinan:</td><td id="vTinan"></td></tr>
                        </table>
                    </div>
                    <div class="col-md-6 border-left">
                        <table class="table table-sm table-borderless">
                            <tr><td width="40%" class="text-muted">Hela Fatin:</td><td id="vHelaFatin"></td></tr>
                            <tr><td class="text-muted">Telefone:</td><td id="vTelefone"></td></tr>
                            <tr><td class="text-muted">Tipu Ran:</td><td><span id="vTipuRan" class="badge badge-danger"></span></td></tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Taka</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="donorModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Dadus Alternativu</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form id="donorForm" action="../process" method="POST">
                <input type="hidden" name="redirect" value="pages/alternatives">
                <div class="modal-body">
                    <input type="hidden" name="id" id="donorId">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Kodu</label>
                                <input type="text" name="code" id="donorCode" class="form-control" placeholder="Ex: A1" required>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="form-group">
                                <label>Naran Kompletu</label>
                                <input type="text" name="name" id="donorName" class="form-control" placeholder="Naran Alternativu" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Sexu</label>
                                <select name="sexu" id="donorSexu" class="form-control" required>
                                    <option value="Mane">Mane</option>
                                    <option value="Feto">Feto</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Tinan</label>
                                <input type="number" name="tinan" id="donorTinan" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Tipu Ran</label>
                                <select name="tipu_ran" id="donorTipuRan" class="form-control" required>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="AB">AB</option>
                                    <option value="O">O</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Hela Fatin</label>
                                <input type="text" name="hela_fatin" id="donorHelaFatin" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Numeru Telefone</label>
                                <input type="text" name="telefone" id="donorTelefone" class="form-control" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Kansela</button>
                    <button type="submit" class="btn btn-primary">Rai Dadus</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const criteriaMap = <?php 
        $cmap = [];
        foreach($criteria as $c) $cmap[$c['id']] = $c['name'] . " (" . $c['code'] . ")";
        echo json_encode($cmap); 
    ?>;

    function openViewModal(alt) {
        $('#vCode').text(alt.code);
        $('#vName').text(alt.name);
        $('#vSexu').text(alt.sexu);
        $('#vTinan').text(alt.tinan);
        $('#vHelaFatin').text(alt.hela_fatin);
        $('#vTelefone').text(alt.telefone);
        $('#vTipuRan').text(alt.tipu_ran);
        
        $('#viewModal').modal('show');
    }

    function openModal(mode, alt = null) {
        if (mode === 'add') {
            $('#modalTitle').text('Aumenta Alternativu Foun');
            $('#donorForm').attr('action', '../process?action=add_alternative');
            $('#donorForm')[0].reset();
            $('#donorId').val('');
        } else {
            $('#modalTitle').text('Edita Alternativu');
            $('#donorForm').attr('action', '../process?action=edit_alternative');
            $('#donorId').val(alt.id);
            $('#donorCode').val(alt.code);
            $('#donorName').val(alt.name);
            $('#donorSexu').val(alt.sexu);
            $('#donorTinan').val(alt.tinan);
            $('#donorHelaFatin').val(alt.hela_fatin);
            $('#donorTelefone').val(alt.telefone);
            $('#donorTipuRan').val(alt.tipu_ran);
        }
        $('#donorModal').modal('show');
    }
</script>

<?php include '../templates/footer.php'; ?>
