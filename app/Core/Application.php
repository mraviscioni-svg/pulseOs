<?php

declare(strict_types=1);

namespace App\Core;

use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;
use App\Middleware\GuestMiddleware;
use App\Middleware\PermissionMiddleware;
use App\Middleware\PlatformAuthMiddleware;
use App\Middleware\PlatformGuestMiddleware;
use App\Middleware\TenantMiddleware;

final class Application
{
    private static ?self $instance = null;

    private Router $router;

    private function __construct(
        private readonly string $root,
    ) {
        $this->router = new Router();
    }

    public static function boot(string $root): self
    {
        if (self::$instance === null) {
            self::$instance = new self($root);
            self::$instance->registerRoutes();
        }

        return self::$instance;
    }

    public function root(): string
    {
        return $this->root;
    }

    public function config(string $file): array
    {
        return require $this->root . '/config/' . $file . '.php';
    }

    public function run(): void
    {
        $debug = (bool) ($this->config('app')['debug'] ?? false);
        set_exception_handler(function (\Throwable $e) use ($debug): void {
            http_response_code(500);
            if ($debug) {
                View::render('errors/500', [
                    'title' => 'Error',
                    'message' => $e->getMessage(),
                    'debug' => true,
                ], null);
            } else {
                View::render('errors/500', ['title' => 'Error'], null);
            }
        });

        Session::start($this->config('app')['session_lifetime'] ?? 7200);
        $this->router->dispatch();
    }

    public function router(): Router
    {
        return $this->router;
    }

    private function registerRoutes(): void
    {
        $middleware = [
            'auth' => AuthMiddleware::class,
            'guest' => GuestMiddleware::class,
            'tenant' => TenantMiddleware::class,
            'csrf' => CsrfMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'platform_auth' => PlatformAuthMiddleware::class,
            'platform_guest' => PlatformGuestMiddleware::class,
        ];

        $registerWeb = require $this->root . '/routes/web.php';
        $registerWeb($this->router, $middleware);

        $registerAdmin = require $this->root . '/routes/admin.php';
        $registerAdmin($this->router, $middleware);
    }
}
