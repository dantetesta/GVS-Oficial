<?php
class Product {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAllProducts() {
        try {
            $stmt = $this->db->query("SELECT * FROM products ORDER BY created_at DESC");
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            error_log("Get products error: " . $e->getMessage());
            return [];
        }
    }

    public function getProductById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM products WHERE id = :id");
            $stmt->execute(['id' => $id]);
            return $stmt->fetch();
        } catch(PDOException $e) {
            error_log("Get product error: " . $e->getMessage());
            return false;
        }
    }

    public function createProduct($data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO products (name, description, price, stock, sku, category, status) 
                VALUES (:name, :description, :price, :stock, :sku, :category, :status)
            ");
            
            return $stmt->execute([
                'name' => $data['name'],
                'description' => $data['description'],
                'price' => $data['price'],
                'stock' => $data['stock'],
                'sku' => $data['sku'],
                'category' => $data['category'],
                'status' => $data['status'] ?? 'active'
            ]);
        } catch(PDOException $e) {
            error_log("Create product error: " . $e->getMessage());
            return false;
        }
    }

    public function updateProduct($id, $data) {
        try {
            $stmt = $this->db->prepare("
                UPDATE products 
                SET name = :name,
                    description = :description,
                    price = :price,
                    stock = :stock,
                    sku = :sku,
                    category = :category,
                    status = :status
                WHERE id = :id
            ");
            
            return $stmt->execute([
                'name' => $data['name'],
                'description' => $data['description'],
                'price' => $data['price'],
                'stock' => $data['stock'],
                'sku' => $data['sku'],
                'category' => $data['category'],
                'status' => $data['status'],
                'id' => $id
            ]);
        } catch(PDOException $e) {
            error_log("Update product error: " . $e->getMessage());
            return false;
        }
    }

    public function deleteProduct($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM products WHERE id = :id");
            return $stmt->execute(['id' => $id]);
        } catch(PDOException $e) {
            error_log("Delete product error: " . $e->getMessage());
            return false;
        }
    }

    public function updateStock($id, $quantity) {
        try {
            $stmt = $this->db->prepare("
                UPDATE products 
                SET stock = stock + :quantity
                WHERE id = :id
            ");
            return $stmt->execute([
                'quantity' => $quantity,
                'id' => $id
            ]);
        } catch(PDOException $e) {
            error_log("Update stock error: " . $e->getMessage());
            return false;
        }
    }

    public function searchProducts($query) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM products 
                WHERE name LIKE :query 
                OR description LIKE :query 
                OR sku LIKE :query
                ORDER BY created_at DESC
            ");
            $searchTerm = "%{$query}%";
            $stmt->execute(['query' => $searchTerm]);
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            error_log("Search products error: " . $e->getMessage());
            return [];
        }
    }

    public function getProductsByCategory($category) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM products 
                WHERE category = :category
                ORDER BY name ASC
            ");
            $stmt->execute(['category' => $category]);
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            error_log("Get products by category error: " . $e->getMessage());
            return [];
        }
    }

    public function getAllCategories() {
        try {
            $stmt = $this->db->query("
                SELECT DISTINCT category 
                FROM products 
                WHERE category IS NOT NULL 
                ORDER BY category ASC
            ");
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch(PDOException $e) {
            error_log("Get categories error: " . $e->getMessage());
            return [];
        }
    }
}
