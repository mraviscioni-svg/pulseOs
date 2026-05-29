<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Concerns\ManagesCatalogSuggestions;
use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Models\BrandModel;
use App\Services\BusinessCatalogService;

final class BrandController extends Controller
{
    use ManagesCatalogSuggestions;

    public function index(): void
    {
        $this->renderIndex();
    }

    public function create(): void
    {
        $this->renderIndex([
            'modal' => $this->modalMeta('Nueva marca', 'Etiquetá productos por fabricante o línea.'),
            'brand' => null,
        ]);
    }

    public function store(): void
    {
        $data = $this->input();
        if (!$this->validate($data)) {
            Session::set('_old', $data);
            $this->redirect('/brands/create');
        }

        $tenantId = $this->tenantId();
        $name = trim((string) $data['name']);
        if ((new BrandModel())->existsByName($tenantId, $name)) {
            Session::flash('error', 'Ya existe una marca con ese nombre.');
            Session::set('_old', $data);
            $this->redirect('/brands/create');
        }

        (new BrandModel())->create($tenantId, ['name' => $name]);
        (new BusinessCatalogService())->rememberBrand((string) Session::get('business_type', 'otro'), $name);

        Session::flash('success', 'Marca creada.');
        $this->redirect('/brands');
    }

    public function edit(array $params): void
    {
        $brand = (new BrandModel())->find((int) $params['id'], $this->tenantId());
        if (!$brand) {
            $this->redirect('/brands');
        }

        $this->renderIndex([
            'modal' => $this->modalMeta('Editar marca', 'El nombre se actualiza en el catálogo del comercio.'),
            'brand' => $brand,
        ]);
    }

    public function update(array $params): void
    {
        $id = (int) $params['id'];
        $data = $this->input();
        if (!$this->validate($data)) {
            Session::set('_old', $data);
            $this->redirect('/brands/' . $id . '/edit');
        }

        $tenantId = $this->tenantId();
        $name = trim((string) $data['name']);
        if ((new BrandModel())->existsByName($tenantId, $name, $id)) {
            Session::flash('error', 'Ya existe una marca con ese nombre.');
            Session::set('_old', $data);
            $this->redirect('/brands/' . $id . '/edit');
        }

        (new BrandModel())->update($tenantId, $id, ['name' => $name]);
        (new BusinessCatalogService())->rememberBrand((string) Session::get('business_type', 'otro'), $name);

        Session::flash('success', 'Marca actualizada.');
        $this->redirect('/brands');
    }

    public function delete(array $params): void
    {
        (new BrandModel())->deleteForTenant($this->tenantId(), (int) $params['id']);
        Session::flash('success', 'Marca eliminada.');
        $this->redirect('/brands');
    }

    public function import(): void
    {
        $name = trim((string) ($this->input()['name'] ?? ''));
        if ($name === '') {
            Session::flash('error', 'Nombre inválido.');
            $this->redirect('/brands');
        }

        $tenantId = $this->tenantId();
        if ((new BrandModel())->existsByName($tenantId, $name)) {
            Session::flash('error', 'Esa marca ya está en tu comercio.');
            $this->redirect('/brands');
        }

        (new BrandModel())->create($tenantId, ['name' => $name]);
        (new BusinessCatalogService())->rememberBrand((string) Session::get('business_type', 'otro'), $name);

        Session::flash('success', 'Marca «' . $name . '» agregada.');
        $this->redirect('/brands');
    }

    public function resetSuggestions(): void
    {
        $this->resetCatalogSuggestions('brand');
        Session::flash('success', 'Sugerencias del rubro restauradas.');
        $this->redirect('/brands');
    }

    public function dismissSuggestions(): void
    {
        $this->dismissSuggestions('brand');
        $this->redirect('/brands');
    }

    /** @param array<string, mixed> $extra */
    private function renderIndex(array $extra = []): void
    {
        $q = trim((string) ($_GET['q'] ?? ''));
        $brands = (new BrandModel())->search($this->tenantId(), $q !== '' ? $q : null);
        $suggestionCtx = $this->suggestionContext('brand', $brands);

        $this->view('brands/index', array_merge([
            'title' => 'Marcas',
            'brands' => $brands,
            'q' => $q,
            'suggestedBrands' => $suggestionCtx['suggestions'],
            'businessTypeLabel' => $suggestionCtx['businessTypeLabel'],
            'suggestionsHidden' => $suggestionCtx['hidden'],
            'categoriesUrl' => url('/categories'),
        ], $extra));
    }

    /** @return array{title: string, subtitle: string, closeUrl: string} */
    private function modalMeta(string $title, string $subtitle): array
    {
        return [
            'title' => $title,
            'subtitle' => $subtitle,
            'closeUrl' => url('/brands'),
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
}
