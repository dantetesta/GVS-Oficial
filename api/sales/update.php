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
    // Obter dados do POST
    $saleId = $_POST['sale_id'] ?? null;
    $data = [
        'status' => $_POST['status'] ?? null,
        'payment_method' => $_POST['payment_method'] ?? null,
        'discount' => $_POST['discount'] ?? 0,
        'notes' => $_POST['notes'] ?? null
    ];

    // Validar dados obrigatórios
    if (empty($saleId) || empty($data['status']) || empty($data['payment_method'])) {
        throw new Exception('Dados obrigatórios não fornecidos');
    }

    // Atualizar venda
    $db = new Database();
    $sale = new Sale($db);
    $result = $sale->update($saleId, $data);

    if ($result['success']) {
        echo json_encode([
            'success' => true,
            'message' => 'Venda atualizada com sucesso'
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
