<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class UserModel extends Model
{
    protected string $table = 'users';

    /** @return array<string, mixed>|null */
    public function findByEmailGlobal(string $email): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT u.*, r.slug AS role_slug, t.name AS tenant_name
             FROM users u
             JOIN roles r ON r.id = u.role_id
             JOIN tenants t ON t.id = u.tenant_id
             WHERE u.email = :email AND u.is_active = 1 AND t.is_active = 1
             LIMIT 1'
        );
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    /** @return list<string> */
    public function permissionsForRole(int $roleId): array
    {
        $stmt = $this->db->prepare(
            'SELECT p.slug FROM permissions p
             JOIN role_permissions rp ON rp.permission_id = p.id
             WHERE rp.role_id = :role_id'
        );
        $stmt->execute(['role_id' => $roleId]);

        return array_column($stmt->fetchAll(), 'slug');
    }

    public function updateLastLogin(int $userId): void
    {
        $stmt = $this->db->prepare('UPDATE users SET last_login_at = NOW() WHERE id = :id');
        $stmt->execute(['id' => $userId]);
    }

    /** @return list<array<string, mixed>> */
    public function listForTenant(int $tenantId): array
    {
        $stmt = $this->db->prepare(
            'SELECT u.id, u.name, u.email, u.is_active, u.created_at, r.name AS role_name, r.slug AS role_slug
             FROM users u JOIN roles r ON r.id = u.role_id
             WHERE u.tenant_id = :tenant_id ORDER BY u.name'
        );
        $stmt->execute(['tenant_id' => $tenantId]);

        return $stmt->fetchAll();
    }

    /** @param array<string, mixed> $data */
    public function create(int $tenantId, array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO users (tenant_id, role_id, name, email, password, phone)
             VALUES (:tenant_id, :role_id, :name, :email, :password, :phone)'
        );
        $stmt->execute([
            'tenant_id' => $tenantId,
            'role_id' => $data['role_id'],
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'phone' => $data['phone'] ?? null,
        ]);

        return (int) $this->db->lastInsertId();
    }
}
