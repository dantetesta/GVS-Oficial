<?php
require_once '../config/config.php';
require_once '../classes/User.php';
require_once '../classes/Database.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit();
}

// Verificar se é admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: ' . BASE_URL . '/admin/dashboard.php');
    exit();
}

// Instanciar classes necessárias
$db = new Database();
$user = new User($db);

// Carregar dados do usuário atual
$userData = $user->getUserById($_SESSION['user_id']);
if (!$userData) {
    session_destroy();
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit();
}

// Buscar todos os usuários
$stmt = $db->prepare("SELECT id, username, email, full_name, is_admin, created_at FROM users ORDER BY id DESC");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuários - <?php echo APP_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?php echo BASE_URL; ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>

<div class="container-fluid">
    <div class="row flex-nowrap">
        <!-- Sidebar -->
        <?php include '../includes/sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="col py-3">
            <!-- Top Navigation -->
            <?php include '../includes/topbar.php'; ?>
            
            <!-- Page Content -->
            <div class="container-fluid">
                <!-- Alert Container -->
                <div id="alertContainer"></div>

                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-0 text-gray-800">Gerenciar Usuários</h1>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                        <i class="bi bi-plus-circle me-1"></i> Novo Usuário
                    </button>
                </div>

                <!-- Users Table -->
                <div class="card table-card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nome</th>
                                        <th>Usuário</th>
                                        <th>Email</th>
                                        <th>Tipo</th>
                                        <th>Criado em</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $u): ?>
                                    <tr>
                                        <td><?php echo $u['id']; ?></td>
                                        <td><?php echo htmlspecialchars($u['full_name']); ?></td>
                                        <td><?php echo htmlspecialchars($u['username']); ?></td>
                                        <td><?php echo htmlspecialchars($u['email']); ?></td>
                                        <td>
                                            <span class="badge <?php echo $u['is_admin'] ? 'bg-primary' : 'bg-secondary'; ?>">
                                                <?php echo $u['is_admin'] ? 'Admin' : 'Usuário'; ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($u['created_at'])); ?></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary edit-user" data-id="<?php echo $u['id']; ?>" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                            <button type="button" class="btn btn-sm btn-danger delete-user" data-id="<?php echo $u['id']; ?>" title="Excluir">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">Novo Usuário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addUserForm">
                    <div class="mb-3">
                        <label for="fullName" class="form-label">Nome Completo</label>
                        <input type="text" class="form-control" id="fullName" required>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Nome de Usuário</label>
                        <input type="text" class="form-control" id="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="password" required>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="isAdmin">
                            <label class="form-check-label" for="isAdmin">Administrador</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="saveUser">Salvar</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Usuário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editUserForm">
                    <input type="hidden" id="editUserId">
                    <div class="mb-3">
                        <label for="editFullName" class="form-label">Nome Completo</label>
                        <input type="text" class="form-control" id="editFullName" required>
                    </div>
                    <div class="mb-3">
                        <label for="editEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="editEmail" required>
                    </div>
                    <div class="mb-3">
                        <label for="editPassword" class="form-label">Nova Senha (deixe em branco para manter)</label>
                        <input type="password" class="form-control" id="editPassword">
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="editIsAdmin">
                            <label class="form-check-label" for="editIsAdmin">Administrador</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="updateUser">Salvar Alterações</button>
            </div>
        </div>
    </div>
</div>

<!-- Profile Modal -->
<?php include '../includes/profile_modal.php'; ?>

<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Custom JavaScript -->
<script>
$(document).ready(function() {
    // Sidebar Toggle
    $('#sidebarToggle').click(function() {
        $('#sidebarMenu').toggleClass('show');
    });

    // Event Listeners
    $('#saveUser').click(saveUser);
    $('#updateUser').click(updateUser);
    
    // Usar delegação de eventos para botões dinâmicos
    $(document).on('click', '.edit-user', function(e) {
        e.preventDefault();
        const userId = $(this).data('id');
        console.log('Clique no botão editar:', userId);
        editUser(userId);
    });
    
    $(document).on('click', '.delete-user', function(e) {
        e.preventDefault();
        const userId = $(this).data('id');
        deleteUser(userId);
    });
});

function editUser(userId) {
    console.log('Editando usuário:', userId);
    
    // Limpar formulário
    $('#editUserForm')[0].reset();
    
    // Buscar dados do usuário
    $.get(BASE_URL + '/api/users/get.php', { id: userId })
        .done(function(response) {
            console.log('Resposta da API:', response);
            if (response.success) {
                const user = response.data;
                $('#editUserId').val(user.id);
                $('#editFullName').val(user.full_name);
                $('#editEmail').val(user.email);
                $('#editIsAdmin').prop('checked', user.is_admin == 1);
                
                // Abrir modal usando jQuery
                $('#editUserModal').modal('show');
            } else {
                showAlert(response.message || 'Erro ao carregar usuário', 'danger');
            }
        })
        .fail(function(xhr) {
            console.error('Erro ao carregar usuário:', xhr);
            showAlert('Erro ao carregar usuário: ' + (xhr.responseJSON?.message || 'Erro desconhecido'), 'danger');
        });
}

function updateUser() {
    const data = {
        id: $('#editUserId').val(),
        full_name: $('#editFullName').val(),
        email: $('#editEmail').val(),
        password: $('#editPassword').val(),
        is_admin: $('#editIsAdmin').is(':checked') ? 1 : 0
    };

    $.post(BASE_URL + '/api/users/update.php', data)
        .done(function(response) {
            if (response.success) {
                showAlert('Usuário atualizado com sucesso!', 'success');
                $('#editUserModal').modal('hide');
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showAlert(response.message || 'Erro ao atualizar usuário', 'danger');
            }
        })
        .fail(function(xhr) {
            showAlert('Erro ao atualizar usuário: ' + (xhr.responseJSON?.message || 'Erro desconhecido'), 'danger');
        });
}

function deleteUser(userId) {
    if (confirm('Tem certeza que deseja excluir este usuário?')) {
        $.post(BASE_URL + '/api/users/delete.php', { id: userId })
            .done(function(response) {
                if (response.success) {
                    showAlert('Usuário excluído com sucesso!', 'success');
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    showAlert(response.message || 'Erro ao excluir usuário', 'danger');
                }
            })
            .fail(function(xhr) {
                showAlert('Erro ao excluir usuário: ' + (xhr.responseJSON?.message || 'Erro desconhecido'), 'danger');
            });
    }
}

function saveUser() {
    const data = {
        username: $('#username').val(),
        full_name: $('#fullName').val(),
        email: $('#email').val(),
        password: $('#password').val(),
        is_admin: $('#isAdmin').is(':checked') ? 1 : 0
    };

    $.post(BASE_URL + '/api/users/create.php', data)
        .done(function(response) {
            if (response.success) {
                showAlert('Usuário criado com sucesso!', 'success');
                $('#addUserModal').modal('hide');
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showAlert(response.message || 'Erro ao criar usuário', 'danger');
            }
        })
        .fail(function(xhr) {
            showAlert('Erro ao criar usuário: ' + (xhr.responseJSON?.message || 'Erro desconhecido'), 'danger');
        });
}

function showAlert(message, type) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    $('#alertContainer').html(alertHtml);
}
</script>

</body>
</html>
