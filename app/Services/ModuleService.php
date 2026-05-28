<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Core\Session;

final class ModuleService
{
    /** @return list<string> */
    public function modulesForTenant(int $tenantId): array
    {
        $db = Database::connection();
        $stmt = $db->prepare(
            'SELECT t.business_type, bs.modules_json FROM tenants t
             LEFT JOIN business_settings bs ON bs.tenant_id = t.id
             WHERE t.id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $tenantId]);
        $row = $stmt->fetch();
        if (!$row) {
            return [];
        }

        if (!empty($row['modules_json'])) {
            $decoded = json_decode((string) $row['modules_json'], true);

            return is_array($decoded) ? $decoded : [];
        }

        $type = (string) ($row['business_type'] ?? 'otro');

        return config('business_types')[$type]['modules'] ?? [];
    }

    public function loadIntoSession(int $tenantId): void
    {
        Session::set('tenant_modules', $this->modulesForTenant($tenantId));
        $db = Database::connection();
        $stmt = $db->prepare('SELECT business_type FROM tenants WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $tenantId]);
        $row = $stmt->fetch();
        Session::set('business_type', $row['business_type'] ?? 'otro');
    }

    public static function enabled(string $module): bool
    {
        $modules = Session::get('tenant_modules', []);

        return in_array($module, $modules, true);
    }
}
