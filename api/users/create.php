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

try {
    $db = new Database();
    $user = new User($db);
    
    // Preparar os dados do usuário
    $userData = [
        'username' => $_POST['username'] ?? '',
        'email' => $_POST['email'] ?? '',
        'password' => $_POST['password'] ?? '',
        'full_name' => $_POST['full_name'] ?? '',
        'is_admin' => isset($_POST['is_admin']) ? 1 : 0
    ];
    
    // Criar o usuário
    if ($user->createUser($userData)) {
        echo json_encode([
            'success' => true,
            'message' => 'Usuário criado com sucesso'
        ]);
    } else {
        throw new Exception('Erro ao criar usuário');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
