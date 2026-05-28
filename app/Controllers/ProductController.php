<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Concerns\ExportableList;
use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Models\BrandModel;
use App\Models\CategoryModel;
use App\Models\ProductModel;
use App\Models\ProductVariantModel;
use App\Models\SupplierModel;
use App\Services\AuditService;
use App\Services\StockService;
use App\Services\UploadService;

final class ProductController extends Controller
{
    use ExportableList;

    public function index(): void
    {
        $q = trim((string) ($_GET['q'] ?? ''));
        $status = (string) ($_GET['status'] ?? 'all');
        if (!in_array($status, ['all', 'active', 'inactive'], true)) {
            $status = 'all';
        }

        $products = (new ProductModel())->search(
            $this->tenantId(),
            $q !== '' ? $q : null,
            500,
            $status === 'all' ? null : $status
        );

        $rows = [];
        foreach ($products as $p) {
            $rows[] = [
                $p['name'],
                $p['sku'] ?? '',
                $p['barcode'] ?? '',
                $p['category_name'] ?? '',
                $p['stock'],
                $p['price'],
                $p['is_active'] ? 'Activo' : 'Inactivo',
            ];
        }

        $this->maybeExportList(
            'Productos',
            ['Nombre', 'SKU', 'Código', 'Categoría', 'Stock', 'Precio', 'Estado'],
            $rows,
            'productos'
        );

        $this->view('products/index', [
            'title' => 'Productos',
            'products' => $products,
            'q' => $q,
            'status' => $status,
        ]);
    }

    public function create(): void
    {
        $this->formView('Nuevo producto', null);
    }

    public function store(): void
    {
        $data = $_POST;
        if (!$this->validateProduct($data)) {
            $this->redirect('/products/create');
        }

        try {
            if (!empty($_FILES['image'])) {
                $data['image_path'] = (new UploadService())->storeImage($_FILES['image']);
            }
            $data['is_active'] = 1;
            $id = (new ProductModel())->create($this->tenantId(), $data);
            (new AuditService())->log($this->tenantId(), $this->userId(), 'product.created', 'product', $id);
            Session::flash('success', 'Producto creado.');
            $this->redirect('/products/' . $id . '/edit');
        } catch (\Throwable $e) {
            Session::flash('error', $e->getMessage());
            $this->redirect('/products/create');
        }
    }

    public function edit(array $params): void
    {
        $product = (new ProductModel())->find((int) $params['id']);
        if (!$product) {
            $this->redirect('/products');
        }
        $this->formView('Editar producto', $product);
    }

    public function update(array $params): void
    {
        $id = (int) $params['id'];
        $data = $_POST;
        if (!$this->validateProduct($data)) {
            $this->redirect('/products/' . $id . '/edit');
        }

        try {
            if (!empty($_FILES['image']['name'])) {
                $old = (new ProductModel())->find($id);
                $data['image_path'] = (new UploadService())->storeImage($_FILES['image']);
                (new UploadService())->delete($old['image_path'] ?? null);
            }
            (new ProductModel())->update($this->tenantId(), $id, $data);
            Session::flash('success', 'Producto actualizado.');
        } catch (\Throwable $e) {
            Session::flash('error', $e->getMessage());
        }
        $this->redirect('/products/' . $id . '/edit');
    }

    public function toggle(array $params): void
    {
        $active = !empty($this->input()['is_active']);
        (new ProductModel())->setActive($this->tenantId(), (int) $params['id'], $active ? 1 : 0);
        Session::flash('success', $active ? 'Producto activado.' : 'Producto desactivado.');
        $this->redirect('/products');
    }

    public function delete(array $params): void
    {
        try {
            (new ProductModel())->deleteForTenant($this->tenantId(), (int) $params['id']);
            Session::flash('success', 'Producto eliminado.');
        } catch (\Throwable $e) {
            Session::flash('error', 'No se pudo eliminar: ' . $e->getMessage());
        }
        $this->redirect('/products');
    }

    public function storeVariant(array $params): void
    {
        $productId = (int) $params['id'];
        $data = $this->input();
        $attrs = [];
        foreach (['talle', 'color', 'modelo', 'sabor', 'medida'] as $key) {
            if (!empty($data[$key])) {
                $attrs[$key] = $data[$key];
            }
        }
        $data['attributes'] = $attrs;
        (new ProductVariantModel())->create($this->tenantId(), $productId, $data);
        $db = \App\Core\Database::connection();
        $db->prepare('UPDATE products SET has_variants = 1 WHERE id = :id AND tenant_id = :t')
            ->execute(['id' => $productId, 't' => $this->tenantId()]);
        Session::flash('success', 'Variante agregada.');
        $this->redirect('/products/' . $productId . '/edit');
    }

    public function deleteVariant(array $params): void
    {
        (new ProductVariantModel())->delete($this->tenantId(), (int) $params['variant_id']);
        Session::flash('success', 'Variante eliminada.');
        $this->redirect('/products/' . $params['id'] . '/edit');
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
        $this->redirect('/products/' . $params['id'] . '/edit');
    }

    /** @param array<string, mixed>|null $product */
    private function formView(string $title, ?array $product): void
    {
        $tenantId = $this->tenantId();
        $variants = $product
            ? (new ProductVariantModel())->forProduct($tenantId, (int) $product['id'])
            : [];

        $this->view('products/form', [
            'title' => $title,
            'product' => $product,
            'variants' => $variants,
            'categories' => (new CategoryModel())->allForTenant($tenantId, 'name ASC'),
            'brands' => (new BrandModel())->allForTenant($tenantId, 'name ASC'),
            'suppliers' => (new SupplierModel())->search($tenantId, null, 'active'),
        ]);
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
