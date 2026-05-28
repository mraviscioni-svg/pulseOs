<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Models\ProductModel;
use App\Services\AuditService;
use App\Services\StockService;

final class ProductController extends Controller
{
    public function index(): void
    {
        $model = new ProductModel();
        $q = trim((string) ($_GET['q'] ?? ''));
        $this->view('products/index', [
            'title' => 'Productos',
            'products' => $model->search($this->tenantId(), $q ?: null),
            'q' => $q,
        ]);
    }

    public function create(): void
    {
        $this->view('products/form', ['title' => 'Nuevo producto', 'product' => null]);
    }

    public function store(): void
    {
        $data = $this->input();
        if (!$this->validateProduct($data)) {
            $this->redirect('/products/create');
        }

        $id = (new ProductModel())->create($this->tenantId(), $data);
        (new AuditService())->log($this->tenantId(), $this->userId(), 'product.created', 'product', $id);
        Session::flash('success', 'Producto creado.');
        $this->redirect('/products');
    }

    public function edit(array $params): void
    {
        $product = (new ProductModel())->find((int) $params['id']);
        if (!$product) {
            $this->redirect('/products');
        }
        $this->view('products/form', ['title' => 'Editar producto', 'product' => $product]);
    }

    public function update(array $params): void
    {
        $id = (int) $params['id'];
        $data = $this->input();
        if (!$this->validateProduct($data)) {
            $this->redirect('/products/' . $id . '/edit');
        }

        (new ProductModel())->update($this->tenantId(), $id, $data);
        Session::flash('success', 'Producto actualizado.');
        $this->redirect('/products');
    }

    public function adjustStock(array $params): void
    {
        $data = $this->input();
        try {
            (new StockService())->adjust(
                (int) $params['id'],
                (float) ($data['quantity'] ?? 0),
                $data['type'] ?? 'ajuste',
                $this->userId(),
                notes: $data['notes'] ?? null
            );
            Session::flash('success', 'Stock actualizado.');
        } catch (\Throwable $e) {
            Session::flash('error', $e->getMessage());
        }
        $this->redirect('/products');
    }

    /** @param array<string, mixed> $data */
    private function validateProduct(array $data): bool
    {
        $validator = new Validator();
        if (!$validator->validate($data, [
            'name' => 'required|min:2',
            'price' => 'required|numeric',
            'cost' => 'numeric',
        ])) {
            Session::flash('error', implode(' ', $validator->errors()));
            Session::set('_old', $data);

            return false;
        }

        return true;
    }
}
