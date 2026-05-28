<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\BrandModel;
use App\Models\CategoryModel;
use App\Services\BusinessCatalogService;

final class CategoryController extends Controller
{
    public function index(): void
    {
        $tenantId = $this->tenantId();
        $businessType = (string) Session::get('business_type', 'otro');
        $catalog = new BusinessCatalogService();

        $categories = (new CategoryModel())->allForTenant($tenantId, 'name ASC');
        $brands = (new BrandModel())->allForTenant($tenantId, 'name ASC');

        $this->view('categories/index', [
            'title' => 'Categorías y marcas',
            'categories' => $categories,
            'brands' => $brands,
            'businessType' => $businessType,
            'businessTypeLabel' => config('business_types')[$businessType]['label'] ?? $businessType,
            'suggestedCategories' => $catalog->suggestionsNotInTenant(
                $catalog->categoriesForType($businessType),
                $categories
            ),
            'suggestedBrands' => $catalog->suggestionsNotInTenant(
                $catalog->brandsForType($businessType),
                $brands
            ),
        ]);
    }

    public function storeCategory(): void
    {
        $this->storeItem('category');
    }

    public function storeBrand(): void
    {
        $this->storeItem('brand');
    }

    public function importCategory(): void
    {
        $name = trim((string) ($this->input()['name'] ?? ''));
        $this->storeItem('category', $name);
    }

    public function importBrand(): void
    {
        $name = trim((string) ($this->input()['name'] ?? ''));
        $this->storeItem('brand', $name);
    }

    private function storeItem(string $type, ?string $presetName = null): void
    {
        $data = $this->input();
        $name = $presetName ?? trim((string) ($data['name'] ?? ''));
        if ($name === '') {
            Session::flash('error', 'El nombre es obligatorio.');
            $this->redirect('/categories');
        }

        $tenantId = $this->tenantId();
        $businessType = (string) Session::get('business_type', 'otro');
        $catalog = new BusinessCatalogService();

        if ($type === 'category') {
            if ((new CategoryModel())->existsByName($tenantId, $name)) {
                Session::flash('error', 'Ya existe esa categoría.');
                $this->redirect('/categories');
            }
            (new CategoryModel())->create($tenantId, [
                'name' => $name,
                'description' => $data['description'] ?? null,
            ]);
            $catalog->rememberCategory($businessType, $name, $data['description'] ?? null);
            Session::flash('success', 'Categoría agregada al comercio y al catálogo del rubro.');
        } else {
            if ((new BrandModel())->existsByName($tenantId, $name)) {
                Session::flash('error', 'Ya existe esa marca.');
                $this->redirect('/categories');
            }
            (new BrandModel())->create($tenantId, ['name' => $name]);
            $catalog->rememberBrand($businessType, $name);
            Session::flash('success', 'Marca agregada al comercio y al catálogo del rubro.');
        }

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
