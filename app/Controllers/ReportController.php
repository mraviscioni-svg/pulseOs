<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Services\ReportService;

final class ReportController extends Controller
{
    public function index(): void
    {
        $from = $_GET['from'] ?? date('Y-m-01');
        $to = $_GET['to'] ?? date('Y-m-d');
        $type = $_GET['type'] ?? 'sales_day';
        $tenantId = $this->tenantId();
        $reports = new ReportService();

        $data = match ($type) {
            'sales_day' => $reports->salesByDay($tenantId, $from, $to),
            'sales_user' => $reports->salesByUser($tenantId, $from, $to),
            'top_products' => $reports->topProducts($tenantId, $from, $to),
            'low_stock' => $reports->lowStock($tenantId),
            'margin' => $reports->profitMargin($tenantId, $from, $to),
            'purchases_supplier' => $reports->purchasesBySupplier($tenantId, $from, $to),
            'cash_daily' => $reports->dailyCash($tenantId, $from, $to),
            'no_movement' => $reports->productsWithoutMovement($tenantId, (int) ($_GET['days'] ?? 90)),
            default => [],
        };

        $this->view('reports/index', [
            'title' => 'Reportes',
            'type' => $type,
            'from' => $from,
            'to' => $to,
            'data' => $data,
            'chartLabels' => $type === 'sales_day' ? array_column($data, 'day') : [],
            'chartValues' => $type === 'sales_day' ? array_map('floatval', array_column($data, 'total')) : [],
        ]);
    }
}
