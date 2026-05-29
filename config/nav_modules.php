<?php

declare(strict_types=1);

return [
    'products' => ['href' => '/products', 'label' => 'Productos', 'perm' => 'products.manage'],
    'stock' => ['href' => '/inventory', 'label' => 'Inventario rápido', 'perm' => 'stock.manage'],
    'pos' => ['href' => '/pos', 'label' => 'Punto de venta', 'perm' => 'pos.sell'],
    'suppliers' => ['href' => '/suppliers', 'label' => 'Proveedores', 'perm' => 'suppliers.manage'],
    'customers' => ['href' => '/customers', 'label' => 'Clientes', 'perm' => 'customers.manage'],
    'purchases' => ['href' => '/purchases', 'label' => 'Compras', 'perm' => 'purchases.manage'],
    'cash' => ['href' => '/cash', 'label' => 'Caja', 'perm' => 'cash.manage'],
    'reports' => ['href' => '/reports', 'label' => 'Reportes', 'perm' => 'reports.view'],
    'categories' => ['href' => '/categories', 'label' => 'Categorías', 'perm' => 'products.manage'],
    'brands' => ['href' => '/brands', 'label' => 'Marcas', 'perm' => 'products.manage'],
    'work_orders' => ['href' => '/work-orders', 'label' => 'Órdenes de trabajo', 'perm' => 'work_orders.manage'],
    'bar' => ['href' => '/bar', 'label' => 'Barras', 'perm' => 'pos.sell'],
    'entries' => ['href' => '/entries', 'label' => 'Entradas', 'perm' => 'pos.sell'],
    'memberships' => ['href' => '/memberships', 'label' => 'Membresías', 'perm' => 'products.manage'],
];
