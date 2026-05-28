<?php

declare(strict_types=1);

namespace App\Core;

final class TenantContext
{
    private static ?int $tenantId = null;

    public static function set(int $tenantId): void
    {
        self::$tenantId = $tenantId;
    }

    public static function id(): ?int
    {
        return self::$tenantId ?? (Session::get('tenant_id') ? (int) Session::get('tenant_id') : null);
    }

    public static function clear(): void
    {
        self::$tenantId = null;
    }
}
