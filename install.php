<?php
require_once 'config/config.php';
require_once 'includes/Install.php';

$install = new Install();
$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['install'])) {
        if ($install->checkConnection()) {
            if ($install->installDatabase()) {
                header('Location: index.php');
                exit();
            } else {
                $error = "Erro ao instalar o banco de dados: " . $install->getError();
            }
        } else {
            $error = "Erro de conexão com o banco de dados: " . $install->getError();
        }
    }
}

// Check if system is already installed
if ($install->checkConnection() && $install->checkTables()) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalação - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <h2 class="text-center mb-4">Instalação do Sistema</h2>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <?php if ($message): ?>
                            <div class="alert alert-success"><?php echo $message; ?></div>
                        <?php endif; ?>

                        <div class="text-center mb-4">
                            <i class="bi bi-gear-fill text-primary" style="font-size: 3rem;"></i>
                        </div>

                        <?php if (!$install->checkConnection()): ?>
                            <div class="alert alert-warning">
                                <h4 class="alert-heading"><i class="bi bi-exclamation-triangle me-2"></i>Conexão não configurada</h4>
                                <p>O sistema não conseguiu se conectar ao banco de dados. Verifique as configurações em config/config.php:</p>
                                <ul>
                                    <li>Host: <?php echo DB_HOST; ?></li>
                                    <li>Banco: <?php echo DB_NAME; ?></li>
                                    <li>Usuário: <?php echo DB_USER; ?></li>
                                </ul>
                            </div>
                        <?php elseif (!$install->checkTables()): ?>
                            <div class="alert alert-info">
                                <h4 class="alert-heading"><i class="bi bi-info-circle me-2"></i>Banco de dados não instalado</h4>
                                <p>A conexão com o banco de dados foi estabelecida, mas as tabelas necessárias não foram encontradas.</p>
                                <p>Clique no botão abaixo para criar as tabelas e instalar os dados iniciais.</p>
                            </div>
                            <form method="post" action="">
                                <button type="submit" name="install" class="btn btn-primary btn-lg w-100">
                                    <i class="bi bi-download me-2"></i>Instalar Sistema
                                </button>
                            </form>
                        <?php endif; ?>

                        <div class="text-center mt-4">
                            <p class="text-muted">Versão 1.0</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer mt-auto py-3 bg-dark text-white fixed-bottom">
        <div class="container text-center">
            <p class="mb-0">Desenvolvido por: <a href="https://www.dantetesta.com.br" class="text-white" target="_blank">Dante Testa</a></p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
