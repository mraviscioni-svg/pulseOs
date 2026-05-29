<?php

declare(strict_types=1);

use App\Controllers\Api\CustomerApiController;
use App\Controllers\Api\ProductApiController;
use App\Controllers\CustomerController;
use App\Controllers\AuthController;
use App\Controllers\CashRegisterController;
use App\Controllers\BrandController;
use App\Controllers\CategoryController;
use App\Controllers\DashboardController;
use App\Controllers\HealthController;
use App\Controllers\InventoryController;
use App\Controllers\ModulePlaceholderController;
use App\Controllers\PosController;
use App\Controllers\ProductController;
use App\Controllers\PurchaseController;
use App\Controllers\ReportController;
use App\Controllers\SettingsController;
use App\Controllers\SupplierController;
use App\Controllers\UserController;
use App\Controllers\WorkOrderController;
use App\Middleware\PermissionMiddleware;

return function ($router, array $mw) {
    $auth = [$mw['csrf'], $mw['auth'], $mw['tenant']];
    $guest = [$mw['csrf'], $mw['guest']];
    $perm = fn (string $p) => PermissionMiddleware::require($p);

    $router->get('/health', [HealthController::class, 'index']);

    $router->get('/', function () {
        header('Location: ' . url('/dashboard'));
        exit;
    });

    $router->get('/login', [AuthController::class, 'showLogin'], $guest);
    $router->post('/login', [AuthController::class, 'login'], $guest);
    // Registro público deshabilitado — alta de comercios solo desde /admin/tenants
    $router->get('/register', function () {
        \App\Core\Session::flash('error', 'El alta de comercios la realiza el administrador de la plataforma.');
        header('Location: ' . url('/login'));
        exit;
    }, $guest);
    $router->post('/register', function () {
        header('Location: ' . url('/login'));
        exit;
    }, $guest);
    $router->get('/forgot-password', [AuthController::class, 'showForgotPassword'], $guest);
    $router->post('/forgot-password', [AuthController::class, 'forgotPassword'], $guest);
    $router->get('/reset-password/{token}', [AuthController::class, 'showResetPassword'], $guest);
    $router->post('/reset-password/{token}', [AuthController::class, 'resetPassword'], $guest);
    $router->post('/logout', [AuthController::class, 'logout'], $auth);

    $router->get('/dashboard', [DashboardController::class, 'index'], $auth);

    $router->get('/products', [ProductController::class, 'index'], $auth);
    $router->get('/products/create', [ProductController::class, 'create'], $auth);
    $router->post('/products', [ProductController::class, 'store'], $auth);
    $router->get('/products/{id}/edit', [ProductController::class, 'edit'], $auth);
    $router->post('/products/{id}', [ProductController::class, 'update'], $auth);
    $router->post('/products/{id}/toggle', [ProductController::class, 'toggle'], $auth);
    $router->post('/products/{id}/delete', [ProductController::class, 'delete'], $auth);
    $router->post('/products/{id}/stock', [ProductController::class, 'adjustStock'], $auth);
    $router->post('/products/{id}/variants', [ProductController::class, 'storeVariant'], $auth);
    $router->post('/products/{id}/variants/{variant_id}/delete', [ProductController::class, 'deleteVariant'], $auth);

    $catalogPerm = array_merge($auth, [$perm('products.manage')]);
    $router->get('/categories', [CategoryController::class, 'index'], $catalogPerm);
    $router->get('/categories/create', [CategoryController::class, 'create'], $catalogPerm);
    $router->post('/categories', [CategoryController::class, 'store'], $catalogPerm);
    $router->get('/categories/{id}/edit', [CategoryController::class, 'edit'], $catalogPerm);
    $router->post('/categories/{id}', [CategoryController::class, 'update'], $catalogPerm);
    $router->post('/categories/{id}/delete', [CategoryController::class, 'delete'], $catalogPerm);
    $router->post('/categories/import', [CategoryController::class, 'import'], $catalogPerm);
    $router->post('/categories/suggestions/reset', [CategoryController::class, 'resetSuggestions'], $catalogPerm);
    $router->post('/categories/suggestions/dismiss', [CategoryController::class, 'dismissSuggestions'], $catalogPerm);

    $router->get('/brands', [BrandController::class, 'index'], $catalogPerm);
    $router->get('/brands/create', [BrandController::class, 'create'], $catalogPerm);
    $router->post('/brands', [BrandController::class, 'store'], $catalogPerm);
    $router->get('/brands/{id}/edit', [BrandController::class, 'edit'], $catalogPerm);
    $router->post('/brands/{id}', [BrandController::class, 'update'], $catalogPerm);
    $router->post('/brands/{id}/delete', [BrandController::class, 'delete'], $catalogPerm);
    $router->post('/brands/import', [BrandController::class, 'import'], $catalogPerm);
    $router->post('/brands/suggestions/reset', [BrandController::class, 'resetSuggestions'], $catalogPerm);
    $router->post('/brands/suggestions/dismiss', [BrandController::class, 'dismissSuggestions'], $catalogPerm);

    $router->get('/inventory', [InventoryController::class, 'index'], array_merge($auth, [$perm('stock.manage')]));
    $router->post('/inventory/adjust', [InventoryController::class, 'adjust'], array_merge($auth, [$perm('stock.manage')]));
    $router->post('/inventory/count', [InventoryController::class, 'setCount'], array_merge($auth, [$perm('stock.manage')]));

    $router->get('/suppliers', [SupplierController::class, 'index'], $auth);
    $router->get('/suppliers/create', [SupplierController::class, 'create'], $auth);
    $router->post('/suppliers', [SupplierController::class, 'store'], $auth);
    $router->get('/suppliers/{id}/edit', [SupplierController::class, 'edit'], $auth);
    $router->post('/suppliers/{id}', [SupplierController::class, 'update'], $auth);
    $router->post('/suppliers/{id}/toggle', [SupplierController::class, 'toggle'], $auth);
    $router->post('/suppliers/{id}/delete', [SupplierController::class, 'delete'], $auth);

    $cust = fn () => PermissionMiddleware::require('customers.manage');
    $router->get('/customers', [CustomerController::class, 'index'], array_merge($auth, [$cust()]));
    $router->get('/customers/create', [CustomerController::class, 'create'], array_merge($auth, [$cust()]));
    $router->post('/customers', [CustomerController::class, 'store'], array_merge($auth, [$cust()]));
    $router->get('/customers/{id}', [CustomerController::class, 'show'], array_merge($auth, [$cust()]));
    $router->get('/customers/{id}/edit', [CustomerController::class, 'edit'], array_merge($auth, [$cust()]));
    $router->post('/customers/{id}', [CustomerController::class, 'update'], array_merge($auth, [$cust()]));
    $router->post('/customers/{id}/toggle', [CustomerController::class, 'toggle'], array_merge($auth, [$cust()]));
    $router->post('/customers/{id}/delete', [CustomerController::class, 'delete'], array_merge($auth, [$cust()]));
    $router->post('/customers/{id}/vehicles', [CustomerController::class, 'storeVehicle'], array_merge($auth, [$cust()]));
    $router->post('/customers/{id}/vehicles/{vehicle_id}/delete', [CustomerController::class, 'deleteVehicle'], array_merge($auth, [$cust()]));

    $router->get('/purchases', [PurchaseController::class, 'index'], $auth);
    $router->get('/purchases/create', [PurchaseController::class, 'create'], $auth);
    $router->post('/purchases', [PurchaseController::class, 'store'], $auth);
    $router->post('/purchases/{id}/receive', [PurchaseController::class, 'receive'], $auth);

    $router->get('/pos', [PosController::class, 'index'], $auth);
    $router->post('/pos/complete', [PosController::class, 'complete'], $auth);
    $router->get('/pos/ticket/{id}', [PosController::class, 'ticket'], $auth);

    $router->get('/cash', [CashRegisterController::class, 'index'], $auth);
    $router->post('/cash/open', [CashRegisterController::class, 'open'], $auth);
    $router->post('/cash/close', [CashRegisterController::class, 'close'], $auth);
    $router->post('/cash/movement', [CashRegisterController::class, 'movement'], $auth);

    $router->get('/reports', [ReportController::class, 'index'], array_merge($auth, [$perm('reports.view')]));

    $router->get('/users', [UserController::class, 'index'], array_merge($auth, [$perm('users.manage')]));
    $router->post('/users', [UserController::class, 'store'], array_merge($auth, [$perm('users.manage')]));
    $router->get('/users/{id}/edit', [UserController::class, 'edit'], array_merge($auth, [$perm('users.manage')]));
    $router->post('/users/{id}', [UserController::class, 'update'], array_merge($auth, [$perm('users.manage')]));
    $router->post('/users/{id}/toggle', [UserController::class, 'toggle'], array_merge($auth, [$perm('users.manage')]));
    $router->post('/users/{id}/delete', [UserController::class, 'delete'], array_merge($auth, [$perm('users.manage')]));

    $router->get('/settings', [SettingsController::class, 'index'], array_merge($auth, [$perm('settings.manage')]));
    $router->get('/settings/comercio', [SettingsController::class, 'comercio'], array_merge($auth, [$perm('settings.manage')]));
    $router->get('/settings/operacion', [SettingsController::class, 'operacion'], array_merge($auth, [$perm('settings.manage')]));
    $router->post('/settings/comercio', [SettingsController::class, 'updateComercio'], array_merge($auth, [$perm('settings.manage')]));
    $router->post('/settings/operacion', [SettingsController::class, 'updateOperacion'], array_merge($auth, [$perm('settings.manage')]));
    $router->post('/settings', [SettingsController::class, 'update'], array_merge($auth, [$perm('settings.manage')]));

    $wo = fn () => PermissionMiddleware::require('work_orders.manage');
    $router->get('/work-orders', [WorkOrderController::class, 'index'], array_merge($auth, [$wo()]));
    $router->get('/work-orders/create', [WorkOrderController::class, 'create'], array_merge($auth, [$wo()]));
    $router->post('/work-orders', [WorkOrderController::class, 'store'], array_merge($auth, [$wo()]));
    $router->get('/work-orders/{id}', [WorkOrderController::class, 'show'], array_merge($auth, [$wo()]));
    $router->get('/work-orders/{id}/edit', [WorkOrderController::class, 'edit'], array_merge($auth, [$wo()]));
    $router->post('/work-orders/{id}', [WorkOrderController::class, 'update'], array_merge($auth, [$wo()]));
    $router->post('/work-orders/{id}/status', [WorkOrderController::class, 'status'], array_merge($auth, [$wo()]));
    $router->post('/work-orders/{id}/consume', [WorkOrderController::class, 'consume'], array_merge($auth, [$wo()]));
    $router->post('/work-orders/{id}/charge', [WorkOrderController::class, 'charge'], array_merge($auth, [$wo()]));
    $router->post('/work-orders/{id}/close', [WorkOrderController::class, 'close'], array_merge($auth, [$wo()]));

    $placeholder = [ModulePlaceholderController::class, 'show'];
    $router->get('/bar', $placeholder, $auth);
    $router->get('/entries', $placeholder, $auth);
    $router->get('/memberships', $placeholder, $auth);

    $router->get('/api/products/search', [ProductApiController::class, 'search'], $auth);
    $router->get('/api/products/barcode', [ProductApiController::class, 'barcode'], $auth);
    $router->get('/api/customers/search', [CustomerApiController::class, 'search'], $auth);
    $router->get('/api/customers/{id}', [CustomerApiController::class, 'show'], $auth);
};
