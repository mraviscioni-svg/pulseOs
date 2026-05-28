<?php

declare(strict_types=1);

return [
    'host' => $_ENV['DB_HOST'] ?? $_ENV['PROD_DB_HOST'] ?? '127.0.0.1',
    'port' => (int) ($_ENV['DB_PORT'] ?? 3306),
    'database' => $_ENV['DB_DATABASE'] ?? $_ENV['PROD_DB_NAME'] ?? 'pulseos',
    'username' => $_ENV['DB_USERNAME'] ?? $_ENV['PROD_DB_USER'] ?? 'root',
    'password' => $_ENV['DB_PASSWORD'] ?? $_ENV['PROD_DB_PASSWORD'] ?? '',
    'charset' => $_ENV['DB_CHARSET'] ?? 'utf8mb4',
];
