<?php

declare(strict_types=1);

use App\Core\Application;
use App\Core\Csrf;
use App\Core\Session;

function app(): Application
{
    static $app;
    if (!$app) {
        $app = require __DIR__ . '/bootstrap.php';
    }

    return $app;
}

function config(string $file): array
{
    return app()->config($file);
}

/** Nombre de la app para UI: siempre «PulseOS». */
function app_name(): string
{
    $name = (string) (config('app')['name'] ?? 'PulseOS');

    return $name !== '' ? $name : 'PulseOS';
}

/** Ruta base de la app (ej. /PulseOS-prep/public) */
function base_path(): string
{
    return rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
}

/** Path para cookie de sesión en subcarpetas */
function session_cookie_path(): string
{
    $base = base_path();

    return $base === '' ? '/' : $base . '/';
}

function is_https_request(): bool
{
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        return true;
    }

    return ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
}

function url(string $path = ''): string
{
    $base = base_path();
    $path = '/' . ltrim($path, '/');

    return $base . ($path === '/' ? '' : $path);
}

function asset(string $path): string
{
    return url('/assets/' . ltrim($path, '/'));
}

/** URL de asset con versión por fecha de modificación (evita caché vieja en el hosting). */
function asset_version(string $path): string
{
    $file = dirname(__DIR__) . '/public/assets/' . ltrim($path, '/');
    $version = is_file($file) ? (string) filemtime($file) : '1';

    return asset($path) . '?v=' . rawurlencode($version);
}

/** URL de login del comercio con slug y usuario opcional para prellenar el acceso. */
function tenant_login_url(?string $slug = null, ?string $username = null): string
{
    $query = array_filter([
        'tenant' => $slug !== null && $slug !== '' ? $slug : null,
        'username' => $username !== null && $username !== '' ? $username : null,
    ], static fn ($v) => $v !== null && $v !== '');

    return url('/login' . ($query !== [] ? '?' . http_build_query($query) : ''));
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

/** @return list<string> Slugs de roles asignables para un rubro de comercio. */
function roles_for_business_type(?string $businessType = null): array
{
    $businessType ??= (string) Session::get('business_type', 'otro');
    $cfg = config('tenant_roles');

    return $cfg['by_business_type'][$businessType] ?? $cfg['default'];
}

/** @return array<string, mixed> Paleta de marca (variante A por defecto en UI). */
function brand_palette(?string $theme = null): array
{
    $brand = config('brand');
    $theme ??= (string) ($brand['default_theme'] ?? 'light');

    return $theme === 'dark' ? $brand['dark'] : $brand['light'];
}

function wo_status_label(string $status): string
{
    $labels = config('work_order_statuses')['labels'] ?? [];

    return $labels[$status] ?? $status;
}

function wo_status_badge_class(string $status): string
{
    $map = config('work_order_statuses')['badge'] ?? [];
    $key = $map[$status] ?? 'muted';

    return match ($key) {
        'success' => 'badge badge-success',
        'warning' => 'badge badge-warning',
        'info' => 'badge badge-warning',
        default => 'badge badge-muted',
    };
}

function is_platform_admin(): bool
{
    return \App\Services\PlatformAuthService::check();
}

function has_tenant_session(): bool
{
    return \App\Services\TenantSessionService::isLoggedIn();
}

function is_platform_impersonating(): bool
{
    return (bool) \App\Core\Session::get('platform_impersonating');
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
