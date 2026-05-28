<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Csrf;
use App\Core\Session;
use App\Core\View;

final class CsrfMiddleware
{
    public function __invoke(callable $next): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if (in_array($method, ['POST', 'PUT', 'DELETE'], true)) {
            $token = $_POST['_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
            if (!Csrf::validate(is_string($token) ? $token : null)) {
                http_response_code(419);
                View::render('errors/419', ['title' => 'Token inválido'], null);
                exit;
            }
        }

        $next();
    }
}
