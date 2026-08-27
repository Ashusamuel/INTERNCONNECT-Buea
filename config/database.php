<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'internconnect');
define('DB_USER', 'root');
define('DB_PASS', '');

// Site-wide settings
define('SITE_NAME', 'InternConnect Buea');

// Automatically detect BASE_URL so CSS, links, and forms load on any XAMPP folder name
if (!defined('BASE_URL')) {
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    $scriptDir = rtrim($scriptDir, '/');

    foreach (['/student', '/organization', '/admin', '/includes', '/config'] as $subFolder) {
        if (substr($scriptDir, -strlen($subFolder)) === $subFolder) {
            $scriptDir = substr($scriptDir, 0, -strlen($subFolder));
            break;
        }
    }
    define('BASE_URL', $scriptDir);
}

try {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    die('Database connection failed: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}
