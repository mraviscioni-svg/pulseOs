<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

final class ModulePlaceholderController extends Controller
{
    public function show(): void
    {
        $uri = (string) (parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '');
        $module = match (true) {
            str_contains($uri, 'work-orders') => 'work_orders',
            str_contains($uri, 'memberships') => 'memberships',
            str_contains($uri, 'entries') => 'entries',
            str_contains($uri, 'bar') => 'bar',
            default => 'modulo',
        };
        $labels = [
            'work_orders' => 'Órdenes de trabajo',
            'bar' => 'Gestión de barras',
            'entries' => 'Entradas y accesos',
            'memberships' => 'Membresías',
        ];
        $this->view('modules/placeholder', [
            'title' => $labels[$module] ?? 'Módulo',
            'moduleLabel' => $labels[$module] ?? $module,
            'module' => $module,
        ]);
    }
}
