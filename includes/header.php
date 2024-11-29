<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' . APP_NAME : APP_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="<?php echo BASE_URL; ?>/assets/css/style.css" rel="stylesheet">
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="<?php echo BASE_URL; ?>/assets/js/notifications.js"></script>
</head>
<body class="bg-light">
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div class="bg-dark text-white" id="sidebar-wrapper">
            <div class="sidebar-heading p-3">
                <?php if (!empty($settings['system_logo'])): ?>
                    <img src="<?php echo BASE_URL; ?>/assets/images/uploads/<?php echo htmlspecialchars($settings['system_logo']); ?>" 
                         alt="<?php echo APP_NAME; ?>" class="img-fluid" style="max-height: 40px;">
                <?php else: ?>
                    <?php echo APP_NAME; ?>
                <?php endif; ?>
            </div>
            <div class="list-group list-group-flush">
                <a href="<?php echo BASE_URL; ?>/admin/dashboard.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
                <?php if ($user->isAdmin()): ?>
                <a href="<?php echo BASE_URL; ?>/admin/users.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <i class="bi bi-people me-2"></i> Usuários
                </a>
                <?php endif; ?>
                <a href="<?php echo BASE_URL; ?>/admin/products.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <i class="bi bi-box me-2"></i> Produtos
                </a>
                <a href="<?php echo BASE_URL; ?>/admin/sales.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <i class="bi bi-cart me-2"></i> Vendas
                </a>
                <a href="<?php echo BASE_URL; ?>/admin/reports.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <i class="bi bi-graph-up me-2"></i> Relatórios
                </a>
            </div>
        </div>

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
                <div class="container-fluid">
                    <button class="btn btn-dark" id="sidebarToggle">
                        <i class="bi bi-list"></i>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav ms-auto mt-2 mt-lg-0">
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                   data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-person-circle me-1"></i>
                                    <span id="currentUsername"><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                    <?php if ($user->isAdmin()): ?>
                                    <li>
                                        <a class="dropdown-item" href="<?php echo BASE_URL; ?>/admin/settings.php">
                                            <i class="bi bi-gear me-2"></i>Configurações
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <?php endif; ?>
                                    <li>
                                        <a class="dropdown-item" href="<?php echo BASE_URL; ?>/auth/logout.php">
                                            <i class="bi bi-box-arrow-right me-2"></i>Sair
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Alert Container -->
            <div id="alertContainer" style="position: fixed; top: 20px; right: 20px; z-index: 1050;"></div>

            <!-- Main Content Container -->
            <div class="container-fluid py-4">
