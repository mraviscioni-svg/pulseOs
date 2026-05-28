<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class ProductModel extends Model
{
    protected string $table = 'products';

    /** @return list<array<string, mixed>> */
    public function search(int $tenantId, ?string $q = null, int $limit = 200, ?string $status = null): array
    {
        $sql = 'SELECT p.*, c.name AS category_name, b.name AS brand_name
                FROM products p
                LEFT JOIN product_categories c ON c.id = p.category_id
                LEFT JOIN brands b ON b.id = p.brand_id
                WHERE p.tenant_id = :tenant_id';
        $params = ['tenant_id' => $tenantId];

        if ($status === 'active') {
            $sql .= ' AND p.is_active = 1';
        } elseif ($status === 'inactive') {
            $sql .= ' AND p.is_active = 0';
        }

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
            'SELECT p.*, v.id AS variant_id, v.name AS variant_name, v.price AS variant_price
             FROM product_variants v
             JOIN products p ON p.id = v.product_id
             WHERE v.tenant_id = :tenant_id AND v.barcode = :barcode AND v.is_active = 1 AND p.is_active = 1
             LIMIT 1'
        );
        $stmt->execute(['tenant_id' => $tenantId, 'barcode' => $barcode]);
        $variant = $stmt->fetch();
        if ($variant) {
            $variant['name'] = $variant['name'] . ' — ' . $variant['variant_name'];
            $variant['price'] = $variant['variant_price'];

            return $variant;
        }

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
             barcode, cost, price, wholesale_price, stock, min_stock, unit, image_path, has_variants, is_active)
             VALUES (:tenant_id, :category_id, :brand_id, :supplier_id, :name, :description, :sku, :internal_code,
             :barcode, :cost, :price, :wholesale_price, :stock, :min_stock, :unit, :image_path, :has_variants, :is_active)'
        );
        $stmt->execute($this->bindProduct($tenantId, $data));

        return (int) $this->db->lastInsertId();
    }

    /** @param array<string, mixed> $data */
    public function update(int $tenantId, int $id, array $data): void
    {
        $params = $this->bindProduct($tenantId, $data);
        $params['id'] = $id;
        $sql = 'UPDATE products SET category_id = :category_id, brand_id = :brand_id, supplier_id = :supplier_id,
             name = :name, description = :description, sku = :sku, internal_code = :internal_code,
             barcode = :barcode, cost = :cost, price = :price, wholesale_price = :wholesale_price,
             min_stock = :min_stock, unit = :unit, has_variants = :has_variants, is_active = :is_active';
        if (!empty($data['image_path'])) {
            $sql .= ', image_path = :image_path';
        }
        $sql .= ' WHERE id = :id AND tenant_id = :tenant_id';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
    }

    /** @param array<string, mixed> $data */
    private function bindProduct(int $tenantId, array $data): array
    {
        $bind = [
            'tenant_id' => $tenantId,
            'category_id' => !empty($data['category_id']) ? (int) $data['category_id'] : null,
            'brand_id' => !empty($data['brand_id']) ? (int) $data['brand_id'] : null,
            'supplier_id' => !empty($data['supplier_id']) ? (int) $data['supplier_id'] : null,
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
            'has_variants' => !empty($data['has_variants']) ? 1 : 0,
            'is_active' => !empty($data['is_active']) ? 1 : 0,
        ];
        if (!empty($data['image_path'])) {
            $bind['image_path'] = $data['image_path'];
        }

        return $bind;
    }

    public function setActive(int $tenantId, int $id, int $active): void
    {
        $stmt = $this->db->prepare(
            'UPDATE products SET is_active = :active WHERE id = :id AND tenant_id = :tenant_id'
        );
        $stmt->execute(['active' => $active ? 1 : 0, 'id' => $id, 'tenant_id' => $tenantId]);
    }

    public function deleteForTenant(int $tenantId, int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM products WHERE id = :id AND tenant_id = :tenant_id');
        $stmt->execute(['id' => $id, 'tenant_id' => $tenantId]);
    }
}
