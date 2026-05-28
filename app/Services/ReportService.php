<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use PDO;

final class ReportService
{
    private readonly PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::connection();
    }

    /** @return list<array<string, mixed>> */
    public function salesByDay(int $tenantId, string $from, string $to): array
    {
        $stmt = $this->db->prepare(
            "SELECT DATE(created_at) AS day, COUNT(*) AS sales_count, COALESCE(SUM(total), 0) AS total
             FROM sales WHERE tenant_id = :tenant_id AND status = 'completada'
             AND DATE(created_at) BETWEEN :from AND :to
             GROUP BY DATE(created_at) ORDER BY day"
        );
        $stmt->execute(['tenant_id' => $tenantId, 'from' => $from, 'to' => $to]);

        return $stmt->fetchAll();
    }

    /** @return list<array<string, mixed>> */
    public function salesByUser(int $tenantId, string $from, string $to): array
    {
        $stmt = $this->db->prepare(
            "SELECT u.name AS user_name, COUNT(s.id) AS sales_count, COALESCE(SUM(s.total), 0) AS total
             FROM sales s LEFT JOIN users u ON u.id = s.user_id
             WHERE s.tenant_id = :tenant_id AND s.status = 'completada'
             AND DATE(s.created_at) BETWEEN :from AND :to
             GROUP BY s.user_id, u.name ORDER BY total DESC"
        );
        $stmt->execute(['tenant_id' => $tenantId, 'from' => $from, 'to' => $to]);

        return $stmt->fetchAll();
    }

    /** @return list<array<string, mixed>> */
    public function topProducts(int $tenantId, string $from, string $to, int $limit = 20): array
    {
        $stmt = $this->db->prepare(
            "SELECT si.product_name, SUM(si.quantity) AS qty, SUM(si.total) AS revenue
             FROM sale_items si JOIN sales s ON s.id = si.sale_id
             WHERE si.tenant_id = :tenant_id AND s.status = 'completada'
             AND DATE(s.created_at) BETWEEN :from AND :to
             GROUP BY si.product_id, si.product_name ORDER BY qty DESC LIMIT " . (int) $limit
        );
        $stmt->execute(['tenant_id' => $tenantId, 'from' => $from, 'to' => $to]);

        return $stmt->fetchAll();
    }

    /** @return list<array<string, mixed>> */
    public function lowStock(int $tenantId): array
    {
        $stmt = $this->db->prepare(
            'SELECT name, sku, stock, min_stock, price FROM products
             WHERE tenant_id = :tenant_id AND is_active = 1 AND stock <= min_stock ORDER BY stock ASC'
        );
        $stmt->execute(['tenant_id' => $tenantId]);

        return $stmt->fetchAll();
    }

    /** @return list<array<string, mixed>> */
    public function profitMargin(int $tenantId, string $from, string $to): array
    {
        $stmt = $this->db->prepare(
            "SELECT si.product_name,
                    SUM(si.quantity) AS qty,
                    SUM(si.total) AS revenue,
                    SUM(si.quantity * p.cost) AS cost_total
             FROM sale_items si
             JOIN sales s ON s.id = si.sale_id
             JOIN products p ON p.id = si.product_id
             WHERE si.tenant_id = :tenant_id AND s.status = 'completada'
             AND DATE(s.created_at) BETWEEN :from AND :to
             GROUP BY si.product_id, si.product_name
             ORDER BY (SUM(si.total) - SUM(si.quantity * p.cost)) DESC LIMIT 30"
        );
        $stmt->execute(['tenant_id' => $tenantId, 'from' => $from, 'to' => $to]);
        $rows = $stmt->fetchAll();
        foreach ($rows as &$row) {
            $row['margin'] = (float) $row['revenue'] - (float) $row['cost_total'];
            $row['margin_pct'] = $row['revenue'] > 0
                ? round(((float) $row['margin'] / (float) $row['revenue']) * 100, 1)
                : 0;
        }

        return $rows;
    }

    /** @return list<array<string, mixed>> */
    public function purchasesBySupplier(int $tenantId, string $from, string $to): array
    {
        $stmt = $this->db->prepare(
            "SELECT COALESCE(s.name, 'Sin proveedor') AS supplier_name,
                    COUNT(p.id) AS orders, COALESCE(SUM(p.total), 0) AS total
             FROM purchases p LEFT JOIN suppliers s ON s.id = p.supplier_id
             WHERE p.tenant_id = :tenant_id AND p.status = 'recibida'
             AND DATE(p.created_at) BETWEEN :from AND :to
             GROUP BY p.supplier_id, s.name ORDER BY total DESC"
        );
        $stmt->execute(['tenant_id' => $tenantId, 'from' => $from, 'to' => $to]);

        return $stmt->fetchAll();
    }

    /** @return list<array<string, mixed>> */
    public function dailyCash(int $tenantId, string $from, string $to): array
    {
        $stmt = $this->db->prepare(
            "SELECT opened_at, closed_at, opening_amount, closing_amount, expected_amount, difference, status
             FROM cash_registers WHERE tenant_id = :tenant_id
             AND DATE(opened_at) BETWEEN :from AND :to ORDER BY opened_at DESC"
        );
        $stmt->execute(['tenant_id' => $tenantId, 'from' => $from, 'to' => $to]);

        return $stmt->fetchAll();
    }

    /** @return list<array<string, mixed>> */
    public function productsWithoutMovement(int $tenantId, int $days = 90): array
    {
        $stmt = $this->db->prepare(
            "SELECT p.id, p.name, p.sku, p.stock, p.price
             FROM products p
             WHERE p.tenant_id = :tenant_id AND p.is_active = 1
             AND NOT EXISTS (
                SELECT 1 FROM sale_items si
                JOIN sales s ON s.id = si.sale_id
                WHERE si.product_id = p.id AND s.tenant_id = :tenant_id2
                AND s.created_at >= DATE_SUB(NOW(), INTERVAL :days DAY)
             )
             ORDER BY p.name LIMIT 100"
        );
        $stmt->execute(['tenant_id' => $tenantId, 'tenant_id2' => $tenantId, 'days' => $days]);

        return $stmt->fetchAll();
    }
}
