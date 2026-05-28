<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\BrandModel;
use App\Models\CategoryModel;

final class CategoryController extends Controller
{
    public function index(): void
    {
        $tenantId = $this->tenantId();
        $this->view('categories/index', [
            'title' => 'Categorías y marcas',
            'categories' => (new CategoryModel())->allForTenant($tenantId, 'name ASC'),
            'brands' => (new BrandModel())->allForTenant($tenantId, 'name ASC'),
        ]);
    }

    public function storeCategory(): void
    {
        $data = $this->input();
        (new CategoryModel())->create($this->tenantId(), $data);
        Session::flash('success', 'Categoría creada.');
        $this->redirect('/categories');
    }

    public function storeBrand(): void
    {
        $data = $this->input();
        (new BrandModel())->create($this->tenantId(), $data);
        Session::flash('success', 'Marca creada.');
        $this->redirect('/categories');
    }

    public function deleteCategory(array $params): void
    {
        $db = \App\Core\Database::connection();
        $db->prepare('DELETE FROM product_categories WHERE id = :id AND tenant_id = :t')
            ->execute(['id' => (int) $params['id'], 't' => $this->tenantId()]);
        Session::flash('success', 'Categoría eliminada.');
        $this->redirect('/categories');
    }

    public function deleteBrand(array $params): void
    {
        $db = \App\Core\Database::connection();
        $db->prepare('DELETE FROM brands WHERE id = :id AND tenant_id = :t')
            ->execute(['id' => (int) $params['id'], 't' => $this->tenantId()]);
        Session::flash('success', 'Marca eliminada.');
        $this->redirect('/categories');
    }
}
