<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Models\SupplierModel;

final class SupplierController extends Controller
{
    public function index(): void
    {
        $this->view('suppliers/index', [
            'title' => 'Proveedores',
            'suppliers' => (new SupplierModel())->allForTenant($this->tenantId()),
        ]);
    }

    public function create(): void
    {
        $this->view('suppliers/form', ['title' => 'Nuevo proveedor', 'supplier' => null]);
    }

    public function store(): void
    {
        $data = $this->input();
        if (!$this->validate($data)) {
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
        $this->view('suppliers/form', ['title' => 'Editar proveedor', 'supplier' => $supplier]);
    }

    public function update(array $params): void
    {
        $data = $this->input();
        if (!$this->validate($data)) {
            $this->redirect('/suppliers/' . $params['id'] . '/edit');
        }
        (new SupplierModel())->update($this->tenantId(), (int) $params['id'], $data);
        Session::flash('success', 'Proveedor actualizado.');
        $this->redirect('/suppliers');
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
