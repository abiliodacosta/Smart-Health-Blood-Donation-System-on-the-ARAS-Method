<?php
require_once '../src/Config.php';
require_once '../src/Auth.php';

Auth::checkRole(['Administrator']);

$stmt = $pdo->query("SELECT * FROM users ORDER BY level DESC, username ASC");
$users = $stmt->fetchAll();

$msg = $_GET['msg'] ?? '';

$page_title = "Dadus Utilisadór - CVTL DSS";
$current_page = "users";
include '../templates/header.php';
include '../templates/sidebar.php';
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dadus Utilisadór</h1>
    <button class="btn btn-sm btn-primary shadow-sm" onclick="openModal('add')">
        <i class="fas fa-plus fa-sm text-white-50"></i> Aumenta Utilisadór
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
    <div class="card-header py-3 bg-gradient-primary">
        <h6 class="m-0 font-weight-bold text-white">Lista Utilisadór Sistema</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead class="bg-light">
                    <tr>
                        <th>Foto</th>
                        <th>Naran Kompletu</th>
                        <th>Username</th>
                        <th>Sexu</th>
                        <th>Level</th>
                        <th>Aksun</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user):
                        $foto_path = !empty($user['foto']) ? '../assets/images/users/' . $user['foto'] : 'https://ui-avatars.com/api/?name=' . $user['full_name'] . '&background=random';
                    ?>
                        <tr>
                            <td class="text-center">
                                <img src="<?php echo $foto_path; ?>" class="rounded-circle border" style="width: 40px; height: 40px; object-fit: cover;">
                            </td>
                            <td><strong><?php echo $user['full_name']; ?></strong></td>
                            <td><?php echo $user['username']; ?></td>
                            <td><?php echo $user['sexu'] == 'L' ? 'Mane' : 'Feto'; ?></td>
                            <td>
                                <span class="badge <?php echo $user['level'] == 'Administrator' ? 'badge-primary' : 'badge-info'; ?>">
                                    <?php echo $user['level']; ?>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex" style="gap: 5px;">
                                    <button class="btn btn-sm btn-light border text-primary" onclick='viewDetail(<?php echo json_encode($user); ?>)' title="View Detail">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-info" onclick='openModal("edit", <?php echo json_encode($user); ?>)' title="Edita">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <?php if ($user['level'] == 'Admin'): ?>
                                    <button class="btn btn-sm btn-dark" onclick="confirmReset('<?php echo $user['id']; ?>')" title="Reset Password">
                                        <i class="fas fa-key"></i>
                                    </button>
                                    <button class="btn btn-sm <?php echo $user['is_active'] == 1 ? 'btn-success' : 'btn-warning'; ?>" 
                                            onclick="confirmToggle('<?php echo $user['id']; ?>', <?php echo $user['is_active']; ?>)" 
                                            title="<?php echo $user['is_active'] == 1 ? 'Xave Account' : 'Loke Account'; ?>">
                                        <i class="fas <?php echo $user['is_active'] == 1 ? 'fa-unlock' : 'fa-lock'; ?>"></i>
                                    </button>
                                    <?php endif; ?>
                                    <button class="btn btn-sm btn-danger"
                                        onclick="confirmDelete('../process?action=delete_user&id=<?php echo $user['id']; ?>&redirect=pages/users')" title="Hamos">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- User Form Modal -->
<div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTitle">Aumenta Utilisadór</h5>
                <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form id="userForm" action="../process" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="redirect" value="pages/users">
                <input type="hidden" name="id" id="userId">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 text-center mb-3">
                            <img id="previewFoto" src="https://ui-avatars.com/api/?name=New+User" class="rounded-circle border" style="width: 100px; height: 100px; object-fit: cover;">
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Foto</label>
                                <input type="file" name="foto" class="form-control-file" onchange="previewFile(this)">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Naran Kompletu</label>
                                <input type="text" name="full_name" id="userFullName" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Sexu</label>
                                <select name="sexu" id="userSexu" class="form-control" required>
                                    <option value="L">Mane</option>
                                    <option value="F">Feto</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Level</label>
                                <select name="level" id="userLevel" class="form-control" required>
                                    <option value="Admin">Admin</option>
                                    <option value="Administrator">Administrator</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Username</label>
                                <input type="text" name="username" id="userName" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label id="passLabel">Password</label>
                                <input type="password" name="password" id="userPass" class="form-control">
                                <small id="passHelp" class="form-text text-muted d-none">Husik mamuk karik lakohi troka password.</small>
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

