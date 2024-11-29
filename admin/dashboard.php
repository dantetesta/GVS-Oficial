<?php
require_once '../config/config.php';
require_once '../classes/User.php';
require_once '../classes/Database.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit();
}

// Instanciar classes necessárias
$db = new Database();
$user = new User($db);

// Carregar dados do usuário
$userData = $user->getUserById($_SESSION['user_id']);
if (!$userData) {
    session_destroy();
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo APP_NAME; ?></title>
    
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
                <?php include '../includes/dashboard_content.php'; ?>
            </div>
        </div>
    </div>
</div>

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
