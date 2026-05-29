<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class CustomerModel extends Model
{
    protected string $table = 'customers';

    /** @return list<array<string, mixed>> */
    public function search(int $tenantId, ?string $q = null, ?string $status = null, int $limit = 500): array
    {
        $sql = 'SELECT c.*,
                (SELECT COUNT(*) FROM work_orders wo WHERE wo.customer_id = c.id AND wo.tenant_id = c.tenant_id) AS work_orders_count
                FROM customers c
                WHERE c.tenant_id = :tenant_id';
        $params = ['tenant_id' => $tenantId];

        if ($status === 'active') {
            $sql .= ' AND c.is_active = 1';
        } elseif ($status === 'inactive') {
            $sql .= ' AND c.is_active = 0';
        }

        if ($q) {
            $sql .= ' AND (c.name LIKE :q OR c.company LIKE :q OR c.tax_id LIKE :q
                OR c.email LIKE :q OR c.phone LIKE :q OR c.notes LIKE :q)';
            $params['q'] = '%' . $q . '%';
        }

        $sql .= ' ORDER BY c.name ASC LIMIT ' . (int) $limit;
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /** @return array<string, mixed>|null */
    public function findWithVehicles(int $tenantId, int $id): ?array
    {
        $customer = $this->find($id, $tenantId);
        if (!$customer) {
            return null;
        }

        $customer['vehicles'] = (new CustomerVehicleModel())->forCustomer($tenantId, $id);

        return $customer;
    }

    /** @param array<string, mixed> $data */
    public function create(int $tenantId, array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO customers (tenant_id, name, company, tax_id, email, phone, address, notes)
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
            'UPDATE customers SET name = :name, company = :company, tax_id = :tax_id,
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
            'UPDATE customers SET is_active = :active WHERE id = :id AND tenant_id = :tenant_id'
        );
        $stmt->execute(['active' => $active ? 1 : 0, 'id' => $id, 'tenant_id' => $tenantId]);
    }

    public function deleteForTenant(int $tenantId, int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM customers WHERE id = :id AND tenant_id = :tenant_id');
        $stmt->execute(['id' => $id, 'tenant_id' => $tenantId]);
    }

    /** @return list<array<string, mixed>> */
    public function recentWorkOrders(int $tenantId, int $customerId, int $limit = 10): array
    {
        $stmt = $this->db->prepare(
            'SELECT id, order_number, status, total, vehicle_label, created_at
             FROM work_orders
             WHERE tenant_id = :tenant_id AND customer_id = :customer_id
             ORDER BY created_at DESC
             LIMIT ' . (int) $limit
        );
        $stmt->execute(['tenant_id' => $tenantId, 'customer_id' => $customerId]);

        return $stmt->fetchAll();
    }
}
