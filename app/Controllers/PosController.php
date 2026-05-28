<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\ProductModel;
use App\Services\CashRegisterService;
use App\Services\SaleService;

final class PosController extends Controller
{
    public function index(): void
    {
        $cash = (new CashRegisterService())->getOpenRegister($this->tenantId());
        $this->view('pos/index', [
            'title' => 'Punto de venta',
            'openCash' => $cash,
            'products' => (new ProductModel())->search($this->tenantId(), null, 100),
        ]);
    }

    public function complete(): void
    {
        $data = $this->input();
        $items = json_decode($data['items_json'] ?? '[]', true);
        if (!is_array($items)) {
            $this->json(['error' => 'Carrito inválido'], 422);
        }

        try {
            $saleId = (new SaleService())->complete(
                $items,
                (float) ($data['discount'] ?? 0),
                $data['payment_method'] ?? 'efectivo',
                null,
                $this->userId()
            );
            $this->json(['success' => true, 'sale_id' => $saleId, 'redirect' => url('/pos/ticket/' . $saleId)]);
        } catch (\Throwable $e) {
            $this->json(['error' => $e->getMessage()], 422);
        }
    }

    public function ticket(array $params): void
    {
        $db = \App\Core\Database::connection();
        $stmt = $db->prepare(
            'SELECT s.*, u.name AS seller_name FROM sales s
             LEFT JOIN users u ON u.id = s.user_id
             WHERE s.id = :id AND s.tenant_id = :tenant_id LIMIT 1'
        );
        $stmt->execute(['id' => (int) $params['id'], 'tenant_id' => $this->tenantId()]);
        $sale = $stmt->fetch();
        if (!$sale) {
            $this->redirect('/pos');
        }

        $items = $db->prepare('SELECT * FROM sale_items WHERE sale_id = :id AND tenant_id = :tenant_id');
        $items->execute(['id' => $sale['id'], 'tenant_id' => $this->tenantId()]);

        $this->view('pos/ticket', [
            'title' => 'Ticket',
            'sale' => $sale,
            'items' => $items->fetchAll(),
            'tenantName' => Session::get('tenant_name'),
        ], 'layouts/print');
    }
}
