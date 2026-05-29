<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Models\BrandModel;
use App\Models\CategoryModel;
use PDO;

final class BusinessCatalogService
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function rememberCategory(string $businessType, string $name, ?string $description = null): void
    {
        $name = $this->normalizeName($name);
        if ($name === '') {
            return;
        }

        $stmt = $this->db->prepare(
            'INSERT INTO business_catalog_categories (business_type, name, description, use_count)
             VALUES (:type, :name, :desc, 1)
             ON DUPLICATE KEY UPDATE
                use_count = use_count + 1,
                description = COALESCE(VALUES(description), description),
                updated_at = CURRENT_TIMESTAMP'
        );
        $stmt->execute([
            'type' => $businessType,
            'name' => $name,
            'desc' => $description,
        ]);
    }

    public function rememberBrand(string $businessType, string $name): void
    {
        $name = $this->normalizeName($name);
        if ($name === '') {
            return;
        }

        $stmt = $this->db->prepare(
            'INSERT INTO business_catalog_brands (business_type, name, use_count)
             VALUES (:type, :name, 1)
             ON DUPLICATE KEY UPDATE
                use_count = use_count + 1,
                updated_at = CURRENT_TIMESTAMP'
        );
        $stmt->execute(['type' => $businessType, 'name' => $name]);
    }

    public function ensureDefaults(string $businessType): void
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM business_catalog_categories WHERE business_type = :type');
        $stmt->execute(['type' => $businessType]);
        if ((int) $stmt->fetchColumn() > 0) {
            return;
        }

        $this->seedDefaultsFromConfig($businessType);
    }

    /** Borra el catálogo del rubro y vuelve a cargar las sugerencias por defecto. */
    public function resetCatalogForType(string $businessType): void
    {
        $stmt = $this->db->prepare('DELETE FROM business_catalog_categories WHERE business_type = :type');
        $stmt->execute(['type' => $businessType]);
        $stmt = $this->db->prepare('DELETE FROM business_catalog_brands WHERE business_type = :type');
        $stmt->execute(['type' => $businessType]);
        $this->seedDefaultsFromConfig($businessType);
    }

    private function seedDefaultsFromConfig(string $businessType): void
    {
        $defaults = config('catalog_defaults')[$businessType] ?? config('catalog_defaults')['otro'] ?? [];
        foreach ($defaults['categories'] ?? [] as $name) {
            $this->rememberCategory($businessType, (string) $name);
        }
        foreach ($defaults['brands'] ?? [] as $name) {
            $this->rememberBrand($businessType, (string) $name);
        }
    }

    /** @return array{categories: int, brands: int} */
    public function seedTenant(int $tenantId, string $businessType): array
    {
        $this->ensureDefaults($businessType);

        $categoryModel = new CategoryModel();
        $brandModel = new BrandModel();
        $imported = ['categories' => 0, 'brands' => 0];

        foreach ($this->categoriesForType($businessType) as $row) {
            if ($categoryModel->existsByName($tenantId, $row['name'])) {
                continue;
            }
            $categoryModel->create($tenantId, [
                'name' => $row['name'],
                'description' => $row['description'],
            ]);
            ++$imported['categories'];
        }

        foreach ($this->brandsForType($businessType) as $row) {
            if ($brandModel->existsByName($tenantId, $row['name'])) {
                continue;
            }
            $brandModel->create($tenantId, ['name' => $row['name']]);
            ++$imported['brands'];
        }

        return $imported;
    }

    /** @return list<array<string, mixed>> */
    public function categoriesForType(string $businessType): array
    {
        $stmt = $this->db->prepare(
            'SELECT name, description, use_count FROM business_catalog_categories
             WHERE business_type = :type ORDER BY use_count DESC, name ASC'
        );
        $stmt->execute(['type' => $businessType]);

        return $stmt->fetchAll();
    }

    /** @return list<array<string, mixed>> */
    public function brandsForType(string $businessType): array
    {
        $stmt = $this->db->prepare(
            'SELECT name, use_count FROM business_catalog_brands
             WHERE business_type = :type ORDER BY use_count DESC, name ASC'
        );
        $stmt->execute(['type' => $businessType]);

        return $stmt->fetchAll();
    }

    /** @param list<array<string, mixed>> $tenantRows
     * @return list<array<string, mixed>>
     */
    public function suggestionsNotInTenant(array $catalogRows, array $tenantRows): array
    {
        $existing = [];
        foreach ($tenantRows as $row) {
            $existing[$this->normalizeKey((string) ($row['name'] ?? ''))] = true;
        }

        $out = [];
        foreach ($catalogRows as $row) {
            $key = $this->normalizeKey((string) ($row['name'] ?? ''));
            if ($key !== '' && !isset($existing[$key])) {
                $out[] = $row;
            }
        }

        return $out;
    }

    private function normalizeName(string $name): string
    {
        return trim(preg_replace('/\s+/u', ' ', $name) ?? '');
    }

    private function normalizeKey(string $name): string
    {
        return mb_strtolower($this->normalizeName($name), 'UTF-8');
    }
}
