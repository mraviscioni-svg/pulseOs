<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Core\TenantContext;
use App\Models\CustomerModel;
use App\Models\CustomerVehicleModel;
use App\Models\ProductModel;
use App\Models\WorkOrderModel;
use PDO;

final class WorkOrderService
{
    private const LABOR_SKU = 'PULSEOS-LABOR';

    public function __construct(
        private readonly WorkOrderModel $orders = new WorkOrderModel(),
        private readonly StockService $stock = new StockService(),
        private readonly SaleService $sales = new SaleService(),
        private readonly AuditService $audit = new AuditService(),
    ) {
    }

    /** @param array<string, mixed> $header */
    /** @param list<array<string, mixed>> $lines */
    public function create(int $tenantId, int $userId, array $header, array $lines): int
    {
        $db = Database::connection();
        $db->beginTransaction();

        try {
            $header = $this->normalizeHeader($tenantId, $header);
            $number = $this->orders->nextOrderNumber($db, $tenantId);
            $totals = $this->sumLines($lines);

            $stmt = $db->prepare(
                'INSERT INTO work_orders (
                    tenant_id, customer_id, customer_vehicle_id, order_number, status, priority,
                    customer_name, customer_phone, customer_email,
                    vehicle_label, vehicle_notes, odometer_km, reported_issue,
                    assigned_user_id, created_by_user_id,
                    subtotal, total, notes_internal, notes_customer
                ) VALUES (
                    :tenant_id, :customer_id, :customer_vehicle_id, :order_number, :status, :priority,
                    :customer_name, :customer_phone, :customer_email,
                    :vehicle_label, :vehicle_notes, :odometer_km, :reported_issue,
                    :assigned_user_id, :created_by,
                    :subtotal, :total, :notes_internal, :notes_customer
                )'
            );
            $stmt->execute([
                'tenant_id' => $tenantId,
                'customer_id' => $header['customer_id'] ?? null,
                'customer_vehicle_id' => $header['customer_vehicle_id'] ?? null,
                'order_number' => $number,
                'status' => $header['status'] ?? 'borrador',
                'priority' => $header['priority'] ?? 'normal',
                'customer_name' => $header['customer_name'],
                'customer_phone' => $header['customer_phone'] ?? null,
                'customer_email' => $header['customer_email'] ?? null,
                'vehicle_label' => $header['vehicle_label'] ?? null,
                'vehicle_notes' => $header['vehicle_notes'] ?? null,
                'odometer_km' => !empty($header['odometer_km']) ? (int) $header['odometer_km'] : null,
                'reported_issue' => $header['reported_issue'] ?? null,
                'assigned_user_id' => !empty($header['assigned_user_id']) ? (int) $header['assigned_user_id'] : null,
                'created_by' => $userId,
                'subtotal' => $totals,
                'total' => $totals,
                'notes_internal' => $header['notes_internal'] ?? null,
                'notes_customer' => $header['notes_customer'] ?? null,
            ]);
            $workOrderId = (int) $db->lastInsertId();

            $this->replaceLines($db, $tenantId, $workOrderId, $lines);
            $this->recordStatus($db, $tenantId, $workOrderId, null, (string) ($header['status'] ?? 'borrador'), 'Alta de orden', $userId);

            $db->commit();
            $this->audit->log($tenantId, $userId, 'work_order.created', 'work_order', $workOrderId);

            return $workOrderId;
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /** @param array<string, mixed> $header */
    /** @param list<array<string, mixed>> $lines */
    public function update(int $tenantId, int $userId, int $workOrderId, array $header, array $lines): void
    {
        $order = $this->orders->find($workOrderId, $tenantId);
        if (!$order) {
            throw new \InvalidArgumentException('Orden no encontrada.');
        }

        $editable = config('work_order_statuses')['editable'] ?? [];
        if (!in_array($order['status'], $editable, true)) {
            throw new \InvalidArgumentException('Esta orden no se puede editar en su estado actual.');
        }

        $db = Database::connection();
        $db->beginTransaction();

        try {
            $header = $this->normalizeHeader($tenantId, $header);
            $totals = $this->sumLines($lines);

            $stmt = $db->prepare(
                'UPDATE work_orders SET
                    customer_id = :customer_id,
                    customer_vehicle_id = :customer_vehicle_id,
                    priority = :priority,
                    customer_name = :customer_name,
                    customer_phone = :customer_phone,
                    customer_email = :customer_email,
                    vehicle_label = :vehicle_label,
                    vehicle_notes = :vehicle_notes,
                    odometer_km = :odometer_km,
                    reported_issue = :reported_issue,
                    assigned_user_id = :assigned_user_id,
                    subtotal = :subtotal,
                    total = :total,
                    notes_internal = :notes_internal,
                    notes_customer = :notes_customer
                 WHERE id = :id AND tenant_id = :tenant_id'
            );
            $stmt->execute([
                'customer_id' => $header['customer_id'] ?? null,
                'customer_vehicle_id' => $header['customer_vehicle_id'] ?? null,
                'priority' => $header['priority'] ?? 'normal',
                'customer_name' => $header['customer_name'],
                'customer_phone' => $header['customer_phone'] ?? null,
                'customer_email' => $header['customer_email'] ?? null,
                'vehicle_label' => $header['vehicle_label'] ?? null,
                'vehicle_notes' => $header['vehicle_notes'] ?? null,
                'odometer_km' => !empty($header['odometer_km']) ? (int) $header['odometer_km'] : null,
                'reported_issue' => $header['reported_issue'] ?? null,
                'assigned_user_id' => !empty($header['assigned_user_id']) ? (int) $header['assigned_user_id'] : null,
                'subtotal' => $totals,
                'total' => $totals,
                'notes_internal' => $header['notes_internal'] ?? null,
                'notes_customer' => $header['notes_customer'] ?? null,
                'id' => $workOrderId,
                'tenant_id' => $tenantId,
            ]);

            $this->replaceLines($db, $tenantId, $workOrderId, $lines);

            $db->commit();
            $this->audit->log($tenantId, $userId, 'work_order.updated', 'work_order', $workOrderId);
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function transition(int $tenantId, int $userId, int $workOrderId, string $toStatus, ?string $note = null): void
    {
        $order = $this->orders->find($workOrderId, $tenantId);
        if (!$order) {
            throw new \InvalidArgumentException('Orden no encontrada.');
        }

        $from = (string) $order['status'];
        $actions = config('work_order_statuses')['actions'][$from] ?? [];
        if (!isset($actions[$toStatus]) && $toStatus !== $from) {
            throw new \InvalidArgumentException('Transición de estado no permitida.');
        }

        $db = Database::connection();
        $db->beginTransaction();

        try {
            $stmt = $db->prepare(
                'UPDATE work_orders SET status = :status WHERE id = :id AND tenant_id = :tenant_id'
            );
            $stmt->execute(['status' => $toStatus, 'id' => $workOrderId, 'tenant_id' => $tenantId]);

            if ($toStatus === 'aprobada') {
                $this->orders->markApproved($tenantId, $workOrderId);
            }

            if ($toStatus === 'cerrada') {
                $db->prepare(
                    'UPDATE work_orders SET closed_at = NOW() WHERE id = :id AND tenant_id = :tenant_id'
                )->execute(['id' => $workOrderId, 'tenant_id' => $tenantId]);
            }

            $this->recordStatus($db, $tenantId, $workOrderId, $from, $toStatus, $note, $userId);

            $db->commit();
            $this->audit->log($tenantId, $userId, 'work_order.status.' . $toStatus, 'work_order', $workOrderId);
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /** Descuenta stock de líneas tipo repuesto aún no consumidas. */
    public function consumeParts(int $tenantId, int $userId, int $workOrderId): int
    {
        $order = $this->orders->find($workOrderId, $tenantId);
        if (!$order) {
            throw new \InvalidArgumentException('Orden no encontrada.');
        }

        $lines = $this->orders->linesForOrder($tenantId, $workOrderId);
        $consumed = 0;

        $db = Database::connection();
        $db->beginTransaction();

        try {
            foreach ($lines as $line) {
                if ($line['line_type'] !== 'part' || empty($line['product_id']) || (int) $line['stock_consumed'] === 1) {
                    continue;
                }

                $productId = (int) $line['product_id'];
                $qty = (float) $line['quantity'];

                $this->stock->adjust(
                    $productId,
                    -$qty,
                    'orden_trabajo',
                    $userId,
                    'work_order',
                    $workOrderId,
                    'Repuesto OT ' . $order['order_number']
                );

                $upd = $db->prepare(
                    'UPDATE work_order_lines SET stock_consumed = 1
                     WHERE id = :id AND tenant_id = :tenant_id'
                );
                $upd->execute(['id' => $line['id'], 'tenant_id' => $tenantId]);
                $consumed++;
            }

            $db->commit();

            return $consumed;
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function charge(int $tenantId, int $userId, int $workOrderId, string $paymentMethod = 'efectivo'): int
    {
        $order = $this->orders->findDetailed($tenantId, $workOrderId);
        if (!$order) {
            throw new \InvalidArgumentException('Orden no encontrada.');
        }

        if ($order['status'] !== 'lista_entregar') {
            throw new \InvalidArgumentException('Solo se puede cobrar una orden lista para entregar.');
        }

        if (!empty($order['sale_id'])) {
            throw new \InvalidArgumentException('Esta orden ya fue cobrada.');
        }

        $lines = $this->orders->linesForOrder($tenantId, $workOrderId);
        if ($lines === []) {
            throw new \InvalidArgumentException('La orden no tiene ítems para cobrar.');
        }

        $this->consumeParts($tenantId, $userId, $workOrderId);

        $saleItems = [];
        $laborProductId = $this->laborProductId($tenantId);

        foreach ($lines as $line) {
            $qty = (float) $line['quantity'];
            $price = (float) $line['unit_price'];
            $name = (string) $line['description'];

            if ($line['line_type'] === 'part' && !empty($line['product_id'])) {
                $productId = (int) $line['product_id'];
                if ((int) $line['stock_consumed'] === 1) {
                    $saleItems[] = [
                        'product_id' => $productId,
                        'quantity' => $qty,
                        'unit_price' => $price,
                        'discount' => 0,
                        'name' => $name,
                        'skip_stock' => true,
                    ];
                } else {
                    $saleItems[] = [
                        'product_id' => $productId,
                        'quantity' => $qty,
                        'unit_price' => $price,
                        'discount' => 0,
                        'name' => $name,
                    ];
                }
            } else {
                $saleItems[] = [
                    'product_id' => $laborProductId,
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'discount' => 0,
                    'name' => $name,
                    'skip_stock' => true,
                ];
            }
        }

        $saleId = $this->sales->complete($saleItems, 0, $paymentMethod, null, $userId);
        $this->orders->linkSale($tenantId, $workOrderId, $saleId);
        $this->recordStatusStandalone(
            $tenantId,
            $workOrderId,
            'lista_entregar',
            'cerrada',
            'Cobro: venta #' . $saleId,
            $userId
        );
        $this->audit->log($tenantId, $userId, 'work_order.charged', 'work_order', $workOrderId);

        return $saleId;
    }

    public function closeWithoutSale(int $tenantId, int $userId, int $workOrderId, ?string $note = null): void
    {
        $this->consumeParts($tenantId, $userId, $workOrderId);
        $this->transition($tenantId, $userId, $workOrderId, 'cerrada', $note ?? 'Cierre sin venta en POS');
    }

    /** @param array<string, mixed> $header */
    private function normalizeHeader(int $tenantId, array $header): array
    {
        $customerId = !empty($header['customer_id']) ? (int) $header['customer_id'] : null;
        $vehicleId = !empty($header['customer_vehicle_id']) ? (int) $header['customer_vehicle_id'] : null;

        if ($customerId) {
            $customer = (new CustomerModel())->find($customerId, $tenantId);
            if (!$customer) {
                throw new \InvalidArgumentException('Cliente no válido.');
            }
            if (trim((string) ($header['customer_name'] ?? '')) === '') {
                $header['customer_name'] = $customer['name'];
            }
            if (empty($header['customer_phone'])) {
                $header['customer_phone'] = $customer['phone'];
            }
            if (empty($header['customer_email'])) {
                $header['customer_email'] = $customer['email'];
            }
        } else {
            $customerId = null;
            $vehicleId = null;
        }

        if ($vehicleId && $customerId) {
            $vehicle = (new CustomerVehicleModel())->findForCustomer($tenantId, $customerId, $vehicleId);
            if ($vehicle) {
                if (empty($header['vehicle_label'])) {
                    $header['vehicle_label'] = $vehicle['label'];
                }
                if (empty($header['vehicle_notes'])) {
                    $header['vehicle_notes'] = $vehicle['description'];
                }
            }
        } elseif (!$customerId) {
            $vehicleId = null;
        }

        $header['customer_id'] = $customerId;
        $header['customer_vehicle_id'] = $vehicleId;

        if (trim((string) ($header['customer_name'] ?? '')) === '') {
            throw new \InvalidArgumentException('Indicá el cliente o seleccioná uno del directorio.');
        }

        return $header;
    }

    /** @param list<array<string, mixed>> $lines */
    private function sumLines(array $lines): float
    {
        $total = 0.0;
        foreach ($lines as $line) {
            $qty = (float) ($line['quantity'] ?? 1);
            $price = (float) ($line['unit_price'] ?? 0);
            $total += $qty * $price;
        }

        return round($total, 2);
    }

    /** @param list<array<string, mixed>> $lines */
    private function replaceLines(PDO $db, int $tenantId, int $workOrderId, array $lines): void
    {
        $db->prepare(
            'DELETE FROM work_order_lines WHERE work_order_id = :wo_id AND tenant_id = :tenant_id'
        )->execute(['wo_id' => $workOrderId, 'tenant_id' => $tenantId]);

        if ($lines === []) {
            return;
        }

        $stmt = $db->prepare(
            'INSERT INTO work_order_lines
            (tenant_id, work_order_id, line_type, product_id, description, quantity, unit_price, line_total, sort_order)
            VALUES (:tenant_id, :wo_id, :line_type, :product_id, :description, :qty, :price, :total, :sort)'
        );

        $sort = 0;
        foreach ($lines as $line) {
            $qty = max(0.001, (float) ($line['quantity'] ?? 1));
            $price = (float) ($line['unit_price'] ?? 0);
            $lineTotal = round($qty * $price, 2);
            $type = (string) ($line['line_type'] ?? 'labor');
            if (!in_array($type, ['labor', 'part', 'other'], true)) {
                $type = 'labor';
            }

            $productId = null;
            if ($type === 'part' && !empty($line['product_id'])) {
                $productId = (int) $line['product_id'];
            }

            $description = trim((string) ($line['description'] ?? ''));
            if ($description === '') {
                continue;
            }

            $stmt->execute([
                'tenant_id' => $tenantId,
                'wo_id' => $workOrderId,
                'line_type' => $type,
                'product_id' => $productId,
                'description' => $description,
                'qty' => $qty,
                'price' => $price,
                'total' => $lineTotal,
                'sort' => $sort++,
            ]);
        }
    }

    private function recordStatus(
        PDO $db,
        int $tenantId,
        int $workOrderId,
        ?string $from,
        string $to,
        ?string $note,
        int $userId,
    ): void {
        if ($from === $to) {
            return;
        }

        $stmt = $db->prepare(
            'INSERT INTO work_order_status_history
            (tenant_id, work_order_id, from_status, to_status, note, user_id)
            VALUES (:tenant_id, :wo_id, :from_status, :to_status, :note, :user_id)'
        );
        $stmt->execute([
            'tenant_id' => $tenantId,
            'wo_id' => $workOrderId,
            'from_status' => $from,
            'to_status' => $to,
            'note' => $note,
            'user_id' => $userId,
        ]);
    }

    private function recordStatusStandalone(
        int $tenantId,
        int $workOrderId,
        string $from,
        string $to,
        ?string $note,
        int $userId,
    ): void {
        $db = Database::connection();
        $this->recordStatus($db, $tenantId, $workOrderId, $from, $to, $note, $userId);
    }

    private function laborProductId(int $tenantId): int
    {
        $products = new ProductModel();
        $stmt = $products->findByBarcode($tenantId, self::LABOR_SKU);
        if ($stmt) {
            return (int) $stmt['id'];
        }

        $db = Database::connection();
        $check = $db->prepare(
            'SELECT id FROM products WHERE tenant_id = :tenant_id AND internal_code = :code LIMIT 1'
        );
        $check->execute(['tenant_id' => $tenantId, 'code' => self::LABOR_SKU]);
        $row = $check->fetch();
        if ($row) {
            return (int) $row['id'];
        }

        return $products->create($tenantId, [
            'name' => 'Mano de obra (servicios)',
            'description' => 'Ítem interno PulseOS para cargar mano de obra en órdenes de trabajo.',
            'internal_code' => self::LABOR_SKU,
            'barcode' => self::LABOR_SKU,
            'cost' => 0,
            'price' => 0,
            'stock' => 0,
            'min_stock' => 0,
            'unit' => 'servicio',
            'is_active' => 1,
        ]);
    }
}
