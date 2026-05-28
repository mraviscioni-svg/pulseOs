<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Session;

final class AuthMiddleware
{
    public function __invoke(callable $next): void
    {
        if (!Session::get('user_id')) {
            header('Location: ' . url('/login'));
            exit;
        }

        $next();
    }
}
