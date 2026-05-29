<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Concerns\ExportableList;
use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Models\CustomerModel;
use App\Models\CustomerVehicleModel;
use App\Services\AuditService;

final class CustomerController extends Controller
{
    use ExportableList;

    public function index(): void
    {
        $this->renderIndex();
    }

    public function create(): void
    {
        $this->renderIndex([
            'modal' => $this->modalMeta('Nuevo cliente', 'Los datos se guardan en tu comercio actual.'),
            'customer' => null,
        ]);
    }

    public function store(): void
    {
        $data = $this->input();
        $data['is_active'] = 1;
        if (!$this->validate($data)) {
            Session::set('_old', $data);
            $this->redirect('/customers/create');
        }

        $id = (new CustomerModel())->create($this->tenantId(), $data);
        (new AuditService())->log($this->tenantId(), $this->userId(), 'customer.created', 'customer', $id);
        Session::flash('success', 'Cliente creado.');
        $this->redirect('/customers/' . $id);
    }

    public function show(array $params): void
    {
        $id = (int) $params['id'];
        $customer = (new CustomerModel())->findWithVehicles($this->tenantId(), $id);
        if (!$customer) {
            Session::flash('error', 'Cliente no encontrado.');
            $this->redirect('/customers');
        }

        $workOrders = [];
        if (module_enabled('work_orders')) {
            $workOrders = (new CustomerModel())->recentWorkOrders($this->tenantId(), $id);
        }

        $this->view('customers/show', [
            'title' => $customer['name'],
            'customer' => $customer,
            'workOrders' => $workOrders,
            'showVehicles' => module_enabled('work_orders'),
        ]);
    }

    public function edit(array $params): void
    {
        $customer = (new CustomerModel())->find((int) $params['id']);
        if (!$customer) {
            $this->redirect('/customers');
        }

        $this->renderIndex([
            'modal' => $this->modalMeta('Editar cliente', 'Los datos se guardan en tu comercio actual.'),
            'customer' => $customer,
        ]);
    }

    public function update(array $params): void
    {
        $id = (int) $params['id'];
        $data = $this->input();
        if (!$this->validate($data)) {
            Session::set('_old', $data);
            $this->redirect('/customers/' . $id . '/edit');
        }

        (new CustomerModel())->update($this->tenantId(), $id, $data);
        Session::flash('success', 'Cliente actualizado.');
        $this->redirect('/customers/' . $id);
    }

    public function toggle(array $params): void
    {
        $active = !empty($this->input()['is_active']);
        (new CustomerModel())->setActive($this->tenantId(), (int) $params['id'], $active ? 1 : 0);
        Session::flash('success', $active ? 'Cliente activado.' : 'Cliente desactivado.');
        $this->redirect('/customers');
    }

    public function delete(array $params): void
    {
        try {
            (new CustomerModel())->deleteForTenant($this->tenantId(), (int) $params['id']);
            Session::flash('success', 'Cliente eliminado.');
        } catch (\Throwable $e) {
            Session::flash('error', 'No se pudo eliminar: ' . $e->getMessage());
        }
        $this->redirect('/customers');
    }

    public function storeVehicle(array $params): void
    {
        $customerId = (int) $params['id'];
        $data = $this->input();
        $label = trim((string) ($data['label'] ?? ''));
        if ($label === '') {
            Session::flash('error', 'Indicá la patente o referencia del vehículo.');
            $this->redirect('/customers/' . $customerId);
        }

        $customer = (new CustomerModel())->find($customerId, $this->tenantId());
        if (!$customer) {
            $this->redirect('/customers');
        }

        (new CustomerVehicleModel())->create(
            $this->tenantId(),
            $customerId,
            $label,
            trim((string) ($data['description'] ?? '')) ?: null
        );
        Session::flash('success', 'Vehículo agregado.');
        $this->redirect('/customers/' . $customerId);
    }

    public function deleteVehicle(array $params): void
    {
        $customerId = (int) $params['id'];
        $vehicleId = (int) $params['vehicle_id'];

        try {
            (new CustomerVehicleModel())->deleteForCustomer($this->tenantId(), $customerId, $vehicleId);
            Session::flash('success', 'Vehículo eliminado.');
        } catch (\Throwable $e) {
            Session::flash('error', $e->getMessage());
        }

        $this->redirect('/customers/' . $customerId);
    }

    /** @param array<string, mixed> $extra */
    private function renderIndex(array $extra = []): void
    {
        $q = trim((string) ($_GET['q'] ?? ''));
        $status = (string) ($_GET['status'] ?? 'all');
        if (!in_array($status, ['all', 'active', 'inactive'], true)) {
            $status = 'all';
        }

        $customers = (new CustomerModel())->search(
            $this->tenantId(),
            $q !== '' ? $q : null,
            $status === 'all' ? null : $status
        );

        $rows = [];
        foreach ($customers as $c) {
            $rows[] = [
                $c['name'],
                $c['company'] ?? '',
                $c['tax_id'] ?? '',
                $c['phone'] ?? '',
                $c['email'] ?? '',
                $c['is_active'] ? 'Activo' : 'Inactivo',
            ];
        }

        $this->maybeExportList(
            'Clientes',
            ['Nombre', 'Empresa', 'CUIT/DNI', 'Teléfono', 'Email', 'Estado'],
            $rows,
            'clientes'
        );

        $this->view('customers/index', array_merge([
            'title' => 'Clientes',
            'customers' => $customers,
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
            'closeUrl' => url('/customers'),
        ];
    }

    /** @param array<string, mixed> $data */
    private function validate(array $data): bool
    {
        $v = new Validator($data);
        $v->required('name', 'Nombre');

        if ($v->fails()) {
            Session::flash('error', $v->firstError());

            return false;
        }

        return true;
    }
}
