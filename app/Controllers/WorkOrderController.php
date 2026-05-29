<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Concerns\ExportableList;
use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Models\CustomerModel;
use App\Models\CustomerVehicleModel;
use App\Models\ProductModel;
use App\Models\UserModel;
use App\Models\WorkOrderModel;
use App\Services\WorkOrderService;

final class WorkOrderController extends Controller
{
    use ExportableList;

    public function index(): void
    {
        $q = trim((string) ($_GET['q'] ?? ''));
        $status = (string) ($_GET['status'] ?? 'all');
        $assigned = !empty($_GET['assigned']) ? (int) $_GET['assigned'] : null;

        $orders = (new WorkOrderModel())->listForTenant(
            $this->tenantId(),
            $q !== '' ? $q : null,
            $status !== 'all' ? $status : null,
            $assigned
        );

        $statusLabels = config('work_order_statuses')['labels'] ?? [];
        $rows = [];
        foreach ($orders as $o) {
            $rows[] = [
                $o['order_number'],
                $o['customer_name'],
                $o['vehicle_label'] ?? '',
                $statusLabels[$o['status']] ?? $o['status'],
                $o['assigned_name'] ?? '',
                $o['total'],
                $o['created_at'],
            ];
        }

        $this->maybeExportList(
            'Órdenes de trabajo',
            ['Nº OT', 'Cliente', 'Vehículo', 'Estado', 'Técnico', 'Total', 'Fecha'],
            $rows,
            'ordenes-trabajo'
        );

        $this->view('work_orders/index', [
            'title' => 'Órdenes de trabajo',
            'orders' => $orders,
            'q' => $q,
            'status' => $status,
            'assigned' => $assigned,
            'users' => (new UserModel())->listForTenant($this->tenantId(), null, 'active'),
            'statusLabels' => $statusLabels,
            'statusOptions' => $this->statusFilterOptions(),
        ]);
    }

    public function create(): void
    {
        $prefill = null;
        $customerId = (int) ($_GET['customer_id'] ?? 0);
        if ($customerId > 0 && module_enabled('customers')) {
            $prefill = (new CustomerModel())->findWithVehicles($this->tenantId(), $customerId);
        }

        $this->view('work_orders/form', $this->formViewData(null, [], $prefill));
    }

    public function store(): void
    {
        $header = $this->headerFromInput();
        if (!$this->validateHeader($header)) {
            $this->redirect('/work-orders/create');
        }

        $lines = $this->linesFromInput();
        if ($lines === []) {
            Session::flash('error', 'Agregá al menos un ítem (mano de obra o repuesto).');
            $this->redirect('/work-orders/create');
        }

        try {
            $id = (new WorkOrderService())->create(
                $this->tenantId(),
                $this->userId(),
                $header,
                $lines
            );
            Session::flash('success', 'Orden de trabajo creada.');
            $this->redirect('/work-orders/' . $id);
        } catch (\Throwable $e) {
            Session::flash('error', $e->getMessage());
            $this->redirect('/work-orders/create');
        }
    }

    public function show(array $params): void
    {
        $id = (int) $params['id'];
        $model = new WorkOrderModel();
        $order = $model->findDetailed($this->tenantId(), $id);
        if (!$order) {
            Session::flash('error', 'Orden no encontrada.');
            $this->redirect('/work-orders');
        }

        $statusConfig = config('work_order_statuses');
        $this->view('work_orders/show', [
            'title' => $order['order_number'],
            'order' => $order,
            'lines' => $model->linesForOrder($this->tenantId(), $id),
            'history' => $model->statusHistory($this->tenantId(), $id),
            'statusLabels' => $statusConfig['labels'] ?? [],
            'statusBadges' => $statusConfig['badge'] ?? [],
            'statusActions' => $statusConfig['actions'][$order['status']] ?? [],
            'editable' => in_array($order['status'], $statusConfig['editable'] ?? [], true),
        ]);
    }

    public function edit(array $params): void
    {
        $id = (int) $params['id'];
        $model = new WorkOrderModel();
        $order = $model->findDetailed($this->tenantId(), $id);
        if (!$order) {
            $this->redirect('/work-orders');
        }

        $editable = config('work_order_statuses')['editable'] ?? [];
        if (!in_array($order['status'], $editable, true)) {
            Session::flash('error', 'Esta orden no se puede editar.');
            $this->redirect('/work-orders/' . $id);
        }

        $this->view('work_orders/form', $this->formViewData($order, $model->linesForOrder($this->tenantId(), $id)));
    }

    public function update(array $params): void
    {
        $id = (int) $params['id'];
        $header = $this->headerFromInput();
        if (!$this->validateHeader($header)) {
            $this->redirect('/work-orders/' . $id . '/edit');
        }

        $lines = $this->linesFromInput();
        if ($lines === []) {
            Session::flash('error', 'Agregá al menos un ítem.');
            $this->redirect('/work-orders/' . $id . '/edit');
        }

        try {
            (new WorkOrderService())->update($this->tenantId(), $this->userId(), $id, $header, $lines);
            Session::flash('success', 'Orden actualizada.');
            $this->redirect('/work-orders/' . $id);
        } catch (\Throwable $e) {
            Session::flash('error', $e->getMessage());
            $this->redirect('/work-orders/' . $id . '/edit');
        }
    }

