<?php

declare(strict_types=1);

/**
 * Carga .env / pulseos.env y expone conexión PDO para scripts CLI.
 */

function scripts_root(): string
{
    return dirname(__DIR__);
}

function scripts_load_env(): void
{
    static $loaded = false;
    if ($loaded) {
        return;
    }

    $root = scripts_root();
    require $root . '/vendor/autoload.php';

    foreach ([$root . '/.env', $root . '/pulseos.env'] as $file) {
        if (is_readable($file)) {
            Dotenv\Dotenv::createImmutable($root, basename($file))->safeLoad();
            break;
        }
    }

    $loaded = true;
}

/** @return array{host: string, port: int, database: string, user: string, password: string} */
function scripts_db_config(): array
{
    scripts_load_env();

    $host = trim((string) ($_ENV['DB_HOST'] ?? $_ENV['PROD_DB_HOST'] ?? ''));
    $database = trim((string) ($_ENV['DB_DATABASE'] ?? $_ENV['PROD_DB_NAME'] ?? ''));
    $user = trim((string) ($_ENV['DB_USERNAME'] ?? $_ENV['PROD_DB_USER'] ?? ''));
    $password = (string) ($_ENV['DB_PASSWORD'] ?? $_ENV['PROD_DB_PASSWORD'] ?? '');
    $port = (int) ($_ENV['DB_PORT'] ?? $_ENV['PROD_DB_PORT'] ?? 3306);

    if (preg_match('/:2083/i', $host) || preg_match('/cpanel/i', $host)) {
        $host = 'localhost';
    }

    return [
        'host' => $host,
        'port' => $port,
        'database' => $database,
        'user' => $user,
        'password' => $password,
    ];
}

function scripts_pdo(): PDO
{
    $cfg = scripts_db_config();

    if ($cfg['host'] === '' || $cfg['database'] === '' || $cfg['user'] === '') {
        throw new RuntimeException(
            'Faltan credenciales MySQL. Copiá .env.example → .env con PROD_DB_* (mismos valores que GitHub Secrets).'
        );
    }

    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
        $cfg['host'],
        $cfg['port'],
        $cfg['database']
    );

    return new PDO($dsn, $cfg['user'], $cfg['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
}
