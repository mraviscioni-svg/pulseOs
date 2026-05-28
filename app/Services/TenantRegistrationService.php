<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use PDO;

final class TenantRegistrationService
{
    public function __construct(
        private readonly AuditService $audit = new AuditService(),
    ) {
    }

    /** @param array<string, mixed> $data */
    public function register(array $data): int
    {
        $db = Database::connection();
        $db->beginTransaction();

        try {
            $slug = $this->uniqueSlug($data['company_name']);

            $stmt = $db->prepare(
                'INSERT INTO tenants (name, slug, business_type, email, phone)
                 VALUES (:name, :slug, :business_type, :email, :phone)'
            );
            $stmt->execute([
                'name' => $data['company_name'],
                'slug' => $slug,
                'business_type' => $data['business_type'] ?? 'otro',
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
            ]);
            $tenantId = (int) $db->lastInsertId();

            $modules = config('business_types')[$data['business_type'] ?? 'otro']['modules'] ?? [];
            $settings = $db->prepare(
                'INSERT INTO business_settings (tenant_id, modules_json) VALUES (:tenant_id, :modules)'
            );
            $settings->execute([
                'tenant_id' => $tenantId,
                'modules' => json_encode($modules, JSON_UNESCAPED_UNICODE),
            ]);

            $roleId = $this->ownerRoleId($db);
            $password = password_hash($data['password'], PASSWORD_DEFAULT);

            $user = $db->prepare(
                'INSERT INTO users (tenant_id, role_id, name, email, password)
                 VALUES (:tenant_id, :role_id, :name, :email, :password)'
            );
            $user->execute([
                'tenant_id' => $tenantId,
                'role_id' => $roleId,
                'name' => $data['owner_name'],
                'email' => $data['email'],
                'password' => $password,
            ]);
            $userId = (int) $db->lastInsertId();

            $db->commit();
            $this->audit->log($tenantId, $userId, 'tenant.registered');

            return $tenantId;
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    private function uniqueSlug(string $name): string
    {
        $base = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $name) ?? '', '-'));
        $slug = $base ?: 'negocio';
        $db = Database::connection();
        $i = 0;

        while (true) {
            $candidate = $i === 0 ? $slug : $slug . '-' . $i;
            $stmt = $db->prepare('SELECT id FROM tenants WHERE slug = :slug LIMIT 1');
            $stmt->execute(['slug' => $candidate]);
            if (!$stmt->fetch()) {
                return $candidate;
            }
            ++$i;
        }
    }

    private function ownerRoleId(PDO $db): int
    {
        $stmt = $db->query("SELECT id FROM roles WHERE slug = 'owner' LIMIT 1");
        $row = $stmt->fetch();

        return (int) ($row['id'] ?? 1);
    }
}
