<?php

/**
 * Prueba de conexión MySQL — misma carga de credenciales que la app.
 * URL: .../PulseOS-prep/test.php (o .../public/../test.php según el hosting)
 * Borrar en producción cuando ya no haga falta.
 */

declare(strict_types=1);

header('Content-Type: text/plain; charset=utf-8');

$root = __DIR__;

if (!is_file($root . '/vendor/autoload.php')) {
    echo "ERROR: falta vendor/. Ejecutá composer install o deploy completo.\n";
    exit(1);
}

require $root . '/vendor/autoload.php';

$loaded = null;
foreach (['.env', 'pulseos.env'] as $envFile) {
    if (is_file($root . '/' . $envFile)) {
        Dotenv\Dotenv::createImmutable($root, $envFile)->safeLoad();
        $loaded = $envFile;
        break;
    }
}

echo "=== PulseOS test.php ===\n\n";
echo "Archivo env cargado: " . ($loaded ?? 'NINGUNO — creá .env o pulseos.env') . "\n\n";

if (!$loaded) {
    exit(1);
}

$config = require $root . '/config/database.php';

echo "DB_HOST: {$config['host']}\n";
echo "DB_DATABASE: {$config['database']}\n";
echo "DB_USERNAME: {$config['username']}\n";
echo "DB_PASSWORD: " . ($config['password'] !== '' ? '(definida, ' . strlen($config['password']) . ' caracteres)' : '(vacía)') . "\n\n";

try {
    $pdo = App\Core\Database::connect($config);
    $version = $pdo->query('SELECT VERSION()')->fetchColumn();
    $tables = (int) $pdo->query('SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE()')->fetchColumn();
    echo "CONEXION: OK\n";
    echo "MySQL: {$version}\n";
    echo "Tablas en la base: {$tables}\n";
} catch (Throwable $e) {
    echo "CONEXION: FALLO\n";
    echo $e->getMessage() . "\n";
    exit(1);
}
