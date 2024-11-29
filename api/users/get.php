<?php
require_once '../../config/config.php';
require_once '../../classes/Database.php';
require_once '../../classes/User.php';

header('Content-Type: application/json');

// Verificar se o usuário está logado e é admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Não autorizado']);
    exit;
}

try {
    $db = new Database();
    $user = new User($db);

    // Verificar se é uma busca por ID específico
    if (isset($_GET['id'])) {
        $userData = $user->getUserById($_GET['id']);
        if ($userData) {
            // Remover senha do resultado
            unset($userData['password']);
            echo json_encode([
                'success' => true,
                'data' => $userData
            ]);
        } else {
            throw new Exception('Usuário não encontrado');
        }
    } else {
        throw new Exception('ID do usuário não fornecido');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
