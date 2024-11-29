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
    $data = [
        'customer_id' => $_POST['customer_id'] ?? null,
        'products' => $_POST['products'] ?? [],
        'quantities' => $_POST['quantities'] ?? [],
        'prices' => $_POST['prices'] ?? [],
        'payment_method' => $_POST['payment_method'] ?? null,
        'discount' => $_POST['discount'] ?? 0,
        'notes' => $_POST['notes'] ?? null,
        'total' => 0
    ];

    // Validar dados obrigatórios
    if (empty($data['customer_id']) || empty($data['products']) || empty($data['payment_method'])) {
        throw new Exception('Dados obrigatórios não fornecidos');
    }

    // Calcular total
    for ($i = 0; $i < count($data['products']); $i++) {
        $data['total'] += $data['prices'][$i] * $data['quantities'][$i];
    }
    $data['total'] -= $data['discount'];

    // Criar venda
    $db = new Database();
    $sale = new Sale($db);
    $result = $sale->create($data);

    if ($result['success']) {
        echo json_encode([
            'success' => true,
            'message' => 'Venda criada com sucesso',
            'sale_id' => $result['sale_id']
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
