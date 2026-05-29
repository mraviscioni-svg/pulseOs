<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Session;

/** Sesión del panel del comercio (independiente de la sesión platform en $_SESSION). */
final class TenantSessionService
{
    /** @var list<string> */
    private const TENANT_KEYS = [
        'user_id',
        'tenant_id',
        'user_name',
        'user_email',
        'role_slug',
        'tenant_name',
        'permissions',
        'tenant_modules',
        'business_type',
    ];

    /** @var list<string> */
    private const IMPERSONATION_KEYS = [
        'platform_impersonating',
        'platform_return_url',
    ];

    public static function isLoggedIn(): bool
    {
        return (bool) Session::get('user_id');
    }

    public static function clear(bool $withImpersonation = true): void
    {
        foreach (self::TENANT_KEYS as $key) {
            Session::forget($key);
        }

        if ($withImpersonation) {
            foreach (self::IMPERSONATION_KEYS as $key) {
                Session::forget($key);
            }
        }
    }
}
