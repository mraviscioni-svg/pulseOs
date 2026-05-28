<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;
use App\Models\SettingsModel;
use App\Services\ModuleService;

final class SettingsController extends Controller
{
    public function index(): void
    {
        $tenantId = $this->tenantId();
        $db = Database::connection();
        $tenant = $db->prepare('SELECT * FROM tenants WHERE id = :id LIMIT 1');
        $tenant->execute(['id' => $tenantId]);
        $settings = (new SettingsModel())->get($tenantId);
        $modules = json_decode((string) ($settings['modules_json'] ?? '[]'), true) ?: [];

        $this->view('settings/index', [
            'title' => 'Configuración',
            'tenant' => $tenant->fetch(),
            'settings' => $settings,
            'activeModules' => $modules,
            'allModules' => config('nav_modules'),
            'businessTypes' => config('business_types'),
        ]);
    }

    public function update(): void
    {
        $data = $this->input();
        $modules = $data['modules'] ?? [];
        if (!is_array($modules)) {
            $modules = [];
        }
        $data['modules'] = array_values($modules);

        (new SettingsModel())->update($this->tenantId(), $data);
        (new ModuleService())->loadIntoSession($this->tenantId());
        Session::set('tenant_name', $data['tenant_name']);
        Session::flash('success', 'Configuración guardada.');
        $this->redirect('/settings');
    }
}
