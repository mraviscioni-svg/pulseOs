<?php

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    protected function view(string $template, array $data = [], ?string $layout = 'layouts/app'): void
    {
        View::render($template, $data, $layout);
    }

    protected function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . url($path));
        exit;
    }

    protected function tenantId(): int
    {
        $id = Session::get('tenant_id');
        if (!$id) {
            throw new \RuntimeException('Tenant no definido en sesión.');
        }

        return (int) $id;
    }

    protected function userId(): int
    {
        return (int) Session::get('user_id');
    }

    /** @return array<string, mixed> */
    protected function input(): array
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (str_contains($contentType, 'application/json')) {
            $json = json_decode(file_get_contents('php://input') ?: '{}', true);

            return is_array($json) ? $json : [];
        }

        return array_merge($_GET, $_POST);
    }
}
