<?php

/**
 * Genera .env y pulseos.env (sin depender de echo en bash; soporta claves con $, !, etc.)
 * Uso en CI: variables PROD_DB_* en el entorno.
 */

declare(strict_types=1);

function env_line(string $key, string $value): string
{
    if ($value === '' || preg_match('/[\s#"\'\\\\]/', $value)) {
        return $key . '="' . str_replace(['\\', '"'], ['\\\\', '\\"'], $value) . '"';
    }

    return $key . '=' . $value;
}

function normalize_db_host(string $host): string
{
    $host = trim($host);
    if ($host === '' || preg_match('/:2083/i', $host) || preg_match('/cpanel/i', $host)) {
        return 'localhost';
    }

    return $host;
}

$root = dirname(__DIR__);

$host = normalize_db_host((string) (getenv('PROD_DB_HOST') ?: 'localhost'));
$db = trim((string) (getenv('PROD_DB_NAME') ?: ''));
$user = trim((string) (getenv('PROD_DB_USER') ?: ''));
$pass = (string) (getenv('PROD_DB_PASSWORD') ?: '');

if ($db === '' || $user === '') {
    fwrite(STDERR, "PROD_DB_NAME y PROD_DB_USER son obligatorios.\n");
    exit(1);
}

$remote = getenv('REMOTE_SUBDIR') ?: 'PulseOS-prep';
$appDebug = $remote === 'pulseOS' ? 'false' : 'true';
$appUrl = getenv('PROD_APP_URL') ?: (
    $remote === 'pulseOS'
        ? 'https://tallerboedo.com.ar/pulseOS/public'
        : 'https://tallerboedo.com.ar/PulseOS-prep/public'
);

$migrationSecret = trim((string) (getenv('MIGRATION_SECRET') ?: ''));

$lines = [
    env_line('APP_NAME', 'PulseOS'),
    env_line('APP_ENV', 'production'),
    env_line('APP_DEBUG', $appDebug),
    env_line('APP_URL', $appUrl),
    env_line('APP_KEY', ''),
    env_line('MIGRATION_SECRET', $migrationSecret),
    env_line('DB_HOST', $host),
    env_line('DB_PORT', getenv('PROD_DB_PORT') ?: '3306'),
    env_line('DB_DATABASE', $db),
    env_line('DB_USERNAME', $user),
    env_line('DB_PASSWORD', $pass),
    env_line('DB_CHARSET', 'utf8mb4'),
    env_line('SESSION_LIFETIME', '7200'),
];

$content = implode("\n", $lines) . "\n";

file_put_contents($root . '/.env', $content);
file_put_contents($root . '/pulseos.env', $content);

echo "OK: .env y pulseos.env generados (DB_HOST={$host}, DB_DATABASE={$db}, DB_USERNAME={$user})\n";
