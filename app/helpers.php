<?php

declare(strict_types=1);

use App\Core\Application;
use App\Core\Csrf;
use App\Core\Session;

function app(): Application
{
    static $app;
    if (!$app) {
        $app = require dirname(__DIR__) . '/bootstrap.php';
    }

    return $app;
}

function config(string $file): array
{
    return app()->config($file);
}

function url(string $path = ''): string
{
    $base = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
    $path = '/' . ltrim($path, '/');

    return $base . ($path === '/' ? '' : $path);
}

function asset(string $path): string
{
    return url('/assets/' . ltrim($path, '/'));
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . e(Csrf::token()) . '">';
}

function old(string $key, mixed $default = ''): mixed
{
    $old = Session::get('_old');
    if (is_array($old)) {
        return $old[$key] ?? $default;
    }

    return $default;
}

function money(float|int|string $amount): string
{
    return '$ ' . number_format((float) $amount, 2, ',', '.');
}

function can(string $permission): bool
{
    $permissions = Session::get('permissions', []);

    return in_array($permission, $permissions, true) || in_array('*', $permissions, true);
}

function module_enabled(string $module): bool
{
    return \App\Services\ModuleService::enabled($module);
}

function is_platform_admin(): bool
{
    return \App\Services\PlatformAuthService::check();
}

function normalize_username(string $value): string
{
    $value = strtolower(trim($value));
    $value = preg_replace('/[^a-z0-9._-]/', '', $value) ?? '';

    return $value;
}

function upload_url(?string $path): string
{
    return $path ? url('/' . ltrim($path, '/')) : '';
}
