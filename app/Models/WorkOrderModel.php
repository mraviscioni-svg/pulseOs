<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Core\Model;
use PDO;

final class WorkOrderModel extends Model
{
    protected string $table = 'work_orders';

    /** @return list<array<string, mixed>> */
    public function listForTenant(int $tenantId, ?string $q = null, ?string $status = null, ?int $assignedUserId = null): array
    {
        $sql = 'SELECT wo.*, u.name AS assigned_name
                FROM work_orders wo
                LEFT JOIN users u ON u.id = wo.assigned_user_id
                WHERE wo.tenant_id = :tenant_id';
        $params = ['tenant_id' => $tenantId];

        if ($status && $status !== 'all') {
            $sql .= ' AND wo.status = :status';
            $params['status'] = $status;
        }

        if ($assignedUserId) {
            $sql .= ' AND wo.assigned_user_id = :assigned';
            $params['assigned'] = $assignedUserId;
        }

        if ($q) {
            $sql .= ' AND (
                wo.order_number LIKE :q OR wo.customer_name LIKE :q
                OR wo.customer_phone LIKE :q OR wo.vehicle_label LIKE :q
                OR wo.reported_issue LIKE :q
            )';
            $params['q'] = '%' . $q . '%';
        }

        $sql .= ' ORDER BY wo.created_at DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /** @return array<string, mixed>|null */
    public function findDetailed(int $tenantId, int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT wo.*,
                    au.name AS assigned_name,
                    cu.name AS created_by_name,
                    s.sale_number,
                    dc.name AS directory_customer_name
             FROM work_orders wo
             LEFT JOIN users au ON au.id = wo.assigned_user_id
             LEFT JOIN users cu ON cu.id = wo.created_by_user_id
             LEFT JOIN sales s ON s.id = wo.sale_id
             LEFT JOIN customers dc ON dc.id = wo.customer_id
             WHERE wo.id = :id AND wo.tenant_id = :tenant_id
             LIMIT 1'
        );
        $stmt->execute(['id' => $id, 'tenant_id' => $tenantId]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    /** @return list<array<string, mixed>> */
    public function linesForOrder(int $tenantId, int $workOrderId): array
    {
        $stmt = $this->db->prepare(
            'SELECT l.*, p.name AS product_name, p.stock AS product_stock
             FROM work_order_lines l
             LEFT JOIN products p ON p.id = l.product_id
             WHERE l.work_order_id = :wo_id AND l.tenant_id = :tenant_id
             ORDER BY l.sort_order, l.id'
        );
        $stmt->execute(['wo_id' => $workOrderId, 'tenant_id' => $tenantId]);

        return $stmt->fetchAll();
    }

    /** @return list<array<string, mixed>> */
    public function statusHistory(int $tenantId, int $workOrderId): array
    {
        $stmt = $this->db->prepare(
            'SELECT h.*, u.name AS user_name
             FROM work_order_status_history h
             LEFT JOIN users u ON u.id = h.user_id
             WHERE h.work_order_id = :wo_id AND h.tenant_id = :tenant_id
             ORDER BY h.created_at DESC, h.id DESC'
        );
        $stmt->execute(['wo_id' => $workOrderId, 'tenant_id' => $tenantId]);

        return $stmt->fetchAll();
    }

    public function nextOrderNumber(PDO $db, int $tenantId): string
    {
        $stmt = $db->prepare(
            'SELECT COUNT(*) AS c FROM work_orders WHERE tenant_id = :tenant_id AND DATE(created_at) = CURDATE()'
        );
        $stmt->execute(['tenant_id' => $tenantId]);
        $count = (int) ($stmt->fetch()['c'] ?? 0);

        return 'OT-' . date('Ymd') . '-' . str_pad((string) ($count + 1), 4, '0', STR_PAD_LEFT);
    }

    /** @param list<array<string, mixed>> $lines */
    public function recalcTotals(int $tenantId, int $workOrderId, array $lines): void
    {
        $total = 0.0;
        foreach ($lines as $line) {
            $total += (float) ($line['line_total'] ?? 0);
        }

        $stmt = $this->db->prepare(
            'UPDATE work_orders SET subtotal = :sub, total = :total
             WHERE id = :id AND tenant_id = :tenant_id'
        );
        $stmt->execute([
            'sub' => $total,
            'total' => $total,
            'id' => $workOrderId,
            'tenant_id' => $tenantId,
        ]);
    }

    public function linkSale(int $tenantId, int $workOrderId, int $saleId): void
    {
        $stmt = $this->db->prepare(
            'UPDATE work_orders SET sale_id = :sale_id, status = "cerrada", closed_at = NOW()
             WHERE id = :id AND tenant_id = :tenant_id'
        );
        $stmt->execute([
            'sale_id' => $saleId,
            'id' => $workOrderId,
            'tenant_id' => $tenantId,
        ]);
    }

    public function markApproved(int $tenantId, int $workOrderId): void
    {
        $stmt = $this->db->prepare(
            'UPDATE work_orders SET approved_at = COALESCE(approved_at, NOW())
             WHERE id = :id AND tenant_id = :tenant_id'
        );
        $stmt->execute(['id' => $workOrderId, 'tenant_id' => $tenantId]);
    }
}
