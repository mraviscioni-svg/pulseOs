<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Concerns\ExportableList;
use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Models\SupplierModel;

final class SupplierController extends Controller
{
    use ExportableList;

    public function index(): void
    {
        $this->renderIndex();
    }

    public function create(): void
    {
        $this->renderIndex([
            'modal' => $this->modalMeta('Nuevo proveedor', 'Los datos se guardan en tu tenant actual.'),
            'supplier' => null,
        ]);
    }

    public function store(): void
    {
        $data = $this->input();
        $data['is_active'] = 1;
        if (!$this->validate($data)) {
            Session::set('_old', $data);
            $this->redirect('/suppliers/create');
        }
        (new SupplierModel())->create($this->tenantId(), $data);
        Session::flash('success', 'Proveedor creado.');
        $this->redirect('/suppliers');
    }

    public function edit(array $params): void
    {
        $supplier = (new SupplierModel())->find((int) $params['id']);
        if (!$supplier) {
            $this->redirect('/suppliers');
        }
        $this->renderIndex([
            'modal' => $this->modalMeta('Editar proveedor', 'Los datos se guardan en tu tenant actual.'),
            'supplier' => $supplier,
        ]);
    }

    public function update(array $params): void
    {
        $data = $this->input();
        if (!$this->validate($data)) {
            Session::set('_old', $data);
            $this->redirect('/suppliers/' . $params['id'] . '/edit');
        }
        (new SupplierModel())->update($this->tenantId(), (int) $params['id'], $data);
        Session::flash('success', 'Proveedor actualizado.');
        $this->redirect('/suppliers');
    }

    public function toggle(array $params): void
    {
        $active = !empty($this->input()['is_active']);
        (new SupplierModel())->setActive($this->tenantId(), (int) $params['id'], $active ? 1 : 0);
        Session::flash('success', $active ? 'Proveedor activado.' : 'Proveedor desactivado.');
        $this->redirect('/suppliers');
    }

    public function delete(array $params): void
    {
        try {
            (new SupplierModel())->deleteForTenant($this->tenantId(), (int) $params['id']);
            Session::flash('success', 'Proveedor eliminado.');
        } catch (\Throwable $e) {
            Session::flash('error', 'No se pudo eliminar: ' . $e->getMessage());
        }
        $this->redirect('/suppliers');
    }

    /** @param array<string, mixed> $extra */
    private function renderIndex(array $extra = []): void
    {
        $q = trim((string) ($_GET['q'] ?? ''));
        $status = (string) ($_GET['status'] ?? 'all');
        if (!in_array($status, ['all', 'active', 'inactive'], true)) {
            $status = 'all';
        }

        $suppliers = (new SupplierModel())->search(
            $this->tenantId(),
            $q !== '' ? $q : null,
            $status === 'all' ? null : $status
        );

        $rows = [];
        foreach ($suppliers as $s) {
            $rows[] = [
                $s['name'],
                $s['company'] ?? '',
                $s['tax_id'] ?? '',
                $s['phone'] ?? '',
                $s['email'] ?? '',
                $s['is_active'] ? 'Activo' : 'Inactivo',
            ];
        }

        $this->maybeExportList(
            'Proveedores',
            ['Nombre', 'Empresa', 'CUIT', 'Teléfono', 'Email', 'Estado'],
            $rows,
            'proveedores'
        );

        $this->view('suppliers/index', array_merge([
            'title' => 'Proveedores',
            'suppliers' => $suppliers,
            'q' => $q,
            'status' => $status,
        ], $extra));
    }

    /** @return array{title: string, subtitle: string, closeUrl: string} */
    private function modalMeta(string $title, string $subtitle): array
    {
        return [
            'title' => $title,
            'subtitle' => $subtitle,
            'closeUrl' => url('/suppliers'),
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
