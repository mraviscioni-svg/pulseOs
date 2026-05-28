<?php

declare(strict_types=1);

use App\Controllers\Api\ProductApiController;

/**
 * API REST interna PulseOS.
 * Las rutas se registran también en web.php para el MVP.
 */
return [
    'GET /api/products/search' => [ProductApiController::class, 'search'],
    'GET /api/products/barcode' => [ProductApiController::class, 'barcode'],
];
