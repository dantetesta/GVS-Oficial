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

// Buscar todos os produtos
$stmt = $db->prepare("SELECT id, name, description, price, stock, created_at FROM products ORDER BY id DESC");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos - <?php echo APP_NAME; ?></title>
    
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
                    <h1 class="h3 mb-0 text-gray-800">Gerenciar Produtos</h1>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                        <i class="bi bi-plus-circle me-1"></i> Novo Produto
                    </button>
                </div>

                <!-- Products Table -->
                <div class="card table-card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nome</th>
                                        <th>Descrição</th>
                                        <th>Preço</th>
                                        <th>Estoque</th>
                                        <th>Criado em</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($products as $p): ?>
                                    <tr>
                                        <td><?php echo $p['id']; ?></td>
                                        <td><?php echo htmlspecialchars($p['name']); ?></td>
                                        <td><?php echo htmlspecialchars($p['description']); ?></td>
                                        <td>R$ <?php echo number_format($p['price'], 2, ',', '.'); ?></td>
                                        <td><?php echo $p['stock']; ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($p['created_at'])); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-primary edit-product" data-id="<?php echo $p['id']; ?>">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger delete-product" data-id="<?php echo $p['id']; ?>">
                                                <i class="bi bi-trash"></i>
                                            </button>
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

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProductModalLabel">Novo Produto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addProductForm">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nome do Produto</label>
                        <input type="text" class="form-control" id="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Descrição</label>
                        <textarea class="form-control" id="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Preço (R$)</label>
                        <input type="number" class="form-control" id="price" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="stock" class="form-label">Estoque</label>
                        <input type="number" class="form-control" id="stock" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="saveProduct">Salvar</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Product Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProductModalLabel">Editar Produto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editProductForm">
                    <input type="hidden" id="editProductId">
                    <div class="mb-3">
                        <label for="editName" class="form-label">Nome do Produto</label>
                        <input type="text" class="form-control" id="editName" required>
                    </div>
                    <div class="mb-3">
                        <label for="editDescription" class="form-label">Descrição</label>
                        <textarea class="form-control" id="editDescription" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="editPrice" class="form-label">Preço (R$)</label>
                        <input type="number" class="form-control" id="editPrice" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="editStock" class="form-label">Estoque</label>
                        <input type="number" class="form-control" id="editStock" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="updateProduct">Salvar Alterações</button>
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

    // Add Product
    document.getElementById('saveProduct').addEventListener('click', function() {
        const formData = {
            name: document.getElementById('name').value,
            description: document.getElementById('description').value,
            price: parseFloat(document.getElementById('price').value),
            stock: parseInt(document.getElementById('stock').value)
        };

        fetch('<?php echo BASE_URL; ?>/api/products/create.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Produto criado com sucesso!', 'success');
                $('#addProductModal').modal('hide');
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showAlert(data.message || 'Erro ao criar produto!', 'danger');
            }
        })
        .catch(error => {
            showAlert('Erro ao criar produto!', 'danger');
        });
    });

    // Edit Product
    document.querySelectorAll('.edit-product').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.id;
            fetch(`<?php echo BASE_URL; ?>/api/products/get.php?id=${productId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('editProductId').value = data.product.id;
                        document.getElementById('editName').value = data.product.name;
                        document.getElementById('editDescription').value = data.product.description;
                        document.getElementById('editPrice').value = data.product.price;
                        document.getElementById('editStock').value = data.product.stock;
                        $('#editProductModal').modal('show');
                    }
                });
        });
    });

    // Update Product
    document.getElementById('updateProduct').addEventListener('click', function() {
        const productId = document.getElementById('editProductId').value;
        const formData = {
            name: document.getElementById('editName').value,
            description: document.getElementById('editDescription').value,
            price: parseFloat(document.getElementById('editPrice').value),
            stock: parseInt(document.getElementById('editStock').value)
        };

        fetch(`<?php echo BASE_URL; ?>/api/products/update.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: productId, ...formData })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Produto atualizado com sucesso!', 'success');
                $('#editProductModal').modal('hide');
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showAlert(data.message || 'Erro ao atualizar produto!', 'danger');
            }
        })
        .catch(error => {
            showAlert('Erro ao atualizar produto!', 'danger');
        });
    });

    // Delete Product
    document.querySelectorAll('.delete-product').forEach(button => {
        button.addEventListener('click', function() {
            if (confirm('Tem certeza que deseja excluir este produto?')) {
                const productId = this.dataset.id;
                fetch(`<?php echo BASE_URL; ?>/api/products/delete.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id: productId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('Produto excluído com sucesso!', 'success');
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        showAlert(data.message || 'Erro ao excluir produto!', 'danger');
                    }
                })
                .catch(error => {
                    showAlert('Erro ao excluir produto!', 'danger');
                });
            }
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
