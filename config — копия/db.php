
<?php
$cfg = [
    'host' => 'localhost',
    'user' => 'root',
    'pass' => '',
    'db' => 'demka',
    'charset' => 'utf8mb4'
];

$pdo = new PDO(
    "mysql:host={$cfg['host']};dbname={$cfg['db']};charset={$cfg['charset']}", 
    $cfg['user'], 
    $cfg['pass'], 
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);
