<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Core\Model;

final class PurchaseModel extends Model
{
    protected string $table = 'purchases';

    /** @return list<array<string, mixed>> */
    public function listWithSupplier(int $tenantId, ?string $q = null, ?string $status = null): array
    {
        $sql = 'SELECT p.*, s.name AS supplier_name FROM purchases p
             LEFT JOIN suppliers s ON s.id = p.supplier_id
             WHERE p.tenant_id = :tenant_id';
        $params = ['tenant_id' => $tenantId];

        if ($status && $status !== 'all') {
            $sql .= ' AND p.status = :status';
            $params['status'] = $status;
        }

        if ($q) {
            $sql .= ' AND (s.name LIKE :q OR CAST(p.id AS CHAR) LIKE :q OR p.notes LIKE :q)';
            $params['q'] = '%' . $q . '%';
        }

        $sql .= ' ORDER BY p.created_at DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /** @param list<array{product_id: int, quantity: float, unit_cost: float}> $items */
    public function create(int $tenantId, int $userId, ?int $supplierId, array $items, ?string $notes): int
    {
        $subtotal = 0.0;
        foreach ($items as $item) {
            $subtotal += $item['quantity'] * $item['unit_cost'];
        }

        $db = Database::connection();
        $db->beginTransaction();
        try {
            $stmt = $db->prepare(
                'INSERT INTO purchases (tenant_id, supplier_id, user_id, status, subtotal, total, notes)
                 VALUES (:tenant_id, :supplier_id, :user_id, "pendiente", :subtotal, :total, :notes)'
            );
            $stmt->execute([
                'tenant_id' => $tenantId,
                'supplier_id' => $supplierId,
                'user_id' => $userId,
                'subtotal' => $subtotal,
                'total' => $subtotal,
                'notes' => $notes,
            ]);
            $purchaseId = (int) $db->lastInsertId();

            $itemStmt = $db->prepare(
                'INSERT INTO purchase_items (tenant_id, purchase_id, product_id, quantity, unit_cost, total)
                 VALUES (:tenant_id, :purchase_id, :product_id, :quantity, :unit_cost, :total)'
            );
            foreach ($items as $item) {
                $line = $item['quantity'] * $item['unit_cost'];
                $itemStmt->execute([
                    'tenant_id' => $tenantId,
                    'purchase_id' => $purchaseId,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'total' => $line,
                ]);
            }

            $db->commit();

            return $purchaseId;
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }
}
