<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class SupplierModel extends Model
{
    protected string $table = 'suppliers';

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
}
