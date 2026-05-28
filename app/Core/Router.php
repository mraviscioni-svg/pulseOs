<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    /** @var array<int, array{method: string, path: string, handler: callable|array, middleware: array}> */
    private array $routes = [];

    public function get(string $path, callable|array $handler, array $middleware = []): void
    {
        $this->add('GET', $path, $handler, $middleware);
    }

    public function post(string $path, callable|array $handler, array $middleware = []): void
    {
        $this->add('POST', $path, $handler, $middleware);
    }

    public function put(string $path, callable|array $handler, array $middleware = []): void
    {
        $this->add('PUT', $path, $handler, $middleware);
    }

    public function delete(string $path, callable|array $handler, array $middleware = []): void
    {
        $this->add('DELETE', $path, $handler, $middleware);
    }

    private function add(string $method, string $path, callable|array $handler, array $middleware): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'middleware' => $middleware,
        ];
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $base = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');

        if ($base !== '' && str_starts_with($uri, $base)) {
            $uri = substr($uri, strlen($base)) ?: '/';
        }

        $uri = '/' . trim($uri, '/');
        if ($uri !== '/') {
            $uri = rtrim($uri, '/');
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $params = $this->match($route['path'], $uri);
            if ($params === null) {
                continue;
            }

            $handler = $this->resolveHandler($route['handler'], $params);
            $middlewares = array_map(function ($middleware) {
                if (is_string($middleware)) {
                    return new $middleware();
                }

                return $middleware;
            }, $route['middleware']);

            $pipeline = array_reduce(
                array_reverse($middlewares),
                fn ($next, $middleware) => fn () => $middleware($next),
                $handler
            );

            $pipeline();

            return;
        }

        http_response_code(404);
        View::render('errors/404', ['title' => 'No encontrado']);
    }

    /** @return array<string, string>|null */
    private function match(string $pattern, string $uri): ?array
    {
        $regex = preg_replace('#\{([a-zA-Z_]+)\}#', '(?P<$1>[^/]+)', $pattern);
        $regex = '#^' . $regex . '$#';

        if (!preg_match($regex, $uri, $matches)) {
            return null;
        }

        $params = [];
        foreach ($matches as $key => $value) {
            if (!is_int($key)) {
                $params[$key] = $value;
            }
        }

        return $params;
    }

    /** @param array<string, string> $params */
    private function resolveHandler(callable|array $handler, array $params): callable
    {
        if (is_callable($handler)) {
            return fn () => $handler($params);
        }

        [$class, $method] = $handler;
        $controller = new $class();

        return fn () => $controller->$method($params);
    }
}
