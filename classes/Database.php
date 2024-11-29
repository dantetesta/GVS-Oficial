<?php
class Database extends PDO {
    private static $instance = null;

    public function __construct() {
        if (!defined('DB_HOST')) {
            require_once __DIR__ . '/../config/config.php';
        }

        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        
        try {
            parent::__construct($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
        } catch (PDOException $e) {
            die("Erro de conexão: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function executeQuery($sql, $params = []) {
        try {
            $stmt = $this->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            die("Erro na query: " . $e->getMessage());
        }
    }

    public function insert($table, $data) {
        $fields = implode(', ', array_keys($data));
        $values = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO {$table} ({$fields}) VALUES ({$values})";
        
        try {
            $stmt = $this->prepare($sql);
            $stmt->execute(array_values($data));
            return $this->lastInsertId();
        } catch (PDOException $e) {
            die("Erro ao inserir: " . $e->getMessage());
        }
    }

    public function update($table, $data, $where, $whereParams = []) {
        $sets = array_map(function($field) {
            return "{$field} = ?";
        }, array_keys($data));
        
        $sql = "UPDATE {$table} SET " . implode(', ', $sets) . " WHERE {$where}";
        
        try {
            $stmt = $this->prepare($sql);
            $stmt->execute(array_merge(array_values($data), $whereParams));
            return $stmt->rowCount();
        } catch (PDOException $e) {
            die("Erro ao atualizar: " . $e->getMessage());
        }
    }

    public function delete($table, $where, $params = []) {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        
        try {
            $stmt = $this->prepare($sql);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            die("Erro ao deletar: " . $e->getMessage());
        }
    }
}
