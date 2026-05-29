<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class CustomerVehicleModel extends Model
{
    protected string $table = 'customer_vehicles';

    /** @return list<array<string, mixed>> */
    public function forCustomer(int $tenantId, int $customerId, bool $activeOnly = false): array
    {
        $sql = 'SELECT * FROM customer_vehicles
                WHERE tenant_id = :tenant_id AND customer_id = :customer_id';
        if ($activeOnly) {
            $sql .= ' AND is_active = 1';
        }
        $sql .= ' ORDER BY label ASC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['tenant_id' => $tenantId, 'customer_id' => $customerId]);

        return $stmt->fetchAll();
    }

    /** @return array<string, mixed>|null */
    public function findForCustomer(int $tenantId, int $customerId, int $vehicleId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM customer_vehicles
             WHERE id = :id AND customer_id = :customer_id AND tenant_id = :tenant_id
             LIMIT 1'
        );
        $stmt->execute(['id' => $vehicleId, 'customer_id' => $customerId, 'tenant_id' => $tenantId]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function create(int $tenantId, int $customerId, string $label, ?string $description = null): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO customer_vehicles (tenant_id, customer_id, label, description)
             VALUES (:tenant_id, :customer_id, :label, :description)'
        );
        $stmt->execute([
            'tenant_id' => $tenantId,
            'customer_id' => $customerId,
            'label' => $label,
            'description' => $description,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function deleteForCustomer(int $tenantId, int $customerId, int $vehicleId): void
    {
        $stmt = $this->db->prepare(
            'DELETE FROM customer_vehicles
             WHERE id = :id AND customer_id = :customer_id AND tenant_id = :tenant_id'
        );
        $stmt->execute(['id' => $vehicleId, 'customer_id' => $customerId, 'tenant_id' => $tenantId]);
    }
}
