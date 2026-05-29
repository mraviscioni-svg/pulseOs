<?php

declare(strict_types=1);

/** Geometría del isotipo (viewBox recortado al dibujo, sin margen derecho). */
return [
    'viewBox' => '0 6 48 30',
    'stroke' => '2.2',
    'stroke_detail' => '1.5',
    'pulse_path' => 'M2 21 H9 L11.25 17 L13.5 21 L20 21',
    'pulse_dot' => ['cx' => 11.25, 'cy' => 16.25, 'r' => 1.6],
    'building' => ['x' => 20, 'y' => 8, 'w' => 26, 'h' => 26, 'rx' => 6],
    'building_line' => 'M20 21 H46',
    'door' => ['x' => 28.5, 'y' => 23, 'w' => 9, 'h' => 7],
    'windows' => [
        ['x' => 24, 'y' => 12.5, 'w' => 4.5, 'h' => 4.5],
        ['x' => 37.5, 'y' => 12.5, 'w' => 4.5, 'h' => 4.5],
    ],
];
