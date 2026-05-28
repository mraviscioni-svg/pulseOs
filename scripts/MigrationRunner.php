<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap-env.php';

final class MigrationRunner
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? scripts_pdo();
    }

    public function ensureMigrationsTable(): void
    {
        $this->pdo->exec(
            'CREATE TABLE IF NOT EXISTS schema_migrations (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255) NOT NULL,
                applied_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY uq_schema_migrations_name (migration)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
        );
    }

    /** @return list<string> */
    public function applied(): array
    {
        $this->ensureMigrationsTable();
        $stmt = $this->pdo->query('SELECT migration FROM schema_migrations ORDER BY migration');

        return array_column($stmt->fetchAll(), 'migration');
    }

    /** @return list<string> */
    public function pending(): array
    {
        $all = $this->migrationFiles();
        $applied = $this->applied();

        return array_values(array_diff($all, $applied));
    }

    /** @return list<string> */
    public function migrationFiles(): array
    {
        $dir = scripts_root() . '/database/migrations';
        $files = glob($dir . '/*.sql') ?: [];
        sort($files, SORT_NATURAL);

        return array_map('basename', $files);
    }

    /**
     * @return array{applied: list<string>, skipped: list<string>, errors: list<string>}
     */
    public function run(?string $only = null): array
    {
        $result = ['applied' => [], 'skipped' => [], 'errors' => []];
        $this->ensureMigrationsTable();
        $this->bootstrapRegistryIfNeeded();

        $pending = $only !== null
            ? (in_array($only, $this->migrationFiles(), true) ? [$only] : [])
            : $this->pending();

        if ($only !== null && $pending === []) {
            $result['errors'][] = "Migración no encontrada o ya aplicada: {$only}";

            return $result;
        }

        foreach ($pending as $file) {
            if (in_array($file, $this->applied(), true)) {
                $result['skipped'][] = $file;
                continue;
            }

            try {
                $this->runFile($file);
                $stmt = $this->pdo->prepare('INSERT INTO schema_migrations (migration) VALUES (:m)');
                $stmt->execute(['m' => $file]);
                $result['applied'][] = $file;
            } catch (Throwable $e) {
                $result['errors'][] = "{$file}: " . $e->getMessage();
                break;
            }
        }

        return $result;
    }

    public function markApplied(string $file): void
    {
        if (!in_array($file, $this->migrationFiles(), true)) {
            throw new InvalidArgumentException("Archivo inexistente: {$file}");
        }

        $this->ensureMigrationsTable();
        $stmt = $this->pdo->prepare(
            'INSERT IGNORE INTO schema_migrations (migration) VALUES (:m)'
        );
        $stmt->execute(['m' => $file]);
    }

    /** Si la base ya existía sin schema_migrations, registra migraciones viejas y deja pendiente solo lo nuevo. */
    public function bootstrapRegistryIfNeeded(): void
    {
        if ($this->applied() !== []) {
            return;
        }

        $hasTenants = (bool) $this->pdo->query("SHOW TABLES LIKE 'tenants'")->fetchColumn();
        if (!$hasTenants) {
            return;
        }

        foreach ($this->migrationFiles() as $file) {
            if ($file === '005_business_type_catalog.sql') {
                $hasCatalog = (bool) $this->pdo->query(
                    "SHOW TABLES LIKE 'business_catalog_categories'"
                )->fetchColumn();
                if (!$hasCatalog) {
                    continue;
                }
            }
            $this->markApplied($file);
        }
    }

    private function runFile(string $file): void
    {
        $path = scripts_root() . '/database/migrations/' . $file;
        if (!is_readable($path)) {
            throw new RuntimeException("No se puede leer {$path}");
        }

        $sql = file_get_contents($path);
        if ($sql === false || trim($sql) === '') {
            return;
        }

        foreach ($this->splitStatements($sql) as $statement) {
            $this->pdo->exec($statement);
        }
    }

    /** @return list<string> */
    private function splitStatements(string $sql): array
    {
        $parts = preg_split('/;\s*[\r\n]+/', $sql) ?: [];
        $statements = [];

        foreach ($parts as $part) {
            $lines = preg_split('/\r\n|\r|\n/', $part) ?: [];
            $buffer = [];
            foreach ($lines as $line) {
                $trim = ltrim($line);
                if ($trim === '' || str_starts_with($trim, '--')) {
                    continue;
                }
                $buffer[] = $line;
            }
            $statement = trim(implode("\n", $buffer));
            if ($statement !== '') {
                $statements[] = $statement;
            }
        }

        return $statements;
    }
}
