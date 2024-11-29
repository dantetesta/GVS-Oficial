<?php
require_once '../../config/config.php';
require_once '../../classes/Database.php';
require_once '../../classes/Sale.php';

header('Content-Type: application/json');

// Verificar se o usuário está logado e é admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Não autorizado']);
    exit;
}

try {
    $db = new Database();
    $sale = new Sale($db);

    // Verificar se é uma busca por ID específico
    if (isset($_GET['id'])) {
        $saleData = $sale->getById($_GET['id']);
        if ($saleData) {
            echo json_encode([
                'success' => true,
                'data' => $saleData
            ]);
        } else {
            throw new Exception('Venda não encontrada');
        }
    } 
    // Listar vendas com filtros
    else {
        $page = $_GET['page'] ?? 1;
        $limit = $_GET['limit'] ?? 10;
        
        $filters = [
            'customer_id' => $_GET['customer_id'] ?? null,
            'status' => $_GET['status'] ?? null,
            'payment_method' => $_GET['payment_method'] ?? null,
            'date_start' => $_GET['date_start'] ?? null,
            'date_end' => $_GET['date_end'] ?? null,
            'min_value' => $_GET['min_value'] ?? null,
            'max_value' => $_GET['max_value'] ?? null
        ];

        $result = $sale->list($filters, $page, $limit);
        echo json_encode([
            'success' => true,
            'data' => $result['data'],
            'total' => $result['total'],
            'page' => $result['page'],
            'limit' => $result['limit'],
            'total_pages' => $result['total_pages']
        ]);
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
