<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;
use PDOException;

final class HealthController extends Controller
{
    public function index(): void
    {
        $root = dirname(__DIR__, 2);
        $dbCfg = Database::config();

        $checks = [
            'app' => 'ok',
            'base_path' => base_path(),
            'session_path' => session_cookie_path(),
            'php' => PHP_VERSION,
            'env_file' => is_file($root . '/.env') ? 'ok (.env)' : (is_file($root . '/pulseos.env') ? 'ok (pulseos.env)' : 'falta — redeploy o creá pulseos.env'),
            'db_host' => $dbCfg['host'],
            'db_name' => $dbCfg['database'],
            'db_user' => $dbCfg['username'],
            'db_password_set' => $dbCfg['password'] !== '' ? 'sí' : 'no (vacío)',
        ];

        try {
            Session::set('_health_ping', '1');
            $checks['session'] = Session::get('_health_ping') === '1' ? 'ok' : 'fail';
        } catch (\Throwable $e) {
            $checks['session'] = 'fail: ' . $e->getMessage();
        }

        try {
            $pdo = Database::connection();
            $pdo->query('SELECT 1');
            $checks['database'] = 'ok';

            $missing = [];
            foreach (['tenants', 'users', 'platform_admins', 'roles'] as $table) {
                try {
                    $pdo->query("SELECT 1 FROM {$table} LIMIT 1");
                } catch (PDOException) {
                    $missing[] = $table;
                }
            }
            $checks['tables'] = $missing === [] ? 'ok' : 'faltan: ' . implode(', ', $missing);

            $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'username'");
            $checks['migration_004'] = $stmt->fetch() ? 'ok' : 'falta columna users.username — importá 004_username_login.sql';

            $idx = $pdo->query("SHOW INDEX FROM users WHERE Key_name = 'uq_users_tenant_username'")->fetch();
            if ($idx) {
                $checks['migration_010'] = 'ok (username único por comercio)';
            } elseif ($pdo->query("SHOW INDEX FROM users WHERE Key_name = 'uq_users_username'")->fetch()) {
                $checks['migration_010'] = 'pendiente — ejecutá 010_tenant_scoped_username.sql o POST migrate-runner.php';
            } else {
                $checks['migration_010'] = 'revisar índices users — migración 004/010';
            }

            $stmt = $pdo->query("SHOW COLUMNS FROM platform_admins LIKE 'username'");
            $checks['platform_username'] = $stmt->fetch() ? 'ok' : 'falta — importá 004_username_login.sql';

            $admins = (int) $pdo->query('SELECT COUNT(*) FROM platform_admins')->fetchColumn();
            $checks['platform_admins'] = $admins > 0 ? "ok ({$admins})" : 'vacío — importá 003_platform_admins.sql';
        } catch (PDOException $e) {
            $checks['database'] = 'fail';
            $checks['database_error'] = $e->getMessage();
            $checks['host_probe'] = Database::probeHosts();

            if (str_contains($dbCfg['host'], ':2083') || str_contains($dbCfg['host'], 'cpanel')) {
                $checks['fix'] = 'PROD_DB_HOST está mal: "' . $dbCfg['host'] . '" es la URL de cPanel, no MySQL. Usá PROD_DB_HOST=localhost';
            } else {
                $localhostProbe = null;
                foreach ($checks['host_probe'] as $row) {
                    if (in_array($row['host'], ['localhost', '127.0.0.1'], true)) {
                        $localhostProbe = $row;
                        break;
                    }
                }
                if ($localhostProbe && !$localhostProbe['ok'] && str_contains((string) ($localhostProbe['error'] ?? ''), '1045')) {
                    $checks['fix'] = 'El host localhost es correcto, pero usuario/clave no coinciden o el usuario no está asignado a la base en cPanel → MySQL® Databases → Add User To Database (ALL PRIVILEGES).';
                } elseif ($localhostProbe && $localhostProbe['ok']) {
                    $checks['fix'] = 'Usá PROD_DB_HOST=localhost en secrets y redeploy.';
                } else {
                    $working = array_filter($checks['host_probe'], fn (array $r) => $r['ok']);
                    if ($working !== []) {
                        $good = reset($working);
                        $checks['fix'] = 'En PROD_DB_HOST usá: "' . $good['host'] . '"';
                    } else {
                        $checks['fix'] = 'Revisá en cPanel → MySQL: host localhost, base, usuario con permisos, clave. Actualizá PROD_DB_* y redeploy.';
                    }
                }
            }
        }

        $ok = ($checks['session'] ?? '') === 'ok'
            && ($checks['database'] ?? '') === 'ok'
            && ($checks['tables'] ?? '') === 'ok';

        $this->json([
            'status' => $ok ? 'ok' : 'error',
            'checks' => $checks,
            'login_urls' => [
                'comercio' => url('/login'),
                'plataforma' => url('/admin/login'),
            ],
        ], $ok ? 200 : 503);
    }
}
