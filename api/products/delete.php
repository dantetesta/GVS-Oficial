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
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID do produto não fornecido']);
    exit();
}

try {
    $db = new Database();
    
    // Preparar e executar a query
    $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
    $result = $stmt->execute([$data['id']]);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Produto excluído com sucesso']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao excluir produto']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao excluir produto: ' . $e->getMessage()]);
}
