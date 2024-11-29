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
if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID do usuário não fornecido']);
    exit();
}

try {
    $db = new Database();
    $user = new User($db);
    
    $userData = $user->getUserById($_GET['id']);
    
    if ($userData) {
        echo json_encode([
            'success' => true,
            'data' => [
                'id' => $userData['id'],
                'username' => $userData['username'],
                'email' => $userData['email'],
                'full_name' => $userData['full_name'],
                'is_admin' => $userData['is_admin']
            ]
        ]);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Usuário não encontrado']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro ao buscar usuário: ' . $e->getMessage()]);
}
