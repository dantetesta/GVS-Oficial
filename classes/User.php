<?php
class User {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getUserById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserByUsername($username) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllUsers() {
        $stmt = $this->db->prepare("SELECT id, username, email, full_name, is_admin, created_at, updated_at FROM users ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createUser($data) {
        // Validar campos obrigatórios
        if (empty($data['username']) || empty($data['email']) || empty($data['password']) || empty($data['full_name'])) {
            throw new Exception('Todos os campos são obrigatórios');
        }

        // Validar email
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Email inválido');
        }

        // Validar tamanho mínimo da senha
        if (strlen($data['password']) < 6) {
            throw new Exception('A senha deve ter no mínimo 6 caracteres');
        }

        // Validar nome de usuário
        if (strlen($data['username']) < 3) {
            throw new Exception('O nome de usuário deve ter no mínimo 3 caracteres');
        }

        // Verificar se username já existe
        if ($this->getUserByUsername($data['username'])) {
            throw new Exception('Este nome de usuário já está em uso');
        }

        // Verificar se email já existe
        if ($this->getUserByEmail($data['email'])) {
            throw new Exception('Este email já está em uso');
        }

        $stmt = $this->db->prepare("
            INSERT INTO users (username, email, password, full_name, is_admin, created_at, updated_at)
            VALUES (:username, :email, :password, :full_name, :is_admin, NOW(), NOW())
        ");
        
        return $stmt->execute([
            ':username' => trim($data['username']),
            ':email' => trim($data['email']),
            ':password' => password_hash($data['password'], PASSWORD_DEFAULT),
            ':full_name' => trim($data['full_name']),
            ':is_admin' => isset($data['is_admin']) ? 1 : 0
        ]);
    }

    public function updateUser($data) {
        $sql = "UPDATE users SET 
                username = :username,
                email = :email,
                full_name = :full_name,
                is_admin = :is_admin,
                updated_at = NOW()";
        
        if (isset($data['password'])) {
            $sql .= ", password = :password";
        }
        
        $sql .= " WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        
        $params = [
            ':id' => $data['id'],
            ':username' => $data['username'],
            ':email' => $data['email'],
            ':full_name' => $data['full_name'],
            ':is_admin' => $data['is_admin']
        ];
        
        if (isset($data['password'])) {
            $params[':password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        return $stmt->execute($params);
    }

    public function deleteUser($id) {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function validateLogin($username, $password) {
        $user = $this->getUserByUsername($username);
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }
}
