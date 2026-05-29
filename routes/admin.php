<?php

declare(strict_types=1);

use App\Controllers\Platform\AuthController;
use App\Controllers\Platform\TenantController;

return function ($router, array $mw) {
    $adminAuth = [$mw['csrf'], $mw['platform_auth']];
    $adminGuest = [$mw['csrf'], $mw['platform_guest']];

    $router->get('/admin/login', [AuthController::class, 'showLogin'], $adminGuest);
    $router->post('/admin/login', [AuthController::class, 'login'], $adminGuest);
    $router->post('/admin/logout', [AuthController::class, 'logout'], $adminAuth);

    $router->get('/admin', function () {
        header('Location: ' . url('/admin/tenants'));
        exit;
    }, $adminAuth);

    $router->get('/admin/tenants', [TenantController::class, 'index'], $adminAuth);
    $router->get('/admin/tenants/create', [TenantController::class, 'create'], $adminAuth);
    $router->post('/admin/tenants', [TenantController::class, 'store'], $adminAuth);
    $router->get('/admin/tenants/{id}', [TenantController::class, 'show'], $adminAuth);
    $router->post('/admin/tenants/{id}', [TenantController::class, 'update'], $adminAuth);
    $router->post('/admin/tenants/{id}/toggle', [TenantController::class, 'toggle'], $adminAuth);
    $router->post('/admin/tenants/{id}/delete', [TenantController::class, 'destroy'], $adminAuth);
    $router->post('/admin/tenants/{id}/enter', [TenantController::class, 'enter'], $adminAuth);
    $router->post('/admin/stop-impersonate', [AuthController::class, 'stopImpersonate'], $adminAuth);
};
