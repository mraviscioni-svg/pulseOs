<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

final class DashboardService
{
    /** @return array<string, mixed> */
    public function stats(int $tenantId): array
    {
        $db = Database::connection();

        $salesToday = $db->prepare(
            "SELECT COALESCE(SUM(total), 0) AS total, COUNT(*) AS count
             FROM sales WHERE tenant_id = :tenant_id AND status = 'completada' AND DATE(created_at) = CURDATE()"
        );
        $salesToday->execute(['tenant_id' => $tenantId]);
        $sales = $salesToday->fetch() ?: ['total' => 0, 'count' => 0];

        $lowStock = $db->prepare(
            'SELECT COUNT(*) AS c FROM products
             WHERE tenant_id = :tenant_id AND is_active = 1 AND stock <= min_stock'
        );
        $lowStock->execute(['tenant_id' => $tenantId]);

        $topProducts = $db->prepare(
            'SELECT si.product_name, SUM(si.quantity) AS qty
             FROM sale_items si
             JOIN sales s ON s.id = si.sale_id
             WHERE si.tenant_id = :tenant_id AND s.status = "completada"
             AND s.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
             GROUP BY si.product_id, si.product_name
             ORDER BY qty DESC LIMIT 5'
        );
        $topProducts->execute(['tenant_id' => $tenantId]);

        $recentSales = $db->prepare(
            'SELECT sale_number, total, payment_method, created_at
             FROM sales WHERE tenant_id = :tenant_id AND status = "completada"
             ORDER BY created_at DESC LIMIT 8'
        );
        $recentSales->execute(['tenant_id' => $tenantId]);

        $pendingPurchases = $db->prepare(
            "SELECT COUNT(*) AS c FROM purchases WHERE tenant_id = :tenant_id AND status = 'pendiente'"
        );
        $pendingPurchases->execute(['tenant_id' => $tenantId]);

        $cash = $db->prepare(
            "SELECT * FROM cash_registers WHERE tenant_id = :tenant_id AND status = 'abierta' ORDER BY id DESC LIMIT 1"
        );
        $cash->execute(['tenant_id' => $tenantId]);
        $openCash = $cash->fetch();

        return [
            'sales_today_total' => (float) $sales['total'],
            'sales_today_count' => (int) $sales['count'],
            'low_stock_count' => (int) ($lowStock->fetch()['c'] ?? 0),
            'top_products' => $topProducts->fetchAll(),
            'recent_sales' => $recentSales->fetchAll(),
            'pending_purchases' => (int) ($pendingPurchases->fetch()['c'] ?? 0),
            'open_cash' => $openCash,
        ];
    }
}
