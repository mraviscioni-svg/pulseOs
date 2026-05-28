<?php

declare(strict_types=1);

/**
 * Ejecuta migraciones SQL pendientes.
 *
 * Uso:
 *   php scripts/migrate.php              # todas las pendientes
 *   php scripts/migrate.php --status     # listar estado
 *   php scripts/migrate.php --only=005_business_type_catalog.sql
 *   php scripts/migrate.php --mark=001_initial_schema.sql  # marcar sin ejecutar
 */

require_once __DIR__ . '/MigrationRunner.php';

$args = array_slice($argv, 1);
$statusOnly = in_array('--status', $args, true);
$only = null;
$mark = null;

foreach ($args as $arg) {
    if (str_starts_with($arg, '--only=')) {
        $only = substr($arg, 7);
    }
    if (str_starts_with($arg, '--mark=')) {
        $mark = substr($arg, 7);
    }
}

try {
    $runner = new MigrationRunner();

    if ($statusOnly) {
        echo "=== Migraciones PulseOS ===\n\n";
        echo "Aplicadas:\n";
        foreach ($runner->applied() as $m) {
            echo "  [x] {$m}\n";
        }
        echo "\nPendientes:\n";
        $pending = $runner->pending();
        if ($pending === []) {
            echo "  (ninguna)\n";
        } else {
            foreach ($pending as $m) {
                echo "  [ ] {$m}\n";
            }
        }
        exit(0);
    }

    if ($mark !== null) {
        $runner->markApplied($mark);
        echo "Marcada como aplicada: {$mark}\n";
        exit(0);
    }

    $result = $runner->run($only);

    foreach ($result['applied'] as $m) {
        echo "[OK] Aplicada: {$m}\n";
    }
    foreach ($result['skipped'] as $m) {
        echo "[SKIP] {$m}\n";
    }
    foreach ($result['errors'] as $err) {
        fwrite(STDERR, "[ERROR] {$err}\n");
    }

    exit($result['errors'] === [] ? 0 : 1);
} catch (Throwable $e) {
    fwrite(STDERR, 'Error: ' . $e->getMessage() . "\n");
    fwrite(STDERR, "Tip: copiá .env.example → .env con PROD_DB_* (mismos secrets de GitHub).\n");
    exit(1);
}
