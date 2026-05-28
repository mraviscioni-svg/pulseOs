<?php

/**
 * Prueba conexión MySQL y FTP usando .env local.
 * Uso: php scripts/verify-connections.php
 */

declare(strict_types=1);

$root = dirname(__DIR__);
require $root . '/vendor/autoload.php';

if (file_exists($root . '/.env')) {
    Dotenv\Dotenv::createImmutable($root)->safeLoad();
}

$ok = true;

echo "=== PulseOS — verificación de conexiones ===\n\n";

// MySQL (GitHub: PROD_DB_* — app: DB_* o PROD_DB_*)
echo "MySQL...\n";
$host = $_ENV['DB_HOST'] ?? $_ENV['PROD_DB_HOST'] ?? $_ENV['PREP_DB_HOST'] ?? '';
$db = $_ENV['DB_DATABASE'] ?? $_ENV['PROD_DB_NAME'] ?? $_ENV['PREP_DB_NAME'] ?? '';
$user = $_ENV['DB_USERNAME'] ?? $_ENV['PROD_DB_USER'] ?? $_ENV['PREP_DB_USER'] ?? '';
$pass = $_ENV['DB_PASSWORD'] ?? $_ENV['PROD_DB_PASSWORD'] ?? $_ENV['PREP_DB_PASSWORD'] ?? '';
$port = (int) ($_ENV['DB_PORT'] ?? 3306);

if ($host === '' || $db === '' || $user === '') {
    echo "  [SKIP] Faltan PROD_DB_* o DB_* en .env\n";
    $ok = false;
} else {
    try {
        $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";
        $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        $version = $pdo->query('SELECT VERSION()')->fetchColumn();
        $tables = (int) $pdo->query('SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE()')->fetchColumn();
        echo "  [OK] Conectado. MySQL {$version}. Tablas en la base: {$tables}\n";
    } catch (Throwable $e) {
        echo "  [FAIL] " . $e->getMessage() . "\n";
        $ok = false;
    }
}

echo "\nFTP...\n";
$ftpServer = $_ENV['FTP_SERVER'] ?? '';
$ftpUser = $_ENV['FTP_USERNAME'] ?? '';
$ftpPass = $_ENV['FTP_PASSWORD'] ?? '';
$ftpDir = $_ENV['FTP_SERVER_DIR'] ?? './';

if ($ftpServer === '' || $ftpUser === '') {
    echo "  [SKIP] Faltan FTP_SERVER o FTP_USERNAME en .env\n";
    $ok = false;
} else {
    $conn = @ftp_connect($ftpServer, 21, 15);
    if (!$conn) {
        echo "  [FAIL] No se pudo conectar a {$ftpServer}:21 (¿FTPS? probá FTPS en el hosting)\n";
        $ok = false;
    } elseif (!@ftp_login($conn, $ftpUser, $ftpPass)) {
        echo "  [FAIL] Login FTP rechazado (usuario/clave)\n";
        $ok = false;
    } else {
        ftp_pasv($conn, true);
        $pwd = @ftp_pwd($conn);
        $target = rtrim($ftpDir, '/') . '/PulseOS-prep';
        $listed = @ftp_nlist($conn, $target) ?: @ftp_nlist($conn, '.');
        echo "  [OK] Login FTP. Directorio actual: {$pwd}\n";
        echo "  Carpeta prep esperada: {$target}/\n";
        echo "  Ítems visibles en listado: " . (is_array($listed) ? count($listed) : 0) . "\n";
        ftp_close($conn);
    }
}

echo "\n" . ($ok ? "Resultado: TODO OK\n" : "Resultado: HAY ERRORES — revisá .env o firewall del hosting\n");
exit($ok ? 0 : 1);
