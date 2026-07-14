<?php
require_once '../src/Config.php';
require_once '../src/Auth.php';

Auth::check();

$stmt = $pdo->query("SELECT * FROM criteria ORDER BY code ASC");
$criteria = $stmt->fetchAll();

$msg = $_GET['msg'] ?? '';

$page_title = "Dadus Kriteria - CVTL DSS";
$current_page = "criteria";
include '../templates/header.php';
include '../templates/sidebar.php';
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dadus Kriteria</h1>
    <button class="btn btn-sm btn-primary shadow-sm" onclick="openModal('add')">
        <i class="fas fa-plus fa-sm text-white-50"></i> Aumenta Kriteria
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
        <h6 class="m-0 font-weight-bold text-primary">Lista Kriteria</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead class="bg-light">
                    <tr>
                        <th>Kodu</th>
                        <th>Naran Kriteria</th>
                        <th>Peza (Weight)</th>
                        <th>Atributu</th>
                        <th>Asaun</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($criteria as $c): ?>
                        <tr>
                            <td><strong><?php echo $c['code']; ?></strong></td>
                            <td><?php echo $c['name']; ?></td>
                            <td><?php echo number_format($c['weight'] * 100, 0); ?>%</td>
                            <td>
                                <span class="badge badge-<?php echo $c['type'] == 'benefit' ? 'success' : 'warning'; ?>">
                                    <?php echo ucfirst($c['type']); ?>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex" style="gap: 5px;">
                                    <button class="btn btn-sm btn-primary" title="View Detail" onclick='openViewModal(<?php echo json_encode($c); ?>)'>
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-info" title="Edita" onclick='openModal("edit", <?php echo json_encode($c); ?>)'>
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="javascript:void(0)" 
                                       class="btn btn-sm btn-danger" title="Hamos" onclick="confirmDelete('../process?action=delete_criteria&id=<?php echo $c['id']; ?>&redirect=pages/criteria')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="bg-light">
                    <?php 
                    $total_weight = 0;
                    foreach ($criteria as $c) {
                        $total_weight += $c['weight'];
                    }
                    ?>
                    <tr>
                        <th colspan="2" class="text-right font-weight-bold">Total Pursentu:</th>
                        <th class="font-weight-bold"><?php echo number_format($total_weight * 100, 0); ?>%</th>
                        <th colspan="2"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- View Detail Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-list-ul mr-2"></i> Detallu Dadus Kriteria</h5>
                <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <tr>
                        <th width="40%" class="bg-light">Kodu Kriteria</th>
                        <td id="vCode" class="font-weight-bold text-primary"></td>
                    </tr>
                    <tr>
                        <th class="bg-light">Naran Kriteria</th>
                        <td id="vName"></td>
                    </tr>
                    <tr>
                        <th class="bg-light">Peza (Weight)</th>
                        <td id="vWeight"></td>
                    </tr>
                    <tr>
                        <th class="bg-light">Atributu</th>
                        <td><span id="vType" class="badge"></span></td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Taka</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="criteriaModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Aumenta Kriteria</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form id="criteriaForm" action="../process" method="POST">
                <input type="hidden" name="redirect" value="pages/criteria">
                <div class="modal-body">
                    <input type="hidden" name="id" id="critId">
                    <div class="form-group">
                        <label>Kodu</label>
                        <input type="text" name="code" id="critCode" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Naran Kriteria</label>
                        <input type="text" name="name" id="critName" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Peza (0 - 1)</label>
                        <input type="number" step="0.01" name="weight" id="critWeight" class="form-control" required>
                        <small class="text-muted">Ex: 0.25 para 25%</small>
                    </div>
                    <div class="form-group">
                        <label>Atributu</label>
                        <select name="type" id="critType" class="form-control" required>
                            <option value="benefit">Benefit</option>
                            <option value="cost">Cost</option>
                        </select>
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
    function openViewModal(crit) {
        $('#vCode').text(crit.code);
        $('#vName').text(crit.name);
        $('#vWeight').text((crit.weight * 100) + '%');
        
        let typeBadge = $('#vType');
        typeBadge.text(crit.type.charAt(0).toUpperCase() + crit.type.slice(1));
        typeBadge.removeClass('badge-success badge-warning');
        typeBadge.addClass(crit.type === 'benefit' ? 'badge-success' : 'badge-warning');
        
        $('#viewModal').modal('show');
    }

    function openModal(mode, crit = null) {
        if (mode === 'add') {
            $('#modalTitle').text('Aumenta Kriteria Foun');
            $('#criteriaForm').attr('action', '../process?action=add_criteria');
            $('#criteriaForm')[0].reset();
            $('#critId').val('');
        } else {
            $('#modalTitle').text('Edita Kriteria');
            $('#criteriaForm').attr('action', '../process?action=edit_criteria');
            $('#critId').val(crit.id);
            $('#critCode').val(crit.code);
            $('#critName').val(crit.name);
            $('#critWeight').val(crit.weight);
            $('#critType').val(crit.type);
        }
        $('#criteriaModal').modal('show');
    }
</script>

<div class="alert alert-info">
    <i class="fas fa-info-circle mr-1"></i> 
    <strong>Informasaun:</strong> Kriteria sira ne'e fixa ona tuir nesesidade sistema nian. Atu halo mudansa ba Peza ka Atributu, favór kontaktu Administradór Sistema.
</div>

<?php include '../templates/footer.php'; ?>
