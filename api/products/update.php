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

// Receber dados do produto
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id']) || !isset($data['name']) || !isset($data['description']) || !isset($data['price']) || !isset($data['stock'])) {
    echo json_encode(['success' => false, 'message' => 'Dados incompletos']);
    exit();
}

try {
    $db = new Database();
    
    // Preparar e executar a query
    $stmt = $db->prepare("UPDATE products SET name = ?, description = ?, price = ?, stock = ? WHERE id = ?");
    
    $result = $stmt->execute([
        $data['name'],
        $data['description'],
        $data['price'],
        $data['stock'],
        $data['id']
    ]);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Produto atualizado com sucesso']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao atualizar produto']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao atualizar produto: ' . $e->getMessage()]);
}
