<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class ProductVariantModel extends Model
{
    protected string $table = 'product_variants';

    /** @return list<array<string, mixed>> */
    public function forProduct(int $tenantId, int $productId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM product_variants WHERE tenant_id = :tenant_id AND product_id = :product_id ORDER BY name'
        );
        $stmt->execute(['tenant_id' => $tenantId, 'product_id' => $productId]);

        return $stmt->fetchAll();
    }

    /** @param array<string, mixed> $data */
    public function create(int $tenantId, int $productId, array $data): int
    {
        $attrs = !empty($data['attributes']) ? json_encode($data['attributes'], JSON_UNESCAPED_UNICODE) : null;
        $stmt = $this->db->prepare(
            'INSERT INTO product_variants (tenant_id, product_id, name, sku, barcode, attributes_json, cost, price, stock)
             VALUES (:tenant_id, :product_id, :name, :sku, :barcode, :attrs, :cost, :price, :stock)'
        );
        $stmt->execute([
            'tenant_id' => $tenantId,
            'product_id' => $productId,
            'name' => $data['name'],
            'sku' => $data['sku'] ?? null,
            'barcode' => $data['barcode'] ?? null,
            'attrs' => $attrs,
            'cost' => $data['cost'] ?? 0,
            'price' => $data['price'] ?? 0,
            'stock' => $data['stock'] ?? 0,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function delete(int $tenantId, int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM product_variants WHERE id = :id AND tenant_id = :tenant_id');
        $stmt->execute(['id' => $id, 'tenant_id' => $tenantId]);
    }
}
