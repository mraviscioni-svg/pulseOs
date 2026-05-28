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
        $this->view('settings/index', [
            'title' => 'Comercio',
        ]);
    }

    public function comercio(): void
    {
        $this->renderForm('settings/comercio', 'Datos del comercio');
    }

    public function operacion(): void
    {
        $this->renderForm('settings/operacion', 'Operación');
    }

    public function updateComercio(): void
    {
        $data = $this->input();
        (new SettingsModel())->updateTenant($this->tenantId(), $data);
        Session::set('tenant_name', $data['tenant_name']);
        Session::flash('success', 'Datos del comercio guardados.');
        $this->redirect('/settings/comercio');
    }

    public function updateOperacion(): void
    {
        $data = $this->input();
        (new SettingsModel())->updateSettings($this->tenantId(), $data);
        Session::flash('success', 'Preferencias de operación guardadas.');
        $this->redirect('/settings/operacion');
    }

    /** Compatibilidad con POST /settings legacy */
    public function update(): void
    {
        $data = $this->input();
        (new SettingsModel())->update($this->tenantId(), $data);
        Session::set('tenant_name', $data['tenant_name']);
        Session::flash('success', 'Configuración guardada.');
        $this->redirect('/settings');
    }

    private function renderForm(string $view, string $title): void
    {
        $tenantId = $this->tenantId();
        $db = Database::connection();
        $tenant = $db->prepare('SELECT * FROM tenants WHERE id = :id LIMIT 1');
        $tenant->execute(['id' => $tenantId]);
        $settings = (new SettingsModel())->get($tenantId);

        $this->view($view, [
            'title' => $title,
            'tenant' => $tenant->fetch(),
            'settings' => $settings,
        ]);
    }
}
