<?php
class Install {
    private $db = null;
    private $error = null;

    public function checkConnection() {
        try {
            $this->db = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
                DB_USER,
                DB_PASS,
                array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
            );
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return true;
        } catch(PDOException $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    public function checkTables() {
        try {
            $tables = ['users', 'products'];
            $existing_tables = [];
            
            foreach ($tables as $table) {
                $stmt = $this->db->prepare("SHOW TABLES LIKE ?");
                $stmt->execute([$table]);
                if ($stmt->rowCount() > 0) {
                    $existing_tables[] = $table;
                }
            }
            
            return count($tables) === count($existing_tables);
        } catch(PDOException $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    public function getError() {
        return $this->error;
    }

    public function installDatabase() {
        try {
            // Read SQL files
            $sqlFiles = [
                __DIR__ . '/../config/database.sql',
                __DIR__ . '/../config/products.sql'
            ];

            foreach ($sqlFiles as $file) {
                if (!file_exists($file)) {
                    throw new Exception("SQL file not found: " . $file);
                }

                $sql = file_get_contents($file);
                $statements = array_filter(array_map('trim', explode(';', $sql)));

                foreach ($statements as $statement) {
                    if (!empty($statement)) {
                        $this->db->exec($statement);
                    }
                }
            }

            return true;
        } catch(Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }
}
