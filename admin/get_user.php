<?php
require_once '../config/config.php';
require_once '../includes/Database.php';
require_once '../includes/User.php';

$user = new User();

if (!$user->isLoggedIn()) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Não autorizado']);
    exit();
}

if (!isset($_GET['id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'ID do usuário não fornecido']);
    exit();
}

// Verificar se o usuário está tentando editar seu próprio perfil ou se é admin
if ($_GET['id'] != $user->getCurrentUserId() && !$user->isAdmin()) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Não autorizado']);
    exit();
}

$userData = $user->getUserById($_GET['id']);

if ($userData) {
    // Remover campos sensíveis
    unset($userData['password']);
    
    header('Content-Type: application/json');
    echo json_encode($userData);
} else {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Usuário não encontrado']);
}
