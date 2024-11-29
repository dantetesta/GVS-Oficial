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

// Verificar se os dados necessários foram fornecidos
if (!isset($_POST['id']) || !isset($_POST['username']) || !isset($_POST['email']) || !isset($_POST['full_name'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Dados incompletos']);
    exit();
}

try {
    $db = new Database();
    $user = new User($db);
    
    // Preparar os dados do usuário
    $userData = [
        'id' => $_POST['id'],
        'username' => $_POST['username'],
        'email' => $_POST['email'],
        'full_name' => $_POST['full_name'],
        'is_admin' => isset($_POST['is_admin']) ? 1 : 0
    ];
    
    // Adicionar senha apenas se fornecida
    if (!empty($_POST['password'])) {
        $userData['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
    }
    
    // Verificar se o email já existe para outro usuário
    $existingUser = $user->getUserByEmail($userData['email']);
    if ($existingUser && $existingUser['id'] != $userData['id']) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Este email já está em uso']);
        exit();
    }
    
    // Atualizar o usuário
    if ($user->updateUser($userData)) {
        echo json_encode([
            'success' => true,
            'message' => 'Usuário atualizado com sucesso'
        ]);
    } else {
        throw new Exception('Erro ao atualizar usuário');
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao atualizar usuário: ' . $e->getMessage()
    ]);
}
