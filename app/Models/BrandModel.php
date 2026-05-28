<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class BrandModel extends Model
{
    protected string $table = 'brands';

    public function existsByName(int $tenantId, string $name): bool
    {
        $stmt = $this->db->prepare(
            'SELECT id FROM brands WHERE tenant_id = :tenant_id AND LOWER(name) = LOWER(:name) LIMIT 1'
        );
        $stmt->execute(['tenant_id' => $tenantId, 'name' => trim($name)]);

        return (bool) $stmt->fetch();
    }

    /** @param array<string, mixed> $data */
    public function create(int $tenantId, array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO brands (tenant_id, name) VALUES (:tenant_id, :name)');
        $stmt->execute(['tenant_id' => $tenantId, 'name' => $data['name']]);

        return (int) $this->db->lastInsertId();
    }

    /** @param array<string, mixed> $data */
    public function update(int $tenantId, int $id, array $data): void
    {
        $stmt = $this->db->prepare('UPDATE brands SET name = :name WHERE id = :id AND tenant_id = :tenant_id');
        $stmt->execute(['name' => $data['name'], 'id' => $id, 'tenant_id' => $tenantId]);
    }
}
