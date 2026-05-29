<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Core\TenantContext;
use PDO;

final class SaleService
{
    public function __construct(
        private readonly StockService $stock = new StockService(),
        private readonly CashRegisterService $cash = new CashRegisterService(),
        private readonly AuditService $audit = new AuditService(),
    ) {
    }

    /** @param list<array{product_id: int, quantity: float, unit_price: float, discount?: float, name: string, skip_stock?: bool}> $items */
    public function complete(
        array $items,
        float $discount,
        string $paymentMethod,
        ?array $paymentDetails,
        int $userId,
    ): int {
        $tenantId = TenantContext::id() ?? throw new \RuntimeException('Sin tenant');
        if ($items === []) {
            throw new \InvalidArgumentException('Carrito vacío.');
        }

        $subtotal = 0.0;
        foreach ($items as $item) {
            $line = ($item['unit_price'] * $item['quantity']) - ($item['discount'] ?? 0);
            $subtotal += $line;
        }
        $total = max(0, $subtotal - $discount);

        $db = Database::connection();
        $db->beginTransaction();

        try {
            $register = $this->cash->getOpenRegister($tenantId);
            if (!$register) {
                throw new \RuntimeException('No hay caja abierta. Abrí la caja antes de vender.');
            }

            $saleNumber = $this->nextSaleNumber($db, $tenantId);

            $sale = $db->prepare(
                'INSERT INTO sales (tenant_id, user_id, cash_register_id, sale_number, subtotal, discount, total, payment_method, payment_details)
                 VALUES (:tenant_id, :user_id, :register_id, :number, :subtotal, :discount, :total, :payment, :details)'
            );
            $sale->execute([
                'tenant_id' => $tenantId,
                'user_id' => $userId,
                'register_id' => $register['id'],
                'number' => $saleNumber,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total' => $total,
                'payment' => $paymentMethod,
                'details' => $paymentDetails ? json_encode($paymentDetails) : null,
            ]);
            $saleId = (int) $db->lastInsertId();

            $itemStmt = $db->prepare(
                'INSERT INTO sale_items (tenant_id, sale_id, product_id, product_name, quantity, unit_price, discount, total)
                 VALUES (:tenant_id, :sale_id, :product_id, :name, :qty, :price, :disc, :total)'
            );

            foreach ($items as $item) {
                $lineTotal = ($item['unit_price'] * $item['quantity']) - ($item['discount'] ?? 0);
                $itemStmt->execute([
                    'tenant_id' => $tenantId,
                    'sale_id' => $saleId,
                    'product_id' => $item['product_id'],
                    'name' => $item['name'],
                    'qty' => $item['quantity'],
                    'price' => $item['unit_price'],
                    'disc' => $item['discount'] ?? 0,
                    'total' => $lineTotal,
                ]);

                if (empty($item['skip_stock'])) {
                    $this->stock->adjust(
                        (int) $item['product_id'],
                        -1 * (float) $item['quantity'],
                        'venta',
                        $userId,
                        'sale',
                        $saleId
                    );
                }
            }

            $this->cash->addMovement(
                (int) $register['id'],
                'venta',
                $total,
                'Venta ' . $saleNumber,
                'sale',
                $saleId,
                $userId
            );

            $db->commit();
            $this->audit->log($tenantId, $userId, 'sale.completed', 'sale', $saleId);

            return $saleId;
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    private function nextSaleNumber(PDO $db, int $tenantId): string
    {
        $stmt = $db->prepare(
            'SELECT COUNT(*) AS c FROM sales WHERE tenant_id = :tenant_id AND DATE(created_at) = CURDATE()'
        );
        $stmt->execute(['tenant_id' => $tenantId]);
        $count = (int) ($stmt->fetch()['c'] ?? 0);

        return 'V-' . date('Ymd') . '-' . str_pad((string) ($count + 1), 4, '0', STR_PAD_LEFT);
    }
}
