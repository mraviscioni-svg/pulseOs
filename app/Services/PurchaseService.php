<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Core\TenantContext;

final class PurchaseService
{
    public function __construct(
        private readonly StockService $stock = new StockService(),
        private readonly AuditService $audit = new AuditService(),
    ) {
    }

    public function receive(int $purchaseId, int $userId): void
    {
        $tenantId = TenantContext::id() ?? throw new \RuntimeException('Sin tenant');
        $db = Database::connection();

        $purchase = $db->prepare(
            'SELECT * FROM purchases WHERE id = :id AND tenant_id = :tenant_id LIMIT 1'
        );
        $purchase->execute(['id' => $purchaseId, 'tenant_id' => $tenantId]);
        $row = $purchase->fetch();
        if (!$row || $row['status'] !== 'pendiente') {
            throw new \RuntimeException('Compra no válida para recepción.');
        }

        $items = $db->prepare(
            'SELECT * FROM purchase_items WHERE purchase_id = :id AND tenant_id = :tenant_id'
        );
        $items->execute(['id' => $purchaseId, 'tenant_id' => $tenantId]);

        $db->beginTransaction();
        try {
            foreach ($items->fetchAll() as $item) {
                $this->stock->adjust(
                    (int) $item['product_id'],
                    (float) $item['quantity'],
                    'compra',
                    $userId,
                    'purchase',
                    $purchaseId
                );

                $upd = $db->prepare(
                    'UPDATE products SET cost = :cost WHERE id = :id AND tenant_id = :tenant_id'
                );
                $upd->execute([
                    'cost' => $item['unit_cost'],
                    'id' => $item['product_id'],
                    'tenant_id' => $tenantId,
                ]);
            }

            $updPurchase = $db->prepare(
                "UPDATE purchases SET status = 'recibida', received_at = NOW() WHERE id = :id AND tenant_id = :tenant_id"
            );
            $updPurchase->execute(['id' => $purchaseId, 'tenant_id' => $tenantId]);

            $db->commit();
            $this->audit->log($tenantId, $userId, 'purchase.received', 'purchase', $purchaseId);
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }
}
