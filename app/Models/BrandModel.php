<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class BrandModel extends Model
{
    protected string $table = 'brands';

    public function existsByName(int $tenantId, string $name, ?int $exceptId = null): bool
    {
        $sql = 'SELECT id FROM brands WHERE tenant_id = :tenant_id AND LOWER(name) = LOWER(:name)';
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
        $sql = 'SELECT b.*, (SELECT COUNT(*) FROM products p WHERE p.brand_id = b.id AND p.tenant_id = b.tenant_id) AS product_count
                FROM brands b WHERE b.tenant_id = :tenant_id';
        $params = ['tenant_id' => $tenantId];
        if ($q !== null && $q !== '') {
            $sql .= ' AND b.name LIKE :q';
            $params['q'] = '%' . $q . '%';
        }
        $sql .= ' ORDER BY b.name ASC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function deleteForTenant(int $tenantId, int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM brands WHERE id = :id AND tenant_id = :tenant_id');
        $stmt->execute(['id' => $id, 'tenant_id' => $tenantId]);
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
