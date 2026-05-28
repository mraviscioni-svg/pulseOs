<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Session;
use App\Models\PlatformAdminModel;

final class PlatformAuthService
{
    public function __construct(
        private readonly PlatformAdminModel $admins = new PlatformAdminModel(),
    ) {
    }

    public function attempt(string $username, string $password): bool
    {
        $username = normalize_username($username);
        $admin = $this->admins->findByUsername($username);
        if (!$admin || !password_verify($password, $admin['password'])) {
            return false;
        }

        $this->clearTenantSession();
        Session::set('platform_admin_id', (int) $admin['id']);
        Session::set('platform_admin_name', $admin['name']);
        Session::set('platform_admin_email', $admin['email']);

        $this->admins->updateLastLogin((int) $admin['id']);

        return true;
    }

    public function logout(): void
    {
        Session::forget('platform_admin_id');
        Session::forget('platform_admin_name');
        Session::forget('platform_admin_email');
    }

    public static function check(): bool
    {
        return (bool) Session::get('platform_admin_id');
    }

    private function clearTenantSession(): void
    {
        foreach (['user_id', 'tenant_id', 'user_name', 'user_email', 'role_slug', 'tenant_name', 'permissions', 'tenant_modules', 'business_type'] as $key) {
            Session::forget($key);
        }
    }
}
