<?php

declare(strict_types=1);

/** Estados y transiciones de órdenes de trabajo. */
return [
    'labels' => [
        'borrador' => 'Borrador',
        'presupuestada' => 'Presupuestada',
        'aprobada' => 'Aprobada',
        'en_taller' => 'En taller',
        'en_espera' => 'En espera',
        'lista_entregar' => 'Lista para entregar',
        'cerrada' => 'Cerrada',
        'cancelada' => 'Cancelada',
    ],
    'badge' => [
        'borrador' => 'muted',
        'presupuestada' => 'warning',
        'aprobada' => 'info',
        'en_taller' => 'info',
        'en_espera' => 'warning',
        'lista_entregar' => 'success',
        'cerrada' => 'success',
        'cancelada' => 'muted',
    ],
    /** Estado => acciones disponibles (target => etiqueta botón) */
    'actions' => [
        'borrador' => [
            'presupuestada' => 'Enviar presupuesto',
            'cancelada' => 'Cancelar',
        ],
        'presupuestada' => [
            'aprobada' => 'Aprobar',
            'borrador' => 'Volver a borrador',
            'cancelada' => 'Cancelar',
        ],
        'aprobada' => [
            'en_taller' => 'Ingresar a taller',
            'cancelada' => 'Cancelar',
        ],
        'en_taller' => [
            'en_espera' => 'Marcar en espera',
            'lista_entregar' => 'Trabajo terminado',
            'cancelada' => 'Cancelar',
        ],
        'en_espera' => [
            'en_taller' => 'Reanudar trabajo',
            'cancelada' => 'Cancelar',
        ],
        'lista_entregar' => [
            'cerrada' => 'Cerrar sin cobrar en POS',
        ],
        'cerrada' => [],
        'cancelada' => [],
    ],
    'editable' => ['borrador', 'presupuestada', 'aprobada', 'en_taller', 'en_espera', 'lista_entregar'],
];
