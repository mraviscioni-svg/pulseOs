<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Core\Session;
use App\Models\UserModel;
use PDO;

final class AuthService
{
    public function __construct(
        private readonly UserModel $users = new UserModel(),
        private readonly AuditService $audit = new AuditService(),
    ) {
    }

    public function attempt(string $username, string $password): bool
    {
        $username = normalize_username($username);
        $user = $this->users->findByUsernameGlobal($username);
        if (!$user || !(int) $user['is_active'] || !password_verify($password, $user['password'])) {
            return false;
        }

        $permissions = $this->users->permissionsForRole((int) $user['role_id']);

        Session::set('user_id', (int) $user['id']);
        Session::set('tenant_id', (int) $user['tenant_id']);
        Session::set('user_name', $user['name']);
        Session::set('user_email', $user['email']);
        Session::set('role_slug', $user['role_slug']);
        Session::set('tenant_name', $user['tenant_name']);
        Session::set('permissions', $permissions);
        try {
            (new ModuleService())->loadIntoSession((int) $user['tenant_id']);
        } catch (\Throwable) {
            Session::set('tenant_modules', []);
            Session::set('business_type', '');
        }

        $this->users->updateLastLogin((int) $user['id']);
        $this->audit->log((int) $user['tenant_id'], (int) $user['id'], 'auth.login');

        return true;
    }

    public function logout(): void
    {
        $tenantId = Session::get('tenant_id');
        $userId = Session::get('user_id');
        if ($tenantId && $userId) {
            $this->audit->log((int) $tenantId, (int) $userId, 'auth.logout');
        }
        Session::destroy();
    }

    /** @return list<string> */
    public function permissions(): array
    {
        return Session::get('permissions', []);
    }
}
