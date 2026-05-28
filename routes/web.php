<?php

declare(strict_types=1);

use App\Controllers\Api\ProductApiController;
use App\Controllers\AuthController;
use App\Controllers\CashRegisterController;
use App\Controllers\DashboardController;
use App\Controllers\PosController;
use App\Controllers\ProductController;
use App\Controllers\PurchaseController;
use App\Controllers\SupplierController;
use App\Controllers\UserController;
use App\Middleware\PermissionMiddleware;

return function ($router, array $mw) {
    $auth = [$mw['csrf'], $mw['auth'], $mw['tenant']];
    $guest = [$mw['csrf'], $mw['guest']];

    $router->get('/', function () {
        header('Location: ' . url('/dashboard'));
        exit;
    });
    $router->get('/login', [AuthController::class, 'showLogin'], $guest);
    $router->post('/login', [AuthController::class, 'login'], $guest);
    $router->get('/register', [AuthController::class, 'showRegister'], $guest);
    $router->post('/register', [AuthController::class, 'register'], $guest);
    $router->get('/forgot-password', [AuthController::class, 'showForgotPassword'], $guest);
    $router->post('/forgot-password', [AuthController::class, 'forgotPassword'], $guest);
    $router->post('/logout', [AuthController::class, 'logout'], $auth);

    $router->get('/dashboard', [DashboardController::class, 'index'], $auth);

    $router->get('/products', [ProductController::class, 'index'], $auth);
    $router->get('/products/create', [ProductController::class, 'create'], $auth);
    $router->post('/products', [ProductController::class, 'store'], $auth);
    $router->get('/products/{id}/edit', [ProductController::class, 'edit'], $auth);
    $router->post('/products/{id}', [ProductController::class, 'update'], $auth);
    $router->post('/products/{id}/stock', [ProductController::class, 'adjustStock'], $auth);

    $router->get('/suppliers', [SupplierController::class, 'index'], $auth);
    $router->get('/suppliers/create', [SupplierController::class, 'create'], $auth);
    $router->post('/suppliers', [SupplierController::class, 'store'], $auth);
    $router->get('/suppliers/{id}/edit', [SupplierController::class, 'edit'], $auth);
    $router->post('/suppliers/{id}', [SupplierController::class, 'update'], $auth);

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

    $router->get('/users', [UserController::class, 'index'], array_merge($auth, [PermissionMiddleware::require('users.manage')]));
    $router->post('/users', [UserController::class, 'store'], array_merge($auth, [PermissionMiddleware::require('users.manage')]));

    $router->get('/api/products/search', [ProductApiController::class, 'search'], $auth);
    $router->get('/api/products/barcode', [ProductApiController::class, 'barcode'], $auth);
};
