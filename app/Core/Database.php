<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

final class Database
{
    private static ?PDO $pdo = null;

    /** @return array{host: string, port: int, database: string, username: string, password: string, charset: string, socket: string} */
    public static function config(): array
    {
        $cfg = require dirname(__DIR__, 2) . '/config/database.php';

        return [
            'host' => (string) ($cfg['host'] ?? 'localhost'),
            'port' => (int) ($cfg['port'] ?? 3306),
            'database' => (string) ($cfg['database'] ?? ''),
            'username' => (string) ($cfg['username'] ?? ''),
            'password' => (string) ($cfg['password'] ?? ''),
            'charset' => (string) ($cfg['charset'] ?? 'utf8mb4'),
            'socket' => (string) ($cfg['socket'] ?? ''),
        ];
    }

    public static function connection(): PDO
    {
        if (self::$pdo === null) {
            self::$pdo = self::connect(self::config());
        }

        return self::$pdo;
    }

    /**
     * Prueba hosts típicos de cPanel (solo diagnóstico).
     *
     * @return list<array{host: string, ok: bool, error?: string}>
     */
    public static function probeHosts(): array
    {
        $config = self::config();
        $hosts = array_values(array_unique(array_filter([
            $config['host'],
            $config['socket'] !== '' ? null : 'localhost',
            $config['socket'] !== '' ? null : '127.0.0.1',
        ])));

        $results = [];
        foreach ($hosts as $host) {
            try {
                $pdo = self::connect([...$config, 'host' => $host]);
                $pdo->query('SELECT 1');
                $results[] = ['host' => $host, 'ok' => true];
            } catch (PDOException $e) {
                $results[] = ['host' => $host, 'ok' => false, 'error' => $e->getMessage()];
            }
        }

        return $results;
    }

    /** @param array{host: string, port: int, database: string, username: string, password: string, charset: string, socket: string} $config */
    public static function connect(array $config): PDO
    {
        if ($config['socket'] !== '') {
            $dsn = sprintf(
                'mysql:unix_socket=%s;dbname=%s;charset=%s',
                $config['socket'],
                $config['database'],
                $config['charset']
            );
        } else {
            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=%s',
                $config['host'],
                $config['port'],
                $config['database'],
                $config['charset']
            );
        }

        return new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_TIMEOUT => 5,
        ]);
    }
}
