<?php
require_once '../config/config.php';
require_once '../classes/User.php';
require_once '../classes/Database.php';
require_once '../classes/Sale.php';

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
$sale = new Sale($db);

// Carregar dados do usuário atual
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
    <title>Gerenciar Vendas - <?php echo APP_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
                    <h1 class="h3 mb-0 text-gray-800">Gerenciar Vendas</h1>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newSaleModal">
                        <i class="bi bi-plus-circle me-1"></i> Nova Venda
                    </button>
                </div>

                <!-- Sales List -->
                <div class="card">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Lista de Vendas</h6>
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="refreshSales">
                                <i class="bi bi-arrow-clockwise"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#filterModal">
                                <i class="bi bi-funnel"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="salesTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Cliente</th>
                                        <th>Data</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Preenchido via AJAX -->
                                </tbody>
                            </table>
                        </div>
                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="dataTables_info" id="salesTableInfo" role="status" aria-live="polite">
                                Mostrando 0 até 0 de 0 registros
                            </div>
                            <nav aria-label="Page navigation">
                                <ul class="pagination" id="salesTablePagination">
                                    <!-- Preenchido via JavaScript -->
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Modals -->
<?php 
include 'sales/modals/new_sale_modal.php';
include 'sales/modals/edit_sale_modal.php';
include 'sales/modals/view_sale_modal.php';
include 'sales/modals/filter_modal.php';
include '../includes/profile_modal.php';
?>

<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- Custom JavaScript -->
<script src="<?php echo BASE_URL; ?>/assets/js/sales.js"></script>

</body>
</html>
