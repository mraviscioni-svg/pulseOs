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
        $checks = [
            'app' => 'ok',
            'base_path' => base_path(),
            'session_path' => session_cookie_path(),
            'php' => PHP_VERSION,
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
        }

        $ok = ($checks['session'] ?? '') === 'ok'
            && ($checks['database'] ?? '') === 'ok'
            && str_starts_with((string) ($checks['migration_004'] ?? ''), 'ok');

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
