<?php
require_once '../config/config.php';
require_once '../includes/Database.php';
require_once '../includes/User.php';
require_once '../includes/Settings.php';

$user = new User();
if (!$user->isLoggedIn() || !$user->isAdmin()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Acesso não autorizado']);
    exit();
}

$settings = new Settings();
$response = ['success' => false, 'message' => 'Tipo de formulário inválido'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formType = $_POST['form_type'] ?? '';
    
    switch ($formType) {
        case 'general':
            $data = [
                'company_name' => $_POST['company_name'] ?? '',
                'company_type' => $_POST['company_type'] ?? '',
                'document_number' => $_POST['document_number'] ?? '',
                'zip_code' => $_POST['zip_code'] ?? '',
                'address' => $_POST['address'] ?? '',
                'address_number' => $_POST['address_number'] ?? '',
                'complement' => $_POST['complement'] ?? '',
                'neighborhood' => $_POST['neighborhood'] ?? '',
                'city' => $_POST['city'] ?? '',
                'state' => $_POST['state'] ?? '',
                'website' => $_POST['website'] ?? '',
                'public_email' => $_POST['public_email'] ?? '',
                'whatsapp' => $_POST['whatsapp'] ?? '',
                'facebook' => $_POST['facebook'] ?? '',
                'instagram' => $_POST['instagram'] ?? '',
                'linkedin' => $_POST['linkedin'] ?? '',
                'tiktok' => $_POST['tiktok'] ?? ''
            ];
            $response = $settings->update($data);
            break;

        case 'logo':
            $response = ['success' => true, 'message' => 'Logotipos atualizados com sucesso', 'reload' => true];
            
            if (isset($_FILES['system_logo']) && $_FILES['system_logo']['error'] === 0) {
                $result = $settings->uploadLogo($_FILES['system_logo'], 'system');
                if (!$result['success']) {
                    $response = $result;
                    break;
                }
            }
            
            if (isset($_FILES['print_logo']) && $_FILES['print_logo']['error'] === 0) {
                $result = $settings->uploadLogo($_FILES['print_logo'], 'print');
                if (!$result['success']) {
                    $response = $result;
                    break;
                }
            }
            break;

        case 'smtp':
            if (isset($_POST['action']) && $_POST['action'] === 'test_smtp') {
                $smtpData = [
                    'smtp_host' => $_POST['smtp_host'],
                    'smtp_port' => $_POST['smtp_port'],
                    'smtp_secure' => $_POST['smtp_secure'],
                    'smtp_user' => $_POST['smtp_user'],
                    'smtp_pass' => $_POST['smtp_pass'],
                    'smtp_from' => $_POST['smtp_from'],
                    'smtp_from_name' => $_POST['smtp_from_name']
                ];
                $response = $settings->testSmtp($smtpData);
            } else {
                $data = [
                    'smtp_host' => $_POST['smtp_host'] ?? '',
                    'smtp_port' => $_POST['smtp_port'] ?? '',
                    'smtp_secure' => $_POST['smtp_secure'] ?? '',
                    'smtp_user' => $_POST['smtp_user'] ?? '',
                    'smtp_pass' => $_POST['smtp_pass'] ?? '',
                    'smtp_from' => $_POST['smtp_from'] ?? '',
                    'smtp_from_name' => $_POST['smtp_from_name'] ?? ''
                ];
                $response = $settings->update($data);
            }
            break;
    }
}

header('Content-Type: application/json');
echo json_encode($response);
