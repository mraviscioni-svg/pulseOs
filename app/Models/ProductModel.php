<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class ProductModel extends Model
{
    protected string $table = 'products';

    /** @return list<array<string, mixed>> */
    public function search(int $tenantId, ?string $q = null, int $limit = 50): array
    {
        $sql = 'SELECT p.*, c.name AS category_name, b.name AS brand_name
                FROM products p
                LEFT JOIN product_categories c ON c.id = p.category_id
                LEFT JOIN brands b ON b.id = p.brand_id
                WHERE p.tenant_id = :tenant_id';
        $params = ['tenant_id' => $tenantId];

        if ($q) {
            $sql .= ' AND (p.name LIKE :q OR p.sku LIKE :q OR p.barcode LIKE :q OR p.internal_code LIKE :q)';
            $params['q'] = '%' . $q . '%';
        }

        $sql .= ' ORDER BY p.name LIMIT ' . (int) $limit;
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /** @return array<string, mixed>|null */
    public function findByBarcode(int $tenantId, string $barcode): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM products WHERE tenant_id = :tenant_id AND barcode = :barcode AND is_active = 1 LIMIT 1'
        );
        $stmt->execute(['tenant_id' => $tenantId, 'barcode' => $barcode]);

        return $stmt->fetch() ?: null;
    }

    /** @param array<string, mixed> $data */
    public function create(int $tenantId, array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO products (tenant_id, category_id, brand_id, supplier_id, name, description, sku, internal_code,
             barcode, cost, price, wholesale_price, stock, min_stock, unit, is_active)
             VALUES (:tenant_id, :category_id, :brand_id, :supplier_id, :name, :description, :sku, :internal_code,
             :barcode, :cost, :price, :wholesale_price, :stock, :min_stock, :unit, :is_active)'
        );
        $stmt->execute($this->bindProduct($tenantId, $data));

        return (int) $this->db->lastInsertId();
    }

    /** @param array<string, mixed> $data */
    public function update(int $tenantId, int $id, array $data): void
    {
        $params = $this->bindProduct($tenantId, $data);
        $params['id'] = $id;
        $stmt = $this->db->prepare(
            'UPDATE products SET category_id = :category_id, brand_id = :brand_id, supplier_id = :supplier_id,
             name = :name, description = :description, sku = :sku, internal_code = :internal_code,
             barcode = :barcode, cost = :cost, price = :price, wholesale_price = :wholesale_price,
             min_stock = :min_stock, unit = :unit, is_active = :is_active
             WHERE id = :id AND tenant_id = :tenant_id'
        );
        $stmt->execute($params);
    }

    /** @param array<string, mixed> $data */
    private function bindProduct(int $tenantId, array $data): array
    {
        return [
            'tenant_id' => $tenantId,
            'category_id' => $data['category_id'] ?: null,
            'brand_id' => $data['brand_id'] ?: null,
            'supplier_id' => $data['supplier_id'] ?: null,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'sku' => $data['sku'] ?? null,
            'internal_code' => $data['internal_code'] ?? null,
            'barcode' => $data['barcode'] ?? null,
            'cost' => $data['cost'] ?? 0,
            'price' => $data['price'] ?? 0,
            'wholesale_price' => $data['wholesale_price'] ?? null,
            'stock' => $data['stock'] ?? 0,
            'min_stock' => $data['min_stock'] ?? 0,
            'unit' => $data['unit'] ?? 'unidad',
            'is_active' => !empty($data['is_active']) ? 1 : 0,
        ];
    }
}
