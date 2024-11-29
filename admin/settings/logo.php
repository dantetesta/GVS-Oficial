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
        <h5 class="card-title mb-0">Logotipos do Sistema</h5>
    </div>
    <div class="card-body">
        <form id="logoSettingsForm" method="post" enctype="multipart/form-data">
            <input type="hidden" name="form_type" value="logo">
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label">Logotipo do Sistema</label>
                    <div class="mb-3">
                        <?php if (!empty($currentSettings['system_logo'])): ?>
                            <img src="../../assets/images/uploads/<?php echo htmlspecialchars($currentSettings['system_logo']); ?>" 
                                 class="img-thumbnail mb-2" style="max-height: 100px;">
                        <?php endif; ?>
                    </div>
                    <div class="input-group">
                        <input type="file" class="form-control" id="system_logo" name="system_logo" 
                               accept="image/png,image/jpeg,image/gif">
                        <button class="btn btn-outline-secondary" type="button" onclick="document.getElementById('system_logo').value = ''">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <small class="text-muted">Recomendado: PNG transparente, 200x50 pixels</small>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Logotipo para Impressão</label>
                    <div class="mb-3">
                        <?php if (!empty($currentSettings['print_logo'])): ?>
                            <img src="../../assets/images/uploads/<?php echo htmlspecialchars($currentSettings['print_logo']); ?>" 
                                 class="img-thumbnail mb-2" style="max-height: 100px;">
                        <?php endif; ?>
                    </div>
                    <div class="input-group">
                        <input type="file" class="form-control" id="print_logo" name="print_logo" 
                               accept="image/png,image/jpeg,image/gif">
                        <button class="btn btn-outline-secondary" type="button" onclick="document.getElementById('print_logo').value = ''">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <small class="text-muted">Recomendado: PNG transparente, 300x300 pixels</small>
                </div>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-2"></i>Salvar Logotipos
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Preview de imagem antes do upload
function previewImage(input, previewElement) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = previewElement.querySelector('img') || document.createElement('img');
            img.src = e.target.result;
            img.classList.add('img-thumbnail', 'mb-2');
            img.style.maxHeight = '100px';
            if (!previewElement.querySelector('img')) {
                previewElement.insertBefore(img, previewElement.firstChild);
            }
        }
        reader.readAsDataURL(input.files[0]);
    }
}

document.getElementById('system_logo').addEventListener('change', function() {
    previewImage(this, this.parentElement.parentElement);
});

document.getElementById('print_logo').addEventListener('change', function() {
    previewImage(this, this.parentElement.parentElement);
});
</script>
