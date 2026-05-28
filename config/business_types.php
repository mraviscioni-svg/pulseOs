<?php

declare(strict_types=1);

return [
    'kiosco' => [
        'label' => 'Kiosco',
        'modules' => ['products', 'pos', 'stock', 'cash', 'purchases', 'suppliers'],
    ],
    'almacen' => [
        'label' => 'Almacén',
        'modules' => ['products', 'pos', 'stock', 'cash', 'purchases', 'suppliers'],
    ],
    'taller' => [
        'label' => 'Taller mecánico',
        'modules' => ['products', 'pos', 'stock', 'cash', 'purchases', 'suppliers', 'work_orders'],
    ],
    'boliche' => [
        'label' => 'Boliche',
        'modules' => ['products', 'pos', 'stock', 'cash', 'entries', 'bar'],
    ],
    'ropa' => [
        'label' => 'Tienda de ropa',
        'modules' => ['products', 'pos', 'stock', 'cash', 'purchases', 'variants'],
    ],
    'ferreteria' => [
        'label' => 'Ferretería',
        'modules' => ['products', 'pos', 'stock', 'cash', 'purchases', 'suppliers'],
    ],
    'distribuidora' => [
        'label' => 'Distribuidora',
        'modules' => ['products', 'pos', 'stock', 'cash', 'purchases', 'suppliers'],
    ],
    'gimnasio' => [
        'label' => 'Gimnasio',
        'modules' => ['products', 'pos', 'cash', 'memberships'],
    ],
    'otro' => [
        'label' => 'Otro comercio',
        'modules' => ['products', 'pos', 'stock', 'cash', 'purchases', 'suppliers'],
    ],
];