<!-- View Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Detallu Utilisadór</h5>
                <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div class="text-center py-4 bg-light border-bottom">
                    <img id="detailFoto" src="" class="rounded-circle border shadow-sm" style="width: 120px; height: 120px; object-fit: cover;">
                    <h4 class="mt-3 font-weight-bold text-gray-800" id="detailName"></h4>
                    <span class="badge badge-primary px-3 py-2" id="detailLevel"></span>
                </div>
                <table class="table mb-0">
                    <tr>
                        <th class="bg-light" width="40%">Username</th>
                        <td id="detailUsername"></td>
                    </tr>
                    <tr>
                        <th class="bg-light">Sexu</th>
                        <td id="detailSexu"></td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Taka</button>
            </div>
        </div>
    </div>
</div>

<script>
    function previewFile(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#previewFoto').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function openModal(type, user = null) {
        if (type === 'add') {
            $('#modalTitle').text('Aumenta Utilisadór Foun');
            $('#userForm').attr('action', '../process?action=add_user');
            $('#userId').val('');
            $('#userName').val('');
            $('#userFullName').val('');
            $('#userSexu').val('L');
            $('#userLevel').val('Admin');
            $('#userPass').attr('required', true);
            $('#passHelp').addClass('d-none');
            $('#passLabel').text('Password');
            $('#previewFoto').attr('src', 'https://ui-avatars.com/api/?name=New+User');
        } else {
            $('#modalTitle').text('Edita Utilisadór');
            $('#userForm').attr('action', '../process?action=edit_user');
            $('#userId').val(user.id);
            $('#userName').val(user.username);
            $('#userFullName').val(user.full_name);
            $('#userSexu').val(user.sexu);
            $('#userLevel').val(user.level);
            $('#userPass').attr('required', false).val('');
            $('#passHelp').removeClass('d-none');
            $('#passLabel').text('Password Foun');

            let foto = user.foto ? '../assets/images/users/' + user.foto : 'https://ui-avatars.com/api/?name=' + user.full_name;
            $('#previewFoto').attr('src', foto);
        }
        $('#userModal').modal('show');
    }

    function viewDetail(user) {
        let foto = user.foto ? '../assets/images/users/' + user.foto : 'https://ui-avatars.com/api/?name=' + user.full_name;
        $('#detailFoto').attr('src', foto);
        $('#detailName').text(user.full_name);
        $('#detailUsername').text(user.username);
        $('#detailSexu').text(user.sexu == 'L' ? 'Mane' : 'Feto');
        $('#detailLevel').text(user.level);
        $('#detailModal').modal('show');
    }

    function confirmReset(id) {
        Swal.fire({
            title: 'Reset Password?',
            text: "Password sei reset ba '123456'!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#5a5c69',
            cancelButtonColor: '#858796',
            confirmButtonText: 'Sim, Reset!',
            cancelButtonText: 'Kansela'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '../process?action=reset_password&id=' + id + '&redirect=pages/users';
            }
        });
    }

    function confirmToggle(id, currentStatus) {
        let action = currentStatus == 1 ? 'Xave' : 'Loke';
        let color = currentStatus == 1 ? '#f6c23e' : '#1cc88a';
        
        Swal.fire({
            title: action + ' Utilisadór?',
            text: "Ita boot fiar atu " + action.toLowerCase() + " kontu ida ne'e?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: color,
            cancelButtonColor: '#858796',
            confirmButtonText: 'Sim, ' + action + '!',
            cancelButtonText: 'Kansela'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '../process?action=toggle_status&id=' + id + '&redirect=pages/users';
            }
        });
    }
</script>

<?php include '../templates/footer.php'; ?>