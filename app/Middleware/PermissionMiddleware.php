<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Session;
use App\Core\View;

final class PermissionMiddleware
{
    public static function require(string $permission): callable
    {
        return function (callable $next) use ($permission) {
            $permissions = Session::get('permissions', []);
            $allowed = in_array('*', $permissions, true) || in_array($permission, $permissions, true);

            if (!$allowed) {
                http_response_code(403);
                View::render('errors/403', ['title' => 'Sin permiso'], null);
                exit;
            }

            $next();
        };
    }
}
