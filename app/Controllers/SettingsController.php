<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;
use App\Models\SettingsModel;

final class SettingsController extends Controller
{
    public function index(): void
    {
        $tenantId = $this->tenantId();
        $db = Database::connection();
        $tenant = $db->prepare('SELECT * FROM tenants WHERE id = :id LIMIT 1');
        $tenant->execute(['id' => $tenantId]);
        $settings = (new SettingsModel())->get($tenantId);

        $this->view('settings/index', [
            'title' => 'Configuración',
            'tenant' => $tenant->fetch(),
            'settings' => $settings,
        ]);
    }

    public function update(): void
    {
        $data = $this->input();

        (new SettingsModel())->update($this->tenantId(), $data);
        Session::set('tenant_name', $data['tenant_name']);
        Session::flash('success', 'Configuración guardada.');
        $this->redirect('/settings');
    }
}
