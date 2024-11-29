<?php
require_once '../../config/config.php';
require_once '../../classes/Database.php';
require_once '../../classes/Sale.php';

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
    // Obter ID da venda
    $saleId = $_POST['id'] ?? null;

    if (empty($saleId)) {
        throw new Exception('ID da venda não fornecido');
    }

    // Cancelar venda
    $db = new Database();
    $sale = new Sale($db);
    $result = $sale->cancel($saleId);

    if ($result['success']) {
        echo json_encode([
            'success' => true,
            'message' => 'Venda cancelada com sucesso'
        ]);
    } else {
        throw new Exception($result['message']);
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
