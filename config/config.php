<?php
// Database Configuration
define('DB_HOST', '187.33.241.61');
define('DB_USER', 'dantetesta_gvs');
define('DB_PASS', 'wH!ZtoEoTVB1');
define('DB_NAME', 'dantetesta_gvs');

// SMTP Configuration
define('SMTP_HOST', 'mail.dantetesta.com.br');
define('SMTP_USER', 'no-reply@dantetesta.com.br');
define('SMTP_PASS', 'ddtevy11@');
define('SMTP_PORT', 465);
define('SMTP_FROM_NAME', 'GVS');

// Application settings
define('APP_NAME', 'GVS');

// Define BASE_URL
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];
$baseDir = dirname(dirname($_SERVER['PHP_SELF']));
$baseDir = trim($baseDir, '/'); // Remove barras do início e fim
define('BASE_URL', $protocol . $host . ($baseDir ? '/' . $baseDir : ''));

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    // Session settings
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_start();
}
