<?php

declare(strict_types=1);

/**
 * Roles disponibles al crear/editar usuarios según rubro del comercio.
 * Slugs deben existir en tabla roles (ver migraciones).
 */
return [
    'default' => ['owner', 'manager', 'seller', 'warehouse', 'accountant'],
    'by_business_type' => [
        'taller' => ['owner', 'manager', 'mechanic', 'seller', 'warehouse', 'accountant'],
    ],
];
