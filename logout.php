<?php
require_once 'config/config.php';
require_once 'classes/Database.php';
require_once 'classes/User.php';

// Destruir todas as variáveis da sessão
$_SESSION = array();

// Destruir a sessão
session_destroy();

// Redirecionar para a página de login
header('Location: ' . BASE_URL . '/login.php');
exit();
