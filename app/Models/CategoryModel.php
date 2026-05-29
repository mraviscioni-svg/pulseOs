<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class CategoryModel extends Model
{
    protected string $table = 'product_categories';

    public function existsByName(int $tenantId, string $name, ?int $exceptId = null): bool
    {
        $sql = 'SELECT id FROM product_categories WHERE tenant_id = :tenant_id AND LOWER(name) = LOWER(:name)';
        if ($exceptId !== null) {
            $sql .= ' AND id != :except_id';
        }
        $sql .= ' LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $params = ['tenant_id' => $tenantId, 'name' => trim($name)];
        if ($exceptId !== null) {
            $params['except_id'] = $exceptId;
        }
        $stmt->execute($params);

        return (bool) $stmt->fetch();
    }

    /** @return list<array<string, mixed>> */
    public function search(int $tenantId, ?string $q = null): array
    {
        $sql = 'SELECT c.*, (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id AND p.tenant_id = c.tenant_id) AS product_count
                FROM product_categories c WHERE c.tenant_id = :tenant_id';
        $params = ['tenant_id' => $tenantId];
        if ($q !== null && $q !== '') {
            $sql .= ' AND (c.name LIKE :q OR c.description LIKE :q)';
            $params['q'] = '%' . $q . '%';
        }
        $sql .= ' ORDER BY c.name ASC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function deleteForTenant(int $tenantId, int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM product_categories WHERE id = :id AND tenant_id = :tenant_id');
        $stmt->execute(['id' => $id, 'tenant_id' => $tenantId]);
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
