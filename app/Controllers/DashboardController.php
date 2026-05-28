<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Services\DashboardService;

final class DashboardController extends Controller
{
    public function index(): void
    {
        $stats = (new DashboardService())->stats($this->tenantId());
        $this->view('dashboard/index', [
            'title' => 'Dashboard',
            'stats' => $stats,
        ]);
    }
}
