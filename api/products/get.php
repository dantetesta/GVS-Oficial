<?php
require_once '../../config/config.php';
require_once '../../classes/Database.php';
require_once '../../classes/User.php';

header('Content-Type: application/json');

// Verificar se o usuário está logado e é admin
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    echo json_encode(['success' => false, 'message' => 'Acesso não autorizado']);
    exit();
}

// Receber ID do produto
if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID do produto não fornecido']);
    exit();
}

try {
    $db = new Database();
    
    // Preparar e executar a query
    $stmt = $db->prepare("SELECT id, name, description, price, stock FROM products WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        echo json_encode(['success' => true, 'product' => $product]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Produto não encontrado']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao buscar produto: ' . $e->getMessage()]);
}
