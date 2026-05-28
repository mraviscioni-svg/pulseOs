<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class SettingsModel
{
    /** @return array<string, mixed>|null */
    public function get(int $tenantId): ?array
    {
        $db = Database::connection();
        $stmt = $db->prepare('SELECT * FROM business_settings WHERE tenant_id = :tenant_id LIMIT 1');
        $stmt->execute(['tenant_id' => $tenantId]);

        return $stmt->fetch() ?: null;
    }

    /** @param array<string, mixed> $data */
    public function updateSettings(int $tenantId, array $data): void
    {
        $db = Database::connection();
        $stmt = $db->prepare(
            'UPDATE business_settings SET currency = :currency, tax_rate = :tax_rate,
             pos_receipt_footer = :footer, low_stock_alert = :alert, dark_mode = :dark
             WHERE tenant_id = :tenant_id'
        );
        $stmt->execute([
            'currency' => $data['currency'] ?? 'ARS',
            'tax_rate' => $data['tax_rate'] ?? 0,
            'footer' => $data['pos_receipt_footer'] ?? null,
            'alert' => !empty($data['low_stock_alert']) ? 1 : 0,
            'dark' => !empty($data['dark_mode']) ? 1 : 0,
            'tenant_id' => $tenantId,
        ]);
    }

    /** @param array<string, mixed> $data */
    public function updateTenant(int $tenantId, array $data): void
    {
        $db = Database::connection();
        $tenant = $db->prepare('UPDATE tenants SET name = :name, phone = :phone, address = :address, tax_id = :tax_id WHERE id = :id');
        $tenant->execute([
            'name' => $data['tenant_name'],
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'tax_id' => $data['tax_id'] ?? null,
            'id' => $tenantId,
        ]);
    }

    /** @param array<string, mixed> $data */
    public function update(int $tenantId, array $data): void
    {
        $this->updateSettings($tenantId, $data);
        $this->updateTenant($tenantId, $data);
    }
}
