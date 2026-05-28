<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Session;

final class GuestMiddleware
{
    public function __invoke(callable $next): void
    {
        if (Session::get('user_id')) {
            header('Location: ' . url('/dashboard'));
            exit;
        }

        $next();
    }
}
