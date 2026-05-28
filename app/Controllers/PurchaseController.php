<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\ProductModel;
use App\Models\PurchaseModel;
use App\Models\SupplierModel;
use App\Services\PurchaseService;

final class PurchaseController extends Controller
{
    public function index(): void
    {
        $this->view('purchases/index', [
            'title' => 'Compras',
            'purchases' => (new PurchaseModel())->listWithSupplier($this->tenantId()),
        ]);
    }

    public function create(): void
    {
        $this->view('purchases/form', [
            'title' => 'Nueva compra',
            'suppliers' => (new SupplierModel())->allForTenant($this->tenantId()),
            'products' => (new ProductModel())->search($this->tenantId(), null, 200),
        ]);
    }

    public function store(): void
    {
        $data = $this->input();
        $items = json_decode($data['items_json'] ?? '[]', true);
        if (!is_array($items) || $items === []) {
            Session::flash('error', 'Agregá al menos un producto.');
            $this->redirect('/purchases/create');
        }

        (new PurchaseModel())->create(
            $this->tenantId(),
            $this->userId(),
            !empty($data['supplier_id']) ? (int) $data['supplier_id'] : null,
            $items,
            $data['notes'] ?? null
        );
        Session::flash('success', 'Orden de compra creada.');
        $this->redirect('/purchases');
    }

    public function receive(array $params): void
    {
        try {
            (new PurchaseService())->receive((int) $params['id'], $this->userId());
            Session::flash('success', 'Compra recibida y stock actualizado.');
        } catch (\Throwable $e) {
            Session::flash('error', $e->getMessage());
        }
        $this->redirect('/purchases');
    }
}
