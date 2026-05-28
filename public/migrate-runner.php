<?php

declare(strict_types=1);

/**
 * Ejecuta migraciones en el servidor (localhost MySQL).
 * Protegido con MIGRATION_SECRET en .env / pulseos.env.
 *
 * CI: POST con header X-Migration-Token
 */

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Use POST']);
    exit;
}

$root = dirname(__DIR__);
require $root . '/vendor/autoload.php';

foreach ([$root . '/.env', $root . '/pulseos.env'] as $file) {
    if (is_readable($file)) {
        Dotenv\Dotenv::createImmutable($root, basename($file))->safeLoad();
        break;
    }
}

$expected = (string) ($_ENV['MIGRATION_SECRET'] ?? '');
$token = (string) ($_SERVER['HTTP_X_MIGRATION_TOKEN'] ?? $_POST['token'] ?? '');

if ($expected === '' || $token === '' || !hash_equals($expected, $token)) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'Forbidden']);
    exit;
}

require_once $root . '/scripts/MigrationRunner.php';

try {
    $runner = new MigrationRunner();
    $result = $runner->run();
    echo json_encode([
        'ok' => $result['errors'] === [],
        'applied' => $result['applied'],
        'skipped' => $result['skipped'],
        'errors' => $result['errors'],
        'pending_after' => $runner->pending(),
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