    public function status(array $params): void
    {
        $id = (int) $params['id'];
        $data = $this->input();
        $to = (string) ($data['status'] ?? '');

        try {
            (new WorkOrderService())->transition(
                $this->tenantId(),
                $this->userId(),
                $id,
                $to,
                !empty($data['note']) ? (string) $data['note'] : null
            );
            Session::flash('success', 'Estado actualizado.');
        } catch (\Throwable $e) {
            Session::flash('error', $e->getMessage());
        }

        $this->redirect('/work-orders/' . $id);
    }

    public function consume(array $params): void
    {
        $id = (int) $params['id'];

        try {
            $n = (new WorkOrderService())->consumeParts($this->tenantId(), $this->userId(), $id);
            Session::flash('success', $n > 0 ? "Stock descontado en {$n} repuesto(s)." : 'No había repuestos pendientes de consumir.');
        } catch (\Throwable $e) {
            Session::flash('error', $e->getMessage());
        }

        $this->redirect('/work-orders/' . $id);
    }

    public function charge(array $params): void
    {
        $id = (int) $params['id'];
        $data = $this->input();

        try {
            $saleId = (new WorkOrderService())->charge(
                $this->tenantId(),
                $this->userId(),
                $id,
                (string) ($data['payment_method'] ?? 'efectivo')
            );
            Session::flash('success', 'Cobro registrado.');
            $this->redirect('/pos/ticket/' . $saleId);
        } catch (\Throwable $e) {
            Session::flash('error', $e->getMessage());
            $this->redirect('/work-orders/' . $id);
        }
    }

    public function close(array $params): void
    {
        $id = (int) $params['id'];
        $data = $this->input();

        try {
            (new WorkOrderService())->closeWithoutSale(
                $this->tenantId(),
                $this->userId(),
                $id,
                !empty($data['note']) ? (string) $data['note'] : null
            );
            Session::flash('success', 'Orden cerrada.');
        } catch (\Throwable $e) {
            Session::flash('error', $e->getMessage());
        }

        $this->redirect('/work-orders/' . $id);
    }

    /** @param array<string, mixed>|null $prefillCustomer */
    /** @return array<string, mixed> */
    private function formViewData(?array $order, array $lines = [], ?array $prefillCustomer = null): array
    {
        $vehicles = [];
        if ($order && !empty($order['customer_id']) && module_enabled('customers')) {
            $vehicles = (new CustomerVehicleModel())->forCustomer(
                $this->tenantId(),
                (int) $order['customer_id'],
                true
            );
        } elseif ($prefillCustomer) {
            $vehicles = $prefillCustomer['vehicles'] ?? [];
        }

        return [
            'title' => $order ? 'Editar ' . $order['order_number'] : 'Nueva orden de trabajo',
            'order' => $order,
            'lines' => $lines,
            'products' => (new ProductModel())->search($this->tenantId(), null, 300, 'active'),
            'users' => (new UserModel())->listForTenant($this->tenantId(), null, 'active'),
            'customersEnabled' => module_enabled('customers') && can('customers.manage'),
            'prefillCustomer' => $prefillCustomer,
            'customerVehicles' => $vehicles,
        ];
    }

    /** @return array<string, string> */
    private function statusFilterOptions(): array
    {
        $labels = config('work_order_statuses')['labels'] ?? [];

        return array_merge(['all' => 'Todos'], $labels);
    }

    /** @return array<string, mixed> */
    private function headerFromInput(): array
    {
        $data = $this->input();

        return [
            'status' => 'borrador',
            'priority' => $data['priority'] ?? 'normal',
            'customer_id' => $data['customer_id'] ?? null,
            'customer_vehicle_id' => $data['customer_vehicle_id'] ?? null,
            'customer_name' => trim((string) ($data['customer_name'] ?? '')),
            'customer_phone' => trim((string) ($data['customer_phone'] ?? '')),
            'customer_email' => trim((string) ($data['customer_email'] ?? '')),
            'vehicle_label' => trim((string) ($data['vehicle_label'] ?? '')),
            'vehicle_notes' => trim((string) ($data['vehicle_notes'] ?? '')),
            'odometer_km' => $data['odometer_km'] ?? null,
            'reported_issue' => trim((string) ($data['reported_issue'] ?? '')),
            'assigned_user_id' => $data['assigned_user_id'] ?? null,
            'notes_internal' => trim((string) ($data['notes_internal'] ?? '')),
            'notes_customer' => trim((string) ($data['notes_customer'] ?? '')),
        ];
    }

    /** @return list<array<string, mixed>> */
    private function linesFromInput(): array
    {
        $raw = $this->input()['lines_json'] ?? '[]';
        $decoded = is_string($raw) ? json_decode($raw, true) : $raw;

        return is_array($decoded) ? $decoded : [];
    }

    /** @param array<string, mixed> $header */
    private function validateHeader(array $header): bool
    {
        $v = new Validator($header);
        $v->required('customer_name', 'Cliente');

        if ($v->fails()) {
            Session::flash('error', $v->firstError());
            Session::flash('_old', $header);

            return false;
        }

        return true;
    }
}
