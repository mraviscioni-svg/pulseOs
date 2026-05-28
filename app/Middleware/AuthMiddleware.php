<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Session;
use App\Services\PlatformAuthService;

final class AuthMiddleware
{
    public function __invoke(callable $next): void
    {
        if (PlatformAuthService::check()) {
            header('Location: ' . url('/admin/tenants'));
            exit;
        }

        if (!Session::get('user_id')) {
            header('Location: ' . url('/login'));
            exit;
        }

        $next();
    }
}
