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
            'env_file' => is_file($root . '/.env') ? 'ok (.env en servidor)' : 'falta — el deploy debe generar .env o crealo manual',
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

            $stmt = $pdo->query("SHOW COLUMNS FROM platform_admins LIKE 'username'");
            $checks['platform_username'] = $stmt->fetch() ? 'ok' : 'falta — importá 004_username_login.sql';

            $admins = (int) $pdo->query('SELECT COUNT(*) FROM platform_admins')->fetchColumn();
            $checks['platform_admins'] = $admins > 0 ? "ok ({$admins})" : 'vacío — importá 003_platform_admins.sql';
        } catch (PDOException $e) {
            $checks['database'] = 'fail';
            $checks['database_error'] = $e->getMessage();
            $checks['host_probe'] = Database::probeHosts();

            $working = array_filter($checks['host_probe'], fn (array $r) => $r['ok']);
            if ($working !== []) {
                $good = reset($working);
                $checks['fix'] = "En GitHub Secret PROD_DB_HOST (o .env DB_HOST) usá: \"{$good['host']}\". En cPanel casi siempre es localhost.";
            } else {
                $checks['fix'] = 'Revisá en cPanel → MySQL: host (localhost), nombre de base, usuario y clave. Actualizá secrets PROD_DB_* y redeploy, o editá .env en PulseOS-prep/.';
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
                'registro' => url('/register'),
            ],
        ], $ok ? 200 : 503);
    }
}
