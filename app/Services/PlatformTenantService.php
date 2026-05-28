<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use PDO;

final class PlatformTenantService
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    /** @return list<array<string, mixed>> */
    public function list(?string $search = null): array
    {
        $sql = 'SELECT t.*,
            (SELECT COUNT(*) FROM users u WHERE u.tenant_id = t.id) AS users_count,
            (SELECT COUNT(*) FROM products p WHERE p.tenant_id = t.id) AS products_count,
            (SELECT COALESCE(SUM(s.total), 0) FROM sales s WHERE s.tenant_id = t.id AND s.status = "completada") AS sales_total
            FROM tenants t WHERE 1=1';
        $params = [];

        if ($search) {
            $sql .= ' AND (t.name LIKE :q OR t.email LIKE :q OR t.slug LIKE :q)';
            $params['q'] = '%' . $search . '%';
        }

        $sql .= ' ORDER BY t.created_at DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /** @return array<string, mixed>|null */
    public function detail(int $tenantId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM tenants WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $tenantId]);
        $tenant = $stmt->fetch();
        if (!$tenant) {
            return null;
        }

        $users = $this->db->prepare(
            'SELECT u.id, u.name, u.email, u.is_active, u.last_login_at, r.name AS role_name
             FROM users u JOIN roles r ON r.id = u.role_id WHERE u.tenant_id = :id ORDER BY u.name'
        );
        $users->execute(['id' => $tenantId]);

        $settings = $this->db->prepare('SELECT * FROM business_settings WHERE tenant_id = :id LIMIT 1');
        $settings->execute(['id' => $tenantId]);

        return [
            'tenant' => $tenant,
            'users' => $users->fetchAll(),
            'settings' => $settings->fetch(),
            'stats' => $this->stats($tenantId),
        ];
    }

    /** @return array<string, mixed> */
    private function stats(int $tenantId): array
    {
        $stmt = $this->db->prepare(
            'SELECT
                (SELECT COUNT(*) FROM products WHERE tenant_id = :id) AS products,
                (SELECT COUNT(*) FROM sales WHERE tenant_id = :id2 AND status = "completada") AS sales,
                (SELECT COUNT(*) FROM users WHERE tenant_id = :id3) AS users'
        );
        $stmt->execute(['id' => $tenantId, 'id2' => $tenantId, 'id3' => $tenantId]);

        return $stmt->fetch() ?: ['products' => 0, 'sales' => 0, 'users' => 0];
    }

    public function setActive(int $tenantId, bool $active): void
    {
        $stmt = $this->db->prepare('UPDATE tenants SET is_active = :active WHERE id = :id');
        $stmt->execute(['active' => $active ? 1 : 0, 'id' => $tenantId]);
    }
}
