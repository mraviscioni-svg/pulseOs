<?php

declare(strict_types=1);

$host = $_ENV['DB_HOST'] ?? $_ENV['PROD_DB_HOST'] ?? 'localhost';
$host = trim((string) $host);
if ($host === '' || preg_match('/:2083/i', $host) || preg_match('/cpanel/i', $host)) {
    $host = 'localhost';
}

return [
    'host' => $host,
    'port' => (int) ($_ENV['DB_PORT'] ?? 3306),
    'database' => $_ENV['DB_DATABASE'] ?? $_ENV['PROD_DB_NAME'] ?? 'pulseos',
    'username' => $_ENV['DB_USERNAME'] ?? $_ENV['PROD_DB_USER'] ?? 'root',
    'password' => $_ENV['DB_PASSWORD'] ?? $_ENV['PROD_DB_PASSWORD'] ?? '',
    'charset' => $_ENV['DB_CHARSET'] ?? 'utf8mb4',
    'socket' => $_ENV['DB_SOCKET'] ?? '',
];
