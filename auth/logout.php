<?php
require_once '../config/config.php';
require_once '../includes/Database.php';
require_once '../includes/User.php';

$user = new User();
$user->logout();

header('Location: ../index.php');
exit();
