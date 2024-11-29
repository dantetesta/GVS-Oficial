<div class="col-auto col-md-3 col-xl-2 px-sm-2 px-0 bg-primary">
    <div class="d-flex flex-column align-items-center align-items-sm-start px-3 pt-2 text-white min-vh-100">
        <a href="<?php echo BASE_URL; ?>/admin/dashboard.php" class="d-flex align-items-center pb-3 mb-md-1 mt-md-3 me-md-auto text-white text-decoration-none">
            <span class="fs-5 fw-bolder d-none d-sm-inline"><?php echo APP_NAME; ?></span>
        </a>
        <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-center align-items-sm-start w-100" id="menu">
            <li class="w-100">
                <a href="<?php echo BASE_URL; ?>/admin/dashboard.php" class="nav-link text-white px-0 align-middle <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                    <i class="bi bi-speedometer2"></i> <span class="ms-1 d-none d-sm-inline">Dashboard</span>
                </a>
            </li>
            <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
            <li class="w-100">
                <a href="<?php echo BASE_URL; ?>/admin/users.php" class="nav-link px-0 align-middle text-white <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>">
                    <i class="bi bi-people"></i> <span class="ms-1 d-none d-sm-inline">Usuários</span>
                </a>
            </li>
            <?php endif; ?>
            <li class="w-100">
                <a href="<?php echo BASE_URL; ?>/admin/products.php" class="nav-link px-0 align-middle text-white <?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>">
                    <i class="bi bi-box"></i> <span class="ms-1 d-none d-sm-inline">Produtos</span>
                </a>
            </li>
            <li class="w-100">
                <a href="<?php echo BASE_URL; ?>/admin/sales.php" class="nav-link px-0 align-middle text-white <?php echo basename($_SERVER['PHP_SELF']) == 'sales.php' ? 'active' : ''; ?>">
                    <i class="bi bi-cart"></i> <span class="ms-1 d-none d-sm-inline">Vendas</span>
                </a>
            </li>
            <li class="w-100">
                <a href="<?php echo BASE_URL; ?>/admin/reports.php" class="nav-link px-0 align-middle text-white <?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : ''; ?>">
                    <i class="bi bi-graph-up"></i> <span class="ms-1 d-none d-sm-inline">Relatórios</span>
                </a>
            </li>
        </ul>
    </div>
</div>
