<?php
class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function login($username, $password) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username");
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['is_admin'] = $user['is_admin'];
                $_SESSION['full_name'] = $user['full_name'];
                return true;
            }
            return false;
        } catch(PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            return false;
        }
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public function isAdmin() {
        return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
    }

    public function getCurrentUserId() {
        return $_SESSION['user_id'] ?? null;
    }

    public function logout() {
        session_destroy();
        session_start();
    }

    public function resetPassword($email) {
        try {
            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);
            
            if ($stmt->rowCount() > 0) {
                $token = bin2hex(random_bytes(32));
                $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
                
                $stmt = $this->db->prepare("UPDATE users SET reset_token = :token, reset_expiry = :expiry WHERE email = :email");
                $stmt->execute([
                    'token' => $token,
                    'expiry' => $expiry,
                    'email' => $email
                ]);
                
                return $token;
            }
            return false;
        } catch(PDOException $e) {
            error_log("Password reset error: " . $e->getMessage());
            return false;
        }
    }

    public function getAllUsers() {
        try {
            $stmt = $this->db->query("SELECT id, username, email, full_name, is_admin, created_at FROM users ORDER BY created_at DESC");
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            error_log("Get users error: " . $e->getMessage());
            return [];
        }
    }

    public function createUser($data) {
        try {
            // Verificar se username ou email já existem
            $stmt = $this->db->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
            $stmt->execute([
                'username' => $data['username'],
                'email' => $data['email']
            ]);
            
            if ($stmt->rowCount() > 0) {
                return ['success' => false, 'message' => 'Usuário ou email já existem.'];
            }

            $stmt = $this->db->prepare("
                INSERT INTO users (username, password, email, full_name, is_admin) 
                VALUES (:username, :password, :email, :full_name, :is_admin)
            ");
            
            $result = $stmt->execute([
                'username' => $data['username'],
                'password' => password_hash($data['password'], PASSWORD_DEFAULT),
                'email' => $data['email'],
                'full_name' => $data['full_name'],
                'is_admin' => isset($data['is_admin']) ? 1 : 0
            ]);

            return ['success' => $result, 'message' => $result ? 'Usuário criado com sucesso!' : 'Erro ao criar usuário.'];
        } catch(PDOException $e) {
            error_log("Create user error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erro ao criar usuário.'];
        }
    }

    public function updateUser($id, $data) {
        try {
            // Não permitir alterar o próprio status de admin
            if ($id == $this->getCurrentUserId() && isset($data['is_admin'])) {
                unset($data['is_admin']);
            }

            // Verificar se username ou email já existem (exceto para o usuário atual)
            $stmt = $this->db->prepare("
                SELECT id FROM users 
                WHERE (username = :username OR email = :email) 
                AND id != :id
            ");
            $stmt->execute([
                'username' => $data['username'],
                'email' => $data['email'],
                'id' => $id
            ]);
            
            if ($stmt->rowCount() > 0) {
                return ['success' => false, 'message' => 'Usuário ou email já existem.'];
            }

            $sql = "UPDATE users SET 
                    username = :username,
                    email = :email,
                    full_name = :full_name";
            
            if (!empty($data['password'])) {
                $sql .= ", password = :password";
            }
            
            if (isset($data['is_admin'])) {
                $sql .= ", is_admin = :is_admin";
            }
            
            $sql .= " WHERE id = :id";
            
            $stmt = $this->db->prepare($sql);
            
            $params = [
                'username' => $data['username'],
                'email' => $data['email'],
                'full_name' => $data['full_name'],
                'id' => $id
            ];
            
            if (!empty($data['password'])) {
                $params['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }
            
            if (isset($data['is_admin'])) {
                $params['is_admin'] = $data['is_admin'] ? 1 : 0;
            }
            
            $result = $stmt->execute($params);
            return ['success' => $result, 'message' => $result ? 'Usuário atualizado com sucesso!' : 'Erro ao atualizar usuário.'];
        } catch(PDOException $e) {
            error_log("Update user error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erro ao atualizar usuário.'];
        }
    }

    public function deleteUser($id) {
        try {
            // Não permitir excluir o próprio usuário
            if ($id == $this->getCurrentUserId()) {
                return ['success' => false, 'message' => 'Não é possível excluir o usuário atual.'];
            }

            $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
            $result = $stmt->execute(['id' => $id]);
            return ['success' => $result, 'message' => $result ? 'Usuário excluído com sucesso!' : 'Erro ao excluir usuário.'];
        } catch(PDOException $e) {
            error_log("Delete user error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erro ao excluir usuário.'];
        }
    }

    public function getUserById($id) {
        try {
            $stmt = $this->db->prepare("SELECT id, username, email, full_name, is_admin FROM users WHERE id = :id");
            $stmt->execute(['id' => $id]);
            return $stmt->fetch();
        } catch(PDOException $e) {
            error_log("Get user error: " . $e->getMessage());
            return false;
        }
    }

    public function validateResetToken($token) {
        try {
            $stmt = $this->db->prepare("
                SELECT id FROM users 
                WHERE reset_token = :token 
                AND reset_expiry > NOW()
            ");
            $stmt->execute(['token' => $token]);
            return $stmt->fetch();
        } catch(PDOException $e) {
            error_log("Token validation error: " . $e->getMessage());
            return false;
        }
    }

    public function updatePassword($userId, $newPassword) {
        try {
            $stmt = $this->db->prepare("
                UPDATE users 
                SET password = :password,
                    reset_token = NULL,
                    reset_expiry = NULL
                WHERE id = :id
            ");
            return $stmt->execute([
                'password' => password_hash($newPassword, PASSWORD_DEFAULT),
                'id' => $userId
            ]);
        } catch(PDOException $e) {
            error_log("Password update error: " . $e->getMessage());
            return false;
        }
    }
}
