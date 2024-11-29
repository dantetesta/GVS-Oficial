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
        <h5 class="card-title mb-0">Configurações Gerais</h5>
    </div>
    <div class="card-body">
        <form id="generalSettingsForm" method="post" enctype="multipart/form-data">
            <input type="hidden" name="form_type" value="general">
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="company_name" class="form-label">Nome da Empresa</label>
                    <input type="text" class="form-control" id="company_name" name="company_name" 
                           value="<?php echo htmlspecialchars($currentSettings['company_name'] ?? ''); ?>" required>
                </div>
                <div class="col-md-3">
                    <label for="company_type" class="form-label">Tipo</label>
                    <select class="form-select" id="company_type" name="company_type" required>
                        <option value="pf" <?php echo ($currentSettings['company_type'] ?? '') === 'pf' ? 'selected' : ''; ?>>Pessoa Física</option>
                        <option value="pj" <?php echo ($currentSettings['company_type'] ?? '') === 'pj' ? 'selected' : ''; ?>>Pessoa Jurídica</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="document_number" class="form-label">CPF/CNPJ</label>
                    <input type="text" class="form-control" id="document_number" name="document_number" 
                           value="<?php echo htmlspecialchars($currentSettings['document_number'] ?? ''); ?>" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-2">
                    <label for="zip_code" class="form-label">CEP</label>
                    <input type="text" class="form-control" id="zip_code" name="zip_code" 
                           value="<?php echo htmlspecialchars($currentSettings['zip_code'] ?? ''); ?>">
                </div>
                <div class="col-md-8">
                    <label for="address" class="form-label">Endereço</label>
                    <input type="text" class="form-control" id="address" name="address" 
                           value="<?php echo htmlspecialchars($currentSettings['address'] ?? ''); ?>">
                </div>
                <div class="col-md-2">
                    <label for="address_number" class="form-label">Número</label>
                    <input type="text" class="form-control" id="address_number" name="address_number" 
                           value="<?php echo htmlspecialchars($currentSettings['address_number'] ?? ''); ?>">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="complement" class="form-label">Complemento</label>
                    <input type="text" class="form-control" id="complement" name="complement" 
                           value="<?php echo htmlspecialchars($currentSettings['complement'] ?? ''); ?>">
                </div>
                <div class="col-md-4">
                    <label for="neighborhood" class="form-label">Bairro</label>
                    <input type="text" class="form-control" id="neighborhood" name="neighborhood" 
                           value="<?php echo htmlspecialchars($currentSettings['neighborhood'] ?? ''); ?>">
                </div>
                <div class="col-md-3">
                    <label for="city" class="form-label">Cidade</label>
                    <input type="text" class="form-control" id="city" name="city" 
                           value="<?php echo htmlspecialchars($currentSettings['city'] ?? ''); ?>">
                </div>
                <div class="col-md-1">
                    <label for="state" class="form-label">UF</label>
                    <input type="text" class="form-control" id="state" name="state" maxlength="2" 
                           value="<?php echo htmlspecialchars($currentSettings['state'] ?? ''); ?>">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="website" class="form-label">Website</label>
                    <input type="url" class="form-control" id="website" name="website" 
                           value="<?php echo htmlspecialchars($currentSettings['website'] ?? ''); ?>">
                </div>
                <div class="col-md-6">
                    <label for="public_email" class="form-label">Email Público</label>
                    <input type="email" class="form-control" id="public_email" name="public_email" 
                           value="<?php echo htmlspecialchars($currentSettings['public_email'] ?? ''); ?>">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="whatsapp" class="form-label">WhatsApp</label>
                    <input type="text" class="form-control" id="whatsapp" name="whatsapp" 
                           value="<?php echo htmlspecialchars($currentSettings['whatsapp'] ?? ''); ?>">
                </div>
                <div class="col-md-3">
                    <label for="facebook" class="form-label">Facebook</label>
                    <input type="url" class="form-control" id="facebook" name="facebook" 
                           value="<?php echo htmlspecialchars($currentSettings['facebook'] ?? ''); ?>">
                </div>
                <div class="col-md-3">
                    <label for="instagram" class="form-label">Instagram</label>
                    <input type="text" class="form-control" id="instagram" name="instagram" 
                           value="<?php echo htmlspecialchars($currentSettings['instagram'] ?? ''); ?>">
                </div>
                <div class="col-md-3">
                    <label for="linkedin" class="form-label">LinkedIn</label>
                    <input type="url" class="form-control" id="linkedin" name="linkedin" 
                           value="<?php echo htmlspecialchars($currentSettings['linkedin'] ?? ''); ?>">
                </div>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-2"></i>Salvar Configurações
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('zip_code').addEventListener('blur', function() {
    const cep = this.value.replace(/\D/g, '');
    if (cep.length === 8) {
        fetch(`https://viacep.com.br/ws/${cep}/json/`)
            .then(response => response.json())
            .then(data => {
                if (!data.erro) {
                    document.getElementById('address').value = data.logradouro;
                    document.getElementById('neighborhood').value = data.bairro;
                    document.getElementById('city').value = data.localidade;
                    document.getElementById('state').value = data.uf;
                }
            });
    }
});

// Máscara para CPF/CNPJ
document.getElementById('company_type').addEventListener('change', function() {
    const doc = document.getElementById('document_number');
    doc.value = '';
    if (this.value === 'pf') {
        doc.setAttribute('maxlength', '14');
        doc.setAttribute('placeholder', '000.000.000-00');
    } else {
        doc.setAttribute('maxlength', '18');
        doc.setAttribute('placeholder', '00.000.000/0000-00');
    }
});

// Máscara para WhatsApp
document.getElementById('whatsapp').addEventListener('input', function() {
    let value = this.value.replace(/\D/g, '');
    if (value.length <= 11) {
        value = value.replace(/^(\d{2})(\d)/g, '($1) $2');
        value = value.replace(/(\d)(\d{4})$/, '$1-$2');
    }
    this.value = value;
});

// Máscara para CEP
document.getElementById('zip_code').addEventListener('input', function() {
    let value = this.value.replace(/\D/g, '');
    value = value.replace(/^(\d{5})(\d)/, '$1-$2');
    this.value = value;
});

// Máscara para CPF/CNPJ
document.getElementById('document_number').addEventListener('input', function() {
    let value = this.value.replace(/\D/g, '');
    if (document.getElementById('company_type').value === 'pf') {
        if (value.length <= 11) {
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        }
    } else {
        if (value.length <= 14) {
            value = value.replace(/^(\d{2})(\d)/, '$1.$2');
            value = value.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
            value = value.replace(/\.(\d{3})(\d)/, '.$1/$2');
            value = value.replace(/(\d{4})(\d)/, '$1-$2');
        }
    }
    this.value = value;
});
</script>
