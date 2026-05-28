<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Concerns\ExportableList;
use App\Core\Controller;
use App\Core\Session;
use App\Models\ProductModel;
use App\Models\PurchaseModel;
use App\Models\SupplierModel;
use App\Services\PurchaseService;

final class PurchaseController extends Controller
{
    use ExportableList;

    public function index(): void
    {
        $q = trim((string) ($_GET['q'] ?? ''));
        $status = (string) ($_GET['status'] ?? 'all');

        $purchases = (new PurchaseModel())->listWithSupplier(
            $this->tenantId(),
            $q !== '' ? $q : null,
            $status !== 'all' ? $status : null
        );

        $rows = [];
        foreach ($purchases as $p) {
            $rows[] = [
                '#' . $p['id'],
                $p['supplier_name'] ?? '',
                $p['total'],
                $p['status'],
                $p['created_at'],
            ];
        }

        $this->maybeExportList(
            'Compras',
            ['Ref', 'Proveedor', 'Total', 'Estado', 'Fecha'],
            $rows,
            'compras'
        );

        $this->view('purchases/index', [
            'title' => 'Compras',
            'purchases' => $purchases,
            'q' => $q,
            'status' => $status,
        ]);
    }

    public function create(): void
    {
        $this->view('purchases/form', [
            'title' => 'Nueva compra',
            'suppliers' => (new SupplierModel())->search($this->tenantId(), null, 'active'),
            'products' => (new ProductModel())->search($this->tenantId(), null, 200, 'active'),
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
        (new PurchaseService())->receive($this->tenantId(), (int) $params['id'], $this->userId());
        Session::flash('success', 'Compra recibida y stock actualizado.');
        $this->redirect('/purchases');
    }
}
