<?php
require_once '../config/config.php';
require_once '../classes/Database.php';
require_once '../classes/User.php';

// Verificar se o usuário está logado e é admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit();
}

$db = new Database();
$user = new User($db);

// Buscar todos os usuários
$users = $user->getAllUsers();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Usuários - <?php echo APP_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?php echo BASE_URL; ?>/assets/css/style.css" rel="stylesheet">
    <!-- Toastr CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
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
            
            <!-- Content -->
            <div class="container-fluid">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-0 text-gray-800">Gerenciar Usuários</h1>
                    <button type="button" class="btn btn-primary" onclick="openUserModal()">
                        <i class="bi bi-plus-circle me-1"></i> Novo Usuário
                    </button>
                </div>

                <!-- Users Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nome Completo</th>
                                        <th>Usuário</th>
                                        <th>Email</th>
                                        <th>Tipo</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $u): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($u['full_name']); ?></td>
                                        <td><?php echo htmlspecialchars($u['username']); ?></td>
                                        <td><?php echo htmlspecialchars($u['email']); ?></td>
                                        <td>
                                            <span class="badge <?php echo $u['is_admin'] ? 'bg-primary' : 'bg-secondary'; ?>">
                                                <?php echo $u['is_admin'] ? 'Admin' : 'Usuário'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary" onclick="editUser(<?php echo $u['id']; ?>)">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                            <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete(<?php echo $u['id']; ?>, '<?php echo htmlspecialchars($u['username']); ?>')">
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

<!-- User Modal -->
<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Novo Usuário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="userForm">
                    <input type="hidden" id="userId" name="id">
                    
                    <div class="mb-3">
                        <label for="username" class="form-label">Nome de Usuário</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Nome Completo</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="password" name="password">
                        <div class="form-text" id="passwordHelp">
                            Deixe em branco para manter a senha atual ao editar.
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="is_admin" name="is_admin">
                            <label class="form-check-label" for="is_admin">Administrador</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="saveUser()">Salvar</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir o usuário <strong id="deleteUserName"></strong>?</p>
                <p class="text-danger">Esta ação não pode ser desfeita!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" onclick="deleteUser()">Excluir</button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
let userModal;
let deleteModal;
let userToDelete = null;

document.addEventListener('DOMContentLoaded', function() {
    userModal = new bootstrap.Modal(document.getElementById('userModal'));
    deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    
    // Configure toastr
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
    };
});

function openUserModal() {
    document.getElementById('userForm').reset();
    document.getElementById('userId').value = '';
    document.getElementById('modalTitle').textContent = 'Novo Usuário';
    document.getElementById('password').required = true;
    userModal.show();
}

function editUser(id) {
    fetch(`<?php echo BASE_URL; ?>/api/users/get.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const user = data.data;
                document.getElementById('userId').value = user.id;
                document.getElementById('username').value = user.username;
                document.getElementById('email').value = user.email;
                document.getElementById('full_name').value = user.full_name;
                document.getElementById('is_admin').checked = user.is_admin == 1;
                document.getElementById('password').required = false;
                document.getElementById('modalTitle').textContent = 'Editar Usuário';
                userModal.show();
            } else {
                toastr.error(data.message || 'Erro ao carregar usuário');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            toastr.error('Erro ao carregar dados do usuário');
        });
}

function saveUser() {
    const formData = new FormData(document.getElementById('userForm'));
    const userId = formData.get('id');
    const endpoint = userId ? 'update.php' : 'create.php';
    
    fetch(`<?php echo BASE_URL; ?>/api/users/${endpoint}`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            toastr.success(data.message || 'Usuário salvo com sucesso!');
            userModal.hide();
            setTimeout(() => window.location.reload(), 1000);
        } else {
            toastr.error(data.message || 'Erro ao salvar usuário');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        toastr.error('Erro ao salvar usuário');
    });
}

function confirmDelete(id, username) {
    userToDelete = id;
    document.getElementById('deleteUserName').textContent = username;
    deleteModal.show();
}

function deleteUser() {
    if (!userToDelete) return;
    
    const formData = new FormData();
    formData.append('id', userToDelete);
    
    fetch('<?php echo BASE_URL; ?>/api/users/delete.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            toastr.success(data.message || 'Usuário excluído com sucesso!');
            deleteModal.hide();
            setTimeout(() => window.location.reload(), 1000);
        } else {
            toastr.error(data.message || 'Erro ao excluir usuário');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        toastr.error('Erro ao excluir usuário');
    });
}
</script>

</body>
</html>
