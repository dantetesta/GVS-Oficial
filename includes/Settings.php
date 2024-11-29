<?php
class Settings {
    private $db;
    private $settings = null;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->loadSettings();
    }

    private function loadSettings() {
        try {
            $stmt = $this->db->query("SELECT * FROM settings LIMIT 1");
            $this->settings = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error loading settings: " . $e->getMessage());
        }
    }

    public function get($key = null) {
        if ($key === null) {
            return $this->settings;
        }
        return $this->settings[$key] ?? null;
    }

    public function update($data) {
        try {
            // Se não existir configurações, criar
            if (!$this->settings) {
                $columns = implode(", ", array_keys($data));
                $values = ":" . implode(", :", array_keys($data));
                $sql = "INSERT INTO settings ($columns) VALUES ($values)";
            } else {
                $sql = "UPDATE settings SET ";
                $updates = [];
                foreach ($data as $key => $value) {
                    $updates[] = "$key = :$key";
                }
                $sql .= implode(", ", $updates);
                $sql .= " WHERE id = :id";
                $data['id'] = $this->settings['id'];
            }

            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute($data);

            if ($result) {
                $this->loadSettings(); // Recarregar configurações
                return ['success' => true, 'message' => 'Configurações atualizadas com sucesso!'];
            }
            return ['success' => false, 'message' => 'Erro ao atualizar configurações.'];
        } catch(PDOException $e) {
            error_log("Settings update error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erro ao atualizar configurações.'];
        }
    }

    public function uploadLogo($file, $type = 'system') {
        try {
            $targetDir = dirname(__DIR__) . "/assets/images/uploads/";
            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $fileName = $type . '_logo_' . time() . '.' . $fileExtension;
            $targetFile = $targetDir . $fileName;

            // Verificar extensão
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($fileExtension, $allowedExtensions)) {
                return ['success' => false, 'message' => 'Apenas arquivos JPG, JPEG, PNG e GIF são permitidos.'];
            }

            // Verificar tamanho (max 5MB)
            if ($file['size'] > 5000000) {
                return ['success' => false, 'message' => 'O arquivo deve ter no máximo 5MB.'];
            }

            // Mover arquivo
            if (move_uploaded_file($file['tmp_name'], $targetFile)) {
                // Atualizar no banco
                $column = $type . '_logo';
                $oldFile = $this->settings[$column] ?? null;
                
                $result = $this->update([$column => $fileName]);
                
                // Se sucesso e existia logo anterior, deletar
                if ($result['success'] && $oldFile && file_exists($targetDir . $oldFile)) {
                    unlink($targetDir . $oldFile);
                }
                
                return $result;
            }

            return ['success' => false, 'message' => 'Erro ao fazer upload do arquivo.'];
        } catch(Exception $e) {
            error_log("Logo upload error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erro ao fazer upload do logo.'];
        }
    }

    public function testSmtp($data = null) {
        if ($data === null) {
            $data = [
                'smtp_host' => $this->settings['smtp_host'],
                'smtp_port' => $this->settings['smtp_port'],
                'smtp_user' => $this->settings['smtp_user'],
                'smtp_pass' => $this->settings['smtp_pass'],
                'smtp_secure' => $this->settings['smtp_secure'],
                'smtp_from' => $this->settings['smtp_from'],
                'smtp_from_name' => $this->settings['smtp_from_name']
            ];
        }

        try {
            $mail = new PHPMailer(true);

            $mail->isSMTP();
            $mail->Host = $data['smtp_host'];
            $mail->SMTPAuth = true;
            $mail->Username = $data['smtp_user'];
            $mail->Password = $data['smtp_pass'];
            $mail->SMTPSecure = $data['smtp_secure'];
            $mail->Port = $data['smtp_port'];
            $mail->CharSet = 'UTF-8';

            $mail->setFrom($data['smtp_from'], $data['smtp_from_name']);
            $mail->addAddress($data['smtp_from']); // Enviar para o próprio email

            $mail->isHTML(true);
            $mail->Subject = 'Teste de Configuração SMTP';
            $mail->Body = 'Se você recebeu este email, suas configurações SMTP estão funcionando corretamente!';

            $mail->send();
            return ['success' => true, 'message' => 'Email de teste enviado com sucesso!'];
        } catch (Exception $e) {
            error_log("SMTP test error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erro ao enviar email de teste: ' . $mail->ErrorInfo];
        }
    }
}
