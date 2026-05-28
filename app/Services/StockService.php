<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Core\TenantContext;
use App\Models\ProductModel;
use PDO;

final class StockService
{
    public function __construct(
        private readonly ProductModel $products = new ProductModel(),
        private readonly AuditService $audit = new AuditService(),
    ) {
    }

    public function adjust(
        int $productId,
        float $quantity,
        string $type,
        ?int $userId = null,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $notes = null,
    ): void {
        $tenantId = TenantContext::id() ?? throw new \RuntimeException('Sin tenant');
        $product = $this->products->find($productId, $tenantId);
        if (!$product) {
            throw new \InvalidArgumentException('Producto no encontrado.');
        }

        $before = (float) $product['stock'];
        $after = $before + $quantity;
        if ($after < 0) {
            throw new \InvalidArgumentException('Stock insuficiente.');
        }

        $db = Database::connection();
        $ownsTransaction = !$db->inTransaction();
        if ($ownsTransaction) {
            $db->beginTransaction();
        }

        try {
            $upd = $db->prepare(
                'UPDATE products SET stock = :stock WHERE id = :id AND tenant_id = :tenant_id'
            );
            $upd->execute(['stock' => $after, 'id' => $productId, 'tenant_id' => $tenantId]);

            $mov = $db->prepare(
                'INSERT INTO stock_movements
                (tenant_id, product_id, user_id, type, quantity, stock_before, stock_after, reference_type, reference_id, notes)
                VALUES (:tenant_id, :product_id, :user_id, :type, :quantity, :before, :after, :ref_type, :ref_id, :notes)'
            );
            $mov->execute([
                'tenant_id' => $tenantId,
                'product_id' => $productId,
                'user_id' => $userId,
                'type' => $type,
                'quantity' => $quantity,
                'before' => $before,
                'after' => $after,
                'ref_type' => $referenceType,
                'ref_id' => $referenceId,
                'notes' => $notes,
            ]);

            if ($ownsTransaction) {
                $db->commit();
            }
            $this->audit->log($tenantId, $userId, 'stock.' . $type, 'product', $productId);
        } catch (\Throwable $e) {
            if ($ownsTransaction && $db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
    }
}
