<?php
require_once '../../config/config.php';
require_once '../../includes/Database.php';
require_once '../../includes/User.php';
require_once '../../includes/Settings.php';

$user = new User();
$settings = new Settings();

if (!$user->isLoggedIn() || !$user->isAdmin()) {
    header('Location: ../../auth/login.php');
    exit();
}

$currentSettings = $settings->get();
?>

<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">Configurações SMTP</h5>
    </div>
    <div class="card-body">
        <form id="smtpSettingsForm" method="post">
            <input type="hidden" name="form_type" value="smtp">
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="smtp_host" class="form-label">Servidor SMTP</label>
                    <input type="text" class="form-control" id="smtp_host" name="smtp_host" 
                           value="<?php echo htmlspecialchars($currentSettings['smtp_host'] ?? ''); ?>" required>
                </div>
                <div class="col-md-3">
                    <label for="smtp_port" class="form-label">Porta</label>
                    <input type="number" class="form-control" id="smtp_port" name="smtp_port" 
                           value="<?php echo htmlspecialchars($currentSettings['smtp_port'] ?? '587'); ?>" required>
                </div>
                <div class="col-md-3">
                    <label for="smtp_secure" class="form-label">Segurança</label>
                    <select class="form-select" id="smtp_secure" name="smtp_secure" required>
                        <option value="tls" <?php echo ($currentSettings['smtp_secure'] ?? '') === 'tls' ? 'selected' : ''; ?>>TLS</option>
                        <option value="ssl" <?php echo ($currentSettings['smtp_secure'] ?? '') === 'ssl' ? 'selected' : ''; ?>>SSL</option>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="smtp_user" class="form-label">Usuário SMTP</label>
                    <input type="text" class="form-control" id="smtp_user" name="smtp_user" 
                           value="<?php echo htmlspecialchars($currentSettings['smtp_user'] ?? ''); ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="smtp_pass" class="form-label">Senha SMTP</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="smtp_pass" name="smtp_pass" 
                               value="<?php echo htmlspecialchars($currentSettings['smtp_pass'] ?? ''); ?>" required>
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('smtp_pass')">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="smtp_from" class="form-label">Email de Envio</label>
                    <input type="email" class="form-control" id="smtp_from" name="smtp_from" 
                           value="<?php echo htmlspecialchars($currentSettings['smtp_from'] ?? ''); ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="smtp_from_name" class="form-label">Nome de Exibição</label>
                    <input type="text" class="form-control" id="smtp_from_name" name="smtp_from_name" 
                           value="<?php echo htmlspecialchars($currentSettings['smtp_from_name'] ?? ''); ?>" required>
                </div>
            </div>

            <div class="text-end">
                <button type="button" class="btn btn-info me-2" onclick="testSmtp()">
                    <i class="bi bi-envelope-check me-2"></i>Testar Conexão
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-2"></i>Salvar Configurações
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
    input.setAttribute('type', type);
    
    const icon = input.nextElementSibling.querySelector('i');
    icon.classList.toggle('bi-eye');
    icon.classList.toggle('bi-eye-slash');
}

function testSmtp() {
    const form = document.getElementById('smtpSettingsForm');
    const formData = new FormData(form);
    formData.append('action', 'test_smtp');

    fetch('settings_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'Email de teste enviado com sucesso! Verifique sua caixa de entrada.');
        } else {
            showAlert('danger', 'Erro ao enviar email de teste: ' + data.message);
        }
    })
    .catch(error => {
        showAlert('danger', 'Erro ao testar conexão SMTP: ' + error.message);
    });
}

function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    const cardBody = document.querySelector('.card-body');
    cardBody.insertBefore(alertDiv, cardBody.firstChild);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}
</script>
