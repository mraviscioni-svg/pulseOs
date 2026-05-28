<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Session;
use App\Core\TenantContext;

final class TenantMiddleware
{
    public function __invoke(callable $next): void
    {
        $tenantId = Session::get('tenant_id');
        if (!$tenantId) {
            header('Location: ' . url('/login'));
            exit;
        }

        TenantContext::set((int) $tenantId);
        $next();
    }
}
