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

// Carregar configurações do sistema
$stmt = $db->prepare("SELECT * FROM settings WHERE id = 1");
$stmt->execute();
$settings = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações - <?php echo APP_NAME; ?></title>
    
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
                    <h1 class="h3 mb-0 text-gray-800">Configurações do Sistema</h1>
                </div>

                <!-- Settings Form -->
                <div class="row">
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-4">Configurações Gerais</h5>
                                <form id="settingsForm">
                                    <div class="mb-3">
                                        <label for="companyName" class="form-label">Nome da Empresa</label>
                                        <input type="text" class="form-control" id="companyName" name="company_name" 
                                               value="<?php echo htmlspecialchars($settings['company_name'] ?? ''); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email de Contato</label>
                                        <input type="email" class="form-control" id="email" name="contact_email"
                                               value="<?php echo htmlspecialchars($settings['contact_email'] ?? ''); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Telefone</label>
                                        <input type="text" class="form-control" id="phone" name="phone"
                                               value="<?php echo htmlspecialchars($settings['phone'] ?? ''); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="address" class="form-label">Endereço</label>
                                        <textarea class="form-control" id="address" name="address" rows="3"><?php echo htmlspecialchars($settings['address'] ?? ''); ?></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save me-1"></i> Salvar Configurações
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-4">Configurações de Email</h5>
                                <form id="emailSettingsForm">
                                    <div class="mb-3">
                                        <label for="smtpHost" class="form-label">Servidor SMTP</label>
                                        <input type="text" class="form-control" id="smtpHost" name="smtp_host"
                                               value="<?php echo htmlspecialchars($settings['smtp_host'] ?? ''); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="smtpPort" class="form-label">Porta SMTP</label>
                                        <input type="number" class="form-control" id="smtpPort" name="smtp_port"
                                               value="<?php echo htmlspecialchars($settings['smtp_port'] ?? ''); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="smtpUser" class="form-label">Usuário SMTP</label>
                                        <input type="text" class="form-control" id="smtpUser" name="smtp_user"
                                               value="<?php echo htmlspecialchars($settings['smtp_user'] ?? ''); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="smtpPass" class="form-label">Senha SMTP</label>
                                        <input type="password" class="form-control" id="smtpPass" name="smtp_pass"
                                               value="<?php echo htmlspecialchars($settings['smtp_pass'] ?? ''); ?>">
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save me-1"></i> Salvar Configurações de Email
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
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
document.addEventListener('DOMContentLoaded', function() {
    // Sidebar Toggle
    document.getElementById('sidebarToggle').addEventListener('click', function() {
        document.querySelector('#sidebarMenu').classList.toggle('show');
    });

    // Settings Form Submit
    document.getElementById('settingsForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            company_name: document.getElementById('companyName').value,
            contact_email: document.getElementById('email').value,
            phone: document.getElementById('phone').value,
            address: document.getElementById('address').value
        };

        fetch('<?php echo BASE_URL; ?>/api/settings/update.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Configurações atualizadas com sucesso!', 'success');
            } else {
                showAlert(data.message || 'Erro ao atualizar configurações!', 'danger');
            }
        })
        .catch(error => {
            showAlert('Erro ao atualizar configurações!', 'danger');
        });
    });

    // Email Settings Form Submit
    document.getElementById('emailSettingsForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            smtp_host: document.getElementById('smtpHost').value,
            smtp_port: document.getElementById('smtpPort').value,
            smtp_user: document.getElementById('smtpUser').value,
            smtp_pass: document.getElementById('smtpPass').value
        };

        fetch('<?php echo BASE_URL; ?>/api/settings/update_email.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Configurações de email atualizadas com sucesso!', 'success');
            } else {
                showAlert(data.message || 'Erro ao atualizar configurações de email!', 'danger');
            }
        })
        .catch(error => {
            showAlert('Erro ao atualizar configurações de email!', 'danger');
        });
    });
});

function showAlert(message, type) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    document.getElementById('alertContainer').innerHTML = alertHtml;
}
</script>

</body>
</html>
