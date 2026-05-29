<?php

declare(strict_types=1);

/**
 * Marca PulseOS — variante activa en la app.
 *
 * A (fondos claros): edificio navy, pulso dorado, wordmark Pulse navy + OS dorado.
 * logo-dark.svg queda para fondos oscuros (marketing) con logoTheme = dark.
 */
return [
    'variant' => 'A',
    'default_theme' => 'light',

    'navy' => '#0c1524',
    'gold' => '#E8B44A',
    'tagline' => '#64748b',

    'light' => [
        'structure' => '#0c1524',
        'pulse' => '#E8B44A',
        'word_pulse' => '#0c1524',
        'word_os' => '#E8B44A',
    ],

    'dark' => [
        'structure' => '#ffffff',
        'pulse' => '#E8B44A',
        'word_pulse' => '#ffffff',
        'word_os' => '#E8B44A',
    ],
];
