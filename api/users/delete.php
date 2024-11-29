<?php
require_once '../../config/config.php';
require_once '../../classes/Database.php';
require_once '../../classes/User.php';

header('Content-Type: application/json');

// Verificar se o usuário está logado e é admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    exit();
}

// Verificar se o ID foi fornecido
if (!isset($_POST['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID do usuário não fornecido']);
    exit();
}

try {
    $db = new Database();
    $user = new User($db);
    
    $userId = $_POST['id'];
    
    // Impedir que o usuário exclua a si mesmo
    if ($userId == $_SESSION['user_id']) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Você não pode excluir seu próprio usuário']);
        exit();
    }
    
    // Verificar se o usuário existe
    if (!$user->getUserById($userId)) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Usuário não encontrado']);
        exit();
    }
    
    // Excluir o usuário
    if ($user->deleteUser($userId)) {
        echo json_encode([
            'success' => true,
            'message' => 'Usuário excluído com sucesso'
        ]);
    } else {
        throw new Exception('Erro ao excluir usuário');
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao excluir usuário: ' . $e->getMessage()
    ]);
}
