<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class CategoryModel extends Model
{
    protected string $table = 'product_categories';

    public function existsByName(int $tenantId, string $name): bool
    {
        $stmt = $this->db->prepare(
            'SELECT id FROM product_categories WHERE tenant_id = :tenant_id AND LOWER(name) = LOWER(:name) LIMIT 1'
        );
        $stmt->execute(['tenant_id' => $tenantId, 'name' => trim($name)]);

        return (bool) $stmt->fetch();
    }

    /** @param array<string, mixed> $data */
    public function create(int $tenantId, array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO product_categories (tenant_id, name, description) VALUES (:tenant_id, :name, :description)'
        );
        $stmt->execute([
            'tenant_id' => $tenantId,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        return (int) $this->db->lastInsertId();
    }

    /** @param array<string, mixed> $data */
    public function update(int $tenantId, int $id, array $data): void
    {
        $stmt = $this->db->prepare(
            'UPDATE product_categories SET name = :name, description = :description
             WHERE id = :id AND tenant_id = :tenant_id'
        );
        $stmt->execute([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'id' => $id,
            'tenant_id' => $tenantId,
        ]);
    }
}
