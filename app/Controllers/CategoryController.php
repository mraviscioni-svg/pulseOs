<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Concerns\ManagesCatalogSuggestions;
use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Models\CategoryModel;
use App\Services\BusinessCatalogService;

final class CategoryController extends Controller
{
    use ManagesCatalogSuggestions;

    public function index(): void
    {
        $this->renderIndex();
    }

    public function create(): void
    {
        $this->renderIndex([
            'modal' => $this->modalMeta('Nueva categoría', 'Organizá tus productos por tipo o familia.'),
            'category' => null,
        ]);
    }

    public function store(): void
    {
        $data = $this->input();
        if (!$this->validate($data)) {
            Session::set('_old', $data);
            $this->redirect('/categories/create');
        }

        $tenantId = $this->tenantId();
        $name = trim((string) $data['name']);
        if ((new CategoryModel())->existsByName($tenantId, $name)) {
            Session::flash('error', 'Ya existe una categoría con ese nombre.');
            Session::set('_old', $data);
            $this->redirect('/categories/create');
        }

        $businessType = (string) Session::get('business_type', 'otro');
        (new CategoryModel())->create($tenantId, [
            'name' => $name,
            'description' => trim((string) ($data['description'] ?? '')) ?: null,
        ]);
        (new BusinessCatalogService())->rememberCategory(
            $businessType,
            $name,
            trim((string) ($data['description'] ?? '')) ?: null
        );

        Session::flash('success', 'Categoría creada.');
        $this->redirect('/categories');
    }

    public function edit(array $params): void
    {
        $category = (new CategoryModel())->find((int) $params['id'], $this->tenantId());
        if (!$category) {
            $this->redirect('/categories');
        }

        $this->renderIndex([
            'modal' => $this->modalMeta('Editar categoría', 'Los cambios aplican a los productos que uses de ahora en adelante.'),
            'category' => $category,
        ]);
    }

    public function update(array $params): void
    {
        $id = (int) $params['id'];
        $data = $this->input();
        if (!$this->validate($data)) {
            Session::set('_old', $data);
            $this->redirect('/categories/' . $id . '/edit');
        }

        $tenantId = $this->tenantId();
        $name = trim((string) $data['name']);
        if ((new CategoryModel())->existsByName($tenantId, $name, $id)) {
            Session::flash('error', 'Ya existe una categoría con ese nombre.');
            Session::set('_old', $data);
            $this->redirect('/categories/' . $id . '/edit');
        }

        (new CategoryModel())->update($tenantId, $id, [
            'name' => $name,
            'description' => trim((string) ($data['description'] ?? '')) ?: null,
        ]);
        (new BusinessCatalogService())->rememberCategory(
            (string) Session::get('business_type', 'otro'),
            $name,
            trim((string) ($data['description'] ?? '')) ?: null
        );

        Session::flash('success', 'Categoría actualizada.');
        $this->redirect('/categories');
    }

    public function delete(array $params): void
    {
        (new CategoryModel())->deleteForTenant($this->tenantId(), (int) $params['id']);
        Session::flash('success', 'Categoría eliminada.');
        $this->redirect('/categories');
    }

    public function import(): void
    {
        $name = trim((string) ($this->input()['name'] ?? ''));
        if ($name === '') {
            Session::flash('error', 'Nombre inválido.');
            $this->redirect('/categories');
        }

        $tenantId = $this->tenantId();
        if ((new CategoryModel())->existsByName($tenantId, $name)) {
            Session::flash('error', 'Esa categoría ya está en tu comercio.');
            $this->redirect('/categories');
        }

        $catalog = new BusinessCatalogService();
        $businessType = (string) Session::get('business_type', 'otro');
        $row = $this->findCatalogCategory($catalog, $businessType, $name);

        (new CategoryModel())->create($tenantId, [
            'name' => $name,
            'description' => $row['description'] ?? null,
        ]);
        $catalog->rememberCategory($businessType, $name, $row['description'] ?? null);

        Session::flash('success', 'Categoría «' . $name . '» agregada.');
        $this->redirect('/categories');
    }

    public function resetSuggestions(): void
    {
        $this->resetCatalogSuggestions('category');
        Session::flash('success', 'Sugerencias del rubro restauradas.');
        $this->redirect('/categories');
    }

    public function dismissSuggestions(): void
    {
        $this->dismissSuggestions('category');
        $this->redirect('/categories');
    }

    /** @param array<string, mixed> $extra */
    private function renderIndex(array $extra = []): void
    {
        $q = trim((string) ($_GET['q'] ?? ''));
        $categories = (new CategoryModel())->search($this->tenantId(), $q !== '' ? $q : null);
        $suggestionCtx = $this->suggestionContext('category', $categories);

        $this->view('categories/index', array_merge([
            'title' => 'Categorías',
            'categories' => $categories,
            'q' => $q,
            'suggestedCategories' => $suggestionCtx['suggestions'],
            'businessTypeLabel' => $suggestionCtx['businessTypeLabel'],
            'suggestionsHidden' => $suggestionCtx['hidden'],
            'brandsUrl' => url('/brands'),
        ], $extra));
    }

    /** @return array{title: string, subtitle: string, closeUrl: string} */
    private function modalMeta(string $title, string $subtitle): array
    {
        return [
            'title' => $title,
            'subtitle' => $subtitle,
            'closeUrl' => url('/categories'),
        ];
    }

    /** @param array<string, mixed> $data */
    private function validate(array $data): bool
    {
        $v = new Validator();
        if (!$v->validate($data, ['name' => 'required|min:2'])) {
            Session::flash('error', implode(' ', $v->errors()));

            return false;
        }

        return true;
    }

    /** @return array<string, mixed> */
    private function findCatalogCategory(BusinessCatalogService $catalog, string $businessType, string $name): array
    {
        foreach ($catalog->categoriesForType($businessType) as $row) {
            if (mb_strtolower((string) $row['name'], 'UTF-8') === mb_strtolower($name, 'UTF-8')) {
                return $row;
            }
        }

        return [];
    }
}
