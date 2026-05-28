<?php

/**
 * Prueba conexión MySQL y FTP usando .env local.
 * Uso: php scripts/verify-connections.php
 */

declare(strict_types=1);

require_once __DIR__ . '/bootstrap-env.php';

$ok = true;

echo "=== PulseOS — verificación de conexiones ===\n\n";

echo "MySQL...\n";
try {
    $cfg = scripts_db_config();
    if ($cfg['database'] === '' || $cfg['user'] === '') {
        echo "  [SKIP] Faltan PROD_DB_* o DB_* en .env\n";
        echo "  Tip: .\\scripts\\setup-local-env.ps1\n";
        $ok = false;
    } else {
        $pdo = scripts_pdo();
        $version = $pdo->query('SELECT VERSION()')->fetchColumn();
        $tables = (int) $pdo->query(
            'SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE()'
        )->fetchColumn();
        echo "  [OK] Conectado. MySQL {$version}. Tablas: {$tables}\n";

        require_once __DIR__ . '/MigrationRunner.php';
        $runner = new MigrationRunner($pdo);
        $pending = $runner->pending();
        if ($pending === []) {
            echo "  [OK] Migraciones: al día\n";
        } else {
            echo '  [INFO] Migraciones pendientes: ' . implode(', ', $pending) . "\n";
            echo "  Ejecutá: php scripts/migrate.php\n";
        }
    }
} catch (Throwable $e) {
    echo '  [FAIL] ' . $e->getMessage() . "\n";
    $ok = false;
}

echo "\nFTP...\n";
scripts_load_env();
$ftpServer = $_ENV['FTP_SERVER'] ?? '';
$ftpUser = $_ENV['FTP_USERNAME'] ?? '';
$ftpPass = $_ENV['FTP_PASSWORD'] ?? '';

if ($ftpServer === '' || $ftpUser === '') {
    echo "  [SKIP] Faltan FTP_* en .env\n";
} else {
    $conn = @ftp_connect($ftpServer, 21, 15);
    if (!$conn || !@ftp_login($conn, $ftpUser, $ftpPass)) {
        echo "  [FAIL] No se pudo conectar al FTP\n";
        $ok = false;
    } else {
        echo "  [OK] FTP conectado\n";
        ftp_close($conn);
    }
}

echo "\n" . ($ok ? "Todo OK.\n" : "Revisá los errores arriba.\n");
exit($ok ? 0 : 1);
