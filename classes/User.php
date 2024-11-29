<?php
class User {
    private $db;
    private $table = 'users';

    public function __construct($db) {
        $this->db = $db;
    }

    public function getUserById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function isAdmin() {
        if (!isset($_SESSION['user_id'])) return false;
        
        $user = $this->getUserById($_SESSION['user_id']);
        return $user && isset($user['is_admin']) && $user['is_admin'] == 1;
    }

    public function updateProfile($userId, $data) {
        $updates = [];
        $values = [];

        if (!empty($data['email'])) {
            $updates[] = "email = ?";
            $values[] = $data['email'];
        }

        if (!empty($data['password'])) {
            $updates[] = "password = ?";
            $values[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        if (empty($updates)) {
            return false;
        }

        $values[] = $userId;
        $sql = "UPDATE {$this->table} SET " . implode(', ', $updates) . " WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }

    public function authenticate($username, $password) {
        $sql = "SELECT * FROM {$this->table} WHERE username = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = $user['is_admin'];
            return true;
        }

        return false;
    }

    public function logout() {
        session_destroy();
        return true;
    }
}
