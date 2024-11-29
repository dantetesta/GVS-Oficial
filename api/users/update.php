<?php
require_once '../../config/config.php';
require_once '../../classes/Database.php';
require_once '../../classes/User.php';

header('Content-Type: application/json');

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

// Verificar se o usuário está logado e é admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Não autorizado']);
    exit;
}

try {
    // Obter dados do POST
    $userId = $_POST['id'] ?? null;
    $fullName = $_POST['full_name'] ?? null;
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;
    $isAdmin = isset($_POST['is_admin']) ? (int)$_POST['is_admin'] : 0;

    // Validar dados obrigatórios
    if (empty($userId) || empty($fullName) || empty($email)) {
        throw new Exception('Dados obrigatórios não fornecidos');
    }

    // Verificar se o usuário existe
    $db = new Database();
    $user = new User($db);
    $existingUser = $user->getUserById($userId);

    if (!$existingUser) {
        throw new Exception('Usuário não encontrado');
    }

    // Preparar dados para atualização
    $data = [
        'full_name' => $fullName,
        'email' => $email,
        'is_admin' => $isAdmin
    ];

    // Adicionar senha apenas se foi fornecida
    if (!empty($password)) {
        $data['password'] = password_hash($password, PASSWORD_DEFAULT);
    }

    // Atualizar usuário
    $stmt = $db->prepare("
        UPDATE users 
        SET full_name = :full_name,
            email = :email,
            " . (!empty($password) ? "password = :password," : "") . "
            is_admin = :is_admin,
            updated_at = NOW()
        WHERE id = :id
    ");

    $stmt->bindParam(':full_name', $data['full_name']);
    $stmt->bindParam(':email', $data['email']);
    $stmt->bindParam(':is_admin', $data['is_admin']);
    $stmt->bindParam(':id', $userId);
    
    if (!empty($password)) {
        $stmt->bindParam(':password', $data['password']);
    }

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Usuário atualizado com sucesso'
        ]);
    } else {
        throw new Exception('Erro ao atualizar usuário');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
