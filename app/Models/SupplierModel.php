<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class SupplierModel extends Model
{
    protected string $table = 'suppliers';

    /** @return list<array<string, mixed>> */
    public function search(int $tenantId, ?string $q = null, ?string $status = null): array
    {
        $sql = 'SELECT * FROM suppliers WHERE tenant_id = :tenant_id';
        $params = ['tenant_id' => $tenantId];

        if ($status === 'active') {
            $sql .= ' AND is_active = 1';
        } elseif ($status === 'inactive') {
            $sql .= ' AND is_active = 0';
        }

        if ($q) {
            $sql .= ' AND (name LIKE :q OR company LIKE :q OR tax_id LIKE :q OR email LIKE :q OR phone LIKE :q)';
            $params['q'] = '%' . $q . '%';
        }

        $sql .= ' ORDER BY name ASC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /** @param array<string, mixed> $data */
    public function create(int $tenantId, array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO suppliers (tenant_id, name, company, tax_id, email, phone, address, notes)
             VALUES (:tenant_id, :name, :company, :tax_id, :email, :phone, :address, :notes)'
        );
        $stmt->execute([
            'tenant_id' => $tenantId,
            'name' => $data['name'],
            'company' => $data['company'] ?? null,
            'tax_id' => $data['tax_id'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        return (int) $this->db->lastInsertId();
    }

    /** @param array<string, mixed> $data */
    public function update(int $tenantId, int $id, array $data): void
    {
        $stmt = $this->db->prepare(
            'UPDATE suppliers SET name = :name, company = :company, tax_id = :tax_id,
             email = :email, phone = :phone, address = :address, notes = :notes, is_active = :is_active
             WHERE id = :id AND tenant_id = :tenant_id'
        );
        $stmt->execute([
            'name' => $data['name'],
            'company' => $data['company'] ?? null,
            'tax_id' => $data['tax_id'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'notes' => $data['notes'] ?? null,
            'is_active' => !empty($data['is_active']) ? 1 : 0,
            'id' => $id,
            'tenant_id' => $tenantId,
        ]);
    }

    public function setActive(int $tenantId, int $id, int $active): void
    {
        $stmt = $this->db->prepare(
            'UPDATE suppliers SET is_active = :active WHERE id = :id AND tenant_id = :tenant_id'
        );
        $stmt->execute(['active' => $active ? 1 : 0, 'id' => $id, 'tenant_id' => $tenantId]);
    }

    public function deleteForTenant(int $tenantId, int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM suppliers WHERE id = :id AND tenant_id = :tenant_id');
        $stmt->execute(['id' => $id, 'tenant_id' => $tenantId]);
    }
}
