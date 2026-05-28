<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Services\PlatformAuthService;

final class PlatformGuestMiddleware
{
    public function __invoke(callable $next): void
    {
        if (PlatformAuthService::check()) {
            header('Location: ' . url('/admin/tenants'));
            exit;
        }

        $next();
    }
}
