<?php

class Sale {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Criar uma nova venda
     */
    public function create($data) {
        try {
            $this->db->beginTransaction();

            // Inserir venda
            $stmt = $this->db->prepare("
                INSERT INTO sales (
                    customer_id, 
                    total_amount, 
                    discount, 
                    payment_method, 
                    status,
                    notes,
                    created_by
                ) VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $data['customer_id'],
                $data['total'],
                $data['discount'],
                $data['payment_method'],
                'pendente',
                $data['notes'],
                $_SESSION['user_id']
            ]);

            $saleId = $this->db->lastInsertId();

            // Inserir itens da venda
            $stmt = $this->db->prepare("
                INSERT INTO sales_items (
                    sale_id,
                    product_id,
                    quantity,
                    price
                ) VALUES (?, ?, ?, ?)
            ");

            foreach ($data['products'] as $index => $productId) {
                $stmt->execute([
                    $saleId,
                    $productId,
                    $data['quantities'][$index],
                    $data['prices'][$index]
                ]);

                // Atualizar estoque do produto
                $this->updateProductStock($productId, $data['quantities'][$index], 'decrease');
            }

            $this->db->commit();
            return ['success' => true, 'sale_id' => $saleId];
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Atualizar uma venda existente
     */
    public function update($saleId, $data) {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                UPDATE sales 
                SET status = ?,
                    payment_method = ?,
                    discount = ?,
                    notes = ?,
                    updated_at = NOW(),
                    updated_by = ?
                WHERE id = ?
            ");

            $stmt->execute([
                $data['status'],
                $data['payment_method'],
                $data['discount'],
                $data['notes'],
                $_SESSION['user_id'],
                $saleId
            ]);

            $this->db->commit();
            return ['success' => true];
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Buscar uma venda pelo ID
     */
    public function getById($saleId) {
        $stmt = $this->db->prepare("
            SELECT s.*, 
                   c.name as customer_name,
                   c.email as customer_email,
                   c.phone as customer_phone,
                   u.name as created_by_name
            FROM sales s
            LEFT JOIN customers c ON s.customer_id = c.id
            LEFT JOIN users u ON s.created_by = u.id
            WHERE s.id = ?
        ");
        
        $stmt->execute([$saleId]);
        $sale = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($sale) {
            // Buscar itens da venda
            $stmt = $this->db->prepare("
                SELECT si.*, p.name as product_name
                FROM sales_items si
                LEFT JOIN products p ON si.product_id = p.id
                WHERE si.sale_id = ?
            ");
            
            $stmt->execute([$saleId]);
            $sale['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $sale;
    }

    /**
     * Listar vendas com filtros e paginação
     */
    public function list($filters = [], $page = 1, $limit = 10) {
        $conditions = [];
        $params = [];
        $offset = ($page - 1) * $limit;

        // Construir query base
        $query = "
            SELECT s.*, 
                   c.name as customer_name,
                   u.name as created_by_name
            FROM sales s
            LEFT JOIN customers c ON s.customer_id = c.id
            LEFT JOIN users u ON s.created_by = u.id
            WHERE 1=1
        ";

        // Aplicar filtros
        if (!empty($filters['customer_id'])) {
            $conditions[] = "s.customer_id = ?";
            $params[] = $filters['customer_id'];
        }

        if (!empty($filters['status'])) {
            $conditions[] = "s.status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['payment_method'])) {
            $conditions[] = "s.payment_method = ?";
            $params[] = $filters['payment_method'];
        }

        if (!empty($filters['date_start'])) {
            $conditions[] = "DATE(s.created_at) >= ?";
            $params[] = $filters['date_start'];
        }

        if (!empty($filters['date_end'])) {
            $conditions[] = "DATE(s.created_at) <= ?";
            $params[] = $filters['date_end'];
        }

        if (!empty($filters['min_value'])) {
            $conditions[] = "s.total_amount >= ?";
            $params[] = $filters['min_value'];
        }

        if (!empty($filters['max_value'])) {
            $conditions[] = "s.total_amount <= ?";
            $params[] = $filters['max_value'];
        }

        // Adicionar condições à query
        if (!empty($conditions)) {
            $query .= " AND " . implode(" AND ", $conditions);
        }

        // Contar total de registros
        $countStmt = $this->db->prepare(str_replace("s.*, c.name", "COUNT(*)", $query));
        $countStmt->execute($params);
        $total = $countStmt->fetchColumn();

        // Adicionar ordenação e limite
        $query .= " ORDER BY s.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        // Executar query principal
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $sales = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'data' => $sales,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'total_pages' => ceil($total / $limit)
        ];
    }

    /**
     * Cancelar uma venda
     */
    public function cancel($saleId) {
        try {
            $this->db->beginTransaction();

            // Verificar se a venda existe e não está cancelada
            $stmt = $this->db->prepare("SELECT status FROM sales WHERE id = ?");
            $stmt->execute([$saleId]);
            $sale = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$sale) {
                throw new Exception("Venda não encontrada");
            }

            if ($sale['status'] === 'cancelado') {
                throw new Exception("Venda já está cancelada");
            }

            // Buscar itens da venda
            $stmt = $this->db->prepare("SELECT product_id, quantity FROM sales_items WHERE sale_id = ?");
            $stmt->execute([$saleId]);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Atualizar status da venda
            $stmt = $this->db->prepare("
                UPDATE sales 
                SET status = 'cancelado',
                    updated_at = NOW(),
                    updated_by = ?
                WHERE id = ?
            ");
            $stmt->execute([$_SESSION['user_id'], $saleId]);

            // Retornar produtos ao estoque
            foreach ($items as $item) {
                $this->updateProductStock($item['product_id'], $item['quantity'], 'increase');
            }

            $this->db->commit();
            return ['success' => true];
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Atualizar estoque do produto
     */
    private function updateProductStock($productId, $quantity, $operation = 'decrease') {
        $sql = "UPDATE products SET stock = stock " . 
               ($operation === 'decrease' ? "-" : "+") . 
               " ? WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$quantity, $productId]);
    }
}
