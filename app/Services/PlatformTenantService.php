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
            'SELECT u.id, u.username, u.name, u.email, u.is_active, u.last_login_at, r.name AS role_name
             FROM users u JOIN roles r ON r.id = u.role_id WHERE u.tenant_id = :id ORDER BY u.name'
        );
        $users->execute(['id' => $tenantId]);

        $settings = $this->db->prepare('SELECT * FROM business_settings WHERE tenant_id = :id LIMIT 1');
        $settings->execute(['id' => $tenantId]);

        $settingsRow = $settings->fetch();

        return [
            'tenant' => $tenant,
            'users' => $users->fetchAll(),
            'settings' => is_array($settingsRow) ? $settingsRow : [],
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

    public function delete(int $tenantId): void
    {
        $stmt = $this->db->prepare('DELETE FROM tenants WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $tenantId]);
        if ($stmt->rowCount() === 0) {
            throw new \RuntimeException('Comercio no encontrado.');
        }
    }

    /** @param array{name: string, email: string, phone?: ?string, address?: ?string, business_type: string, modules?: list<string>} $data */
    public function update(int $tenantId, array $data): void
    {
        $modules = $this->normalizeModules($data);

        $stmt = $this->db->prepare(
            'UPDATE tenants SET name = :name, email = :email, phone = :phone, address = :address, business_type = :business_type WHERE id = :id'
        );
        $stmt->execute([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?: null,
            'address' => $data['address'] ?: null,
            'business_type' => $data['business_type'],
            'id' => $tenantId,
        ]);

        $this->ensureSettingsRow($tenantId, $modules);
    }

    /** @param list<string> $modules */
    private function ensureSettingsRow(int $tenantId, array $modules): void
    {
        $check = $this->db->prepare('SELECT id FROM business_settings WHERE tenant_id = :id LIMIT 1');
        $check->execute(['id' => $tenantId]);
        $encoded = json_encode($modules, JSON_UNESCAPED_UNICODE);

        if ($check->fetch()) {
            $stmt = $this->db->prepare(
                'UPDATE business_settings SET modules_json = :modules WHERE tenant_id = :id'
            );
            $stmt->execute(['modules' => $encoded, 'id' => $tenantId]);

            return;
        }

        $stmt = $this->db->prepare(
            'INSERT INTO business_settings (tenant_id, modules_json) VALUES (:id, :modules)'
        );
        $stmt->execute(['id' => $tenantId, 'modules' => $encoded]);
    }

    /** @param array{modules?: list<string>|mixed, business_type?: string} $data
     * @return list<string>
     */
    public function normalizeModules(array $data): array
    {
        $allowed = array_keys(config('platform_modules'));
        $modules = $data['modules'] ?? null;

        if (!is_array($modules)) {
            $type = (string) ($data['business_type'] ?? 'otro');

            return config('business_types')[$type]['modules'] ?? [];
        }

        $modules = array_values(array_unique(array_filter(
            $modules,
            static fn ($m) => is_string($m) && in_array($m, $allowed, true)
        )));

        return $modules;
    }
}
