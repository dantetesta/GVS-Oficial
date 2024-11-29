<?php
require_once 'config.php';
require_once '../includes/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    // Primeiro, vamos limpar qualquer usuário admin existente
    $stmt = $db->prepare("DELETE FROM users WHERE username = 'admin'");
    $stmt->execute();
    
    // Agora vamos criar o novo usuário admin com a senha hasheada corretamente
    $password = 'admin@123';
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $db->prepare("
        INSERT INTO users (username, password, email, full_name, is_admin) 
        VALUES (:username, :password, :email, :full_name, :is_admin)
    ");
    
    $result = $stmt->execute([
        'username' => 'admin',
        'password' => $hashedPassword,
        'email' => 'admin@example.com',
        'full_name' => 'Administrator',
        'is_admin' => 1
    ]);
    
    if ($result) {
        echo "Usuário admin criado com sucesso!\n";
        echo "Username: admin\n";
        echo "Password: admin@123\n";
    } else {
        echo "Erro ao criar usuário admin.\n";
    }
    
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
