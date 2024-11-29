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

// Buscar estatísticas
$stmt = $db->prepare("SELECT 
    (SELECT COUNT(*) FROM sales) as total_sales,
    (SELECT COUNT(*) FROM products) as total_products,
    (SELECT COUNT(*) FROM users) as total_users,
    (SELECT SUM(total_amount) FROM sales) as total_revenue
");
$stmt->execute();
$stats = $stmt->fetch(PDO::FETCH_ASSOC);

// Buscar vendas por mês
$stmt = $db->prepare("SELECT 
    DATE_FORMAT(created_at, '%Y-%m') as month,
    COUNT(*) as total_sales,
    SUM(total_amount) as revenue
    FROM sales 
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY month DESC
");
$stmt->execute();
$monthlySales = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar produtos mais vendidos
$stmt = $db->prepare("SELECT 
    p.name,
    COUNT(*) as total_sold,
    SUM(si.quantity) as units_sold,
    SUM(si.price * si.quantity) as revenue
    FROM sales_items si
    JOIN products p ON p.id = si.product_id
    GROUP BY si.product_id
    ORDER BY units_sold DESC
    LIMIT 10
");
$stmt->execute();
$topProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatórios - <?php echo APP_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                    <h1 class="h3 mb-0 text-gray-800">Relatórios e Estatísticas</h1>
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary" onclick="exportToPDF()">
                            <i class="bi bi-file-pdf me-1"></i> Exportar PDF
                        </button>
                        <button type="button" class="btn btn-success" onclick="exportToExcel()">
                            <i class="bi bi-file-excel me-1"></i> Exportar Excel
                        </button>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-left-primary h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Total de Vendas</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php echo number_format($stats['total_sales']); ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-cart fs-2 text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card border-left-success h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Receita Total</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            R$ <?php echo number_format($stats['total_revenue'], 2, ',', '.'); ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-currency-dollar fs-2 text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card border-left-info h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            Total de Produtos</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php echo number_format($stats['total_products']); ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-box fs-2 text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card border-left-warning h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Total de Usuários</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php echo number_format($stats['total_users']); ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-people fs-2 text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="row mb-4">
                    <!-- Monthly Sales Chart -->
                    <div class="col-xl-8">
                        <div class="card">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Vendas Mensais</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="monthlySalesChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Top Products Chart -->
                    <div class="col-xl-4">
                        <div class="card">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Produtos Mais Vendidos</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="topProductsChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Products Table -->
                <div class="card mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Detalhamento de Produtos Mais Vendidos</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Produto</th>
                                        <th>Vendas</th>
                                        <th>Unidades Vendidas</th>
                                        <th>Receita</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($topProducts as $product): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                                        <td><?php echo number_format($product['total_sold']); ?></td>
                                        <td><?php echo number_format($product['units_sold']); ?></td>
                                        <td>R$ <?php echo number_format($product['revenue'], 2, ',', '.'); ?></td>
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

    // Monthly Sales Chart
    const monthlySalesCtx = document.getElementById('monthlySalesChart').getContext('2d');
    new Chart(monthlySalesCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode(array_column(array_reverse($monthlySales), 'month')); ?>,
            datasets: [{
                label: 'Vendas',
                data: <?php echo json_encode(array_column(array_reverse($monthlySales), 'total_sales')); ?>,
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }, {
                label: 'Receita (R$)',
                data: <?php echo json_encode(array_column(array_reverse($monthlySales), 'revenue')); ?>,
                borderColor: 'rgb(255, 99, 132)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Top Products Chart
    const topProductsCtx = document.getElementById('topProductsChart').getContext('2d');
    new Chart(topProductsCtx, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode(array_column($topProducts, 'name')); ?>,
            datasets: [{
                data: <?php echo json_encode(array_column($topProducts, 'units_sold')); ?>,
                backgroundColor: [
                    'rgb(255, 99, 132)',
                    'rgb(54, 162, 235)',
                    'rgb(255, 206, 86)',
                    'rgb(75, 192, 192)',
                    'rgb(153, 102, 255)'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});

// Export functions
function exportToPDF() {
    // Implementar exportação para PDF
    alert('Exportação para PDF será implementada em breve!');
}

function exportToExcel() {
    // Implementar exportação para Excel
    alert('Exportação para Excel será implementada em breve!');
}

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
