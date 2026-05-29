<?php

declare(strict_types=1);

namespace App\Controllers\Platform;

use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Controllers\Concerns\ExportableList;
use App\Models\UserModel;
use App\Services\AuditService;
use App\Services\AuthService;
use App\Services\PlatformTenantService;
use App\Services\TenantRegistrationService;

final class TenantController extends Controller
{
    use ExportableList;

    public function index(): void
    {
        $q = trim((string) ($_GET['q'] ?? ''));
        $tenants = (new PlatformTenantService())->list($q ?: null);

        $rows = [];
        foreach ($tenants as $t) {
            $rows[] = [
                $t['name'],
                $t['slug'],
                $t['business_type'],
                $t['users_count'],
                $t['products_count'],
                $t['sales_total'],
                $t['is_active'] ? 'Activo' : 'Suspendido',
                $t['created_at'],
            ];
        }

        $this->maybeExportList(
            'Comercios ' . app_name(),
            ['Nombre', 'Slug', 'Rubro', 'Usuarios', 'Productos', 'Ventas', 'Estado', 'Alta'],
            $rows,
            'comercios'
        );

        $this->view('admin/tenants/index', [
            'title' => 'Gestión de tenants',
            'tenants' => $tenants,
            'q' => $q,
        ], 'layouts/admin');
    }

    public function create(): void
    {
        $defaultType = (string) old('business_type', 'otro');
        $defaultModules = config('business_types')[$defaultType]['modules'] ?? [];

        $this->view('admin/tenants/create', [
            'title' => 'Nuevo comercio',
            'businessTypes' => config('business_types'),
            'moduleLabels' => config('platform_modules'),
            'activeModules' => is_array(old('modules')) ? old('modules') : $defaultModules,
        ], 'layouts/admin');
    }

    public function store(): void
    {
        $data = $this->input();
        $data['username'] = normalize_username((string) ($data['username'] ?? ''));

        $validator = new Validator();
        if (!$validator->validate($data, [
            'company_name' => 'required|min:2',
            'owner_name' => 'required|min:2',
            'username' => 'required|min:3',
            'email' => 'required|email',
            'password' => 'required|min:8',
            'business_type' => 'required',
        ])) {
            Session::flash('error', implode(' ', $validator->errors()));
            Session::set('_old', $data);
            $this->redirect('/admin/tenants/create');
        }

        $data['modules'] = is_array($data['modules'] ?? null) ? array_values($data['modules']) : null;

        try {
            $tenantId = (new TenantRegistrationService())->register($data);
            (new AuditService())->log(null, null, 'platform.tenant.created', 'tenant', $tenantId);

            $this->redirect('/admin/tenants/' . $tenantId);
        } catch (\Throwable $e) {
            Session::flash('error', 'No se pudo crear: ' . $e->getMessage());
            Session::set('_old', $data);
            $this->redirect('/admin/tenants/create');
        }
    }

    public function show(array $params): void
    {
        $detail = (new PlatformTenantService())->detail((int) $params['id']);
        if (!$detail) {
            Session::flash('error', 'Tenant no encontrado.');
            $this->redirect('/admin/tenants');
        }

        $settings = $detail['settings'] ?? [];
        if (!is_array($settings)) {
            $settings = [];
        }
        $activeModules = json_decode((string) ($settings['modules_json'] ?? '[]'), true) ?: [];
        if ($activeModules === []) {
            $activeModules = config('business_types')[$detail['tenant']['business_type']]['modules'] ?? [];
        }

        $ownerUser = null;
        foreach ($detail['users'] as $u) {
            $role = strtolower((string) ($u['role_name'] ?? ''));
            if (str_contains($role, 'owner') || str_contains($role, 'dueño') || str_contains($role, 'propietario')) {
                $ownerUser = $u;
                break;
            }
        }
        if (!$ownerUser && !empty($detail['users'][0])) {
            $ownerUser = $detail['users'][0];
        }

        $ownerUserId = (new UserModel())->findOwnerUserIdForTenant((int) $detail['tenant']['id']);

        $this->view('admin/tenants/show', [
            'title' => $detail['tenant']['name'],
            'detail' => $detail,
            'businessTypes' => config('business_types'),
            'moduleLabels' => config('platform_modules'),
            'activeModules' => $activeModules,
            'ownerUserId' => $ownerUserId,
            'ownerUser' => $ownerUser,
            'tenantLoginUrl' => tenant_login_url(
                (string) $detail['tenant']['slug'],
                $ownerUser['username'] ?? null
            ),
        ], 'layouts/admin');
    }

    public function enter(array $params): void
    {
        $tenantId = (int) $params['id'];
        $userId = (new UserModel())->findOwnerUserIdForTenant($tenantId);

        if (!$userId) {
            Session::flash('error', 'Este comercio no tiene un usuario activo para ingresar.');
            $this->redirect('/admin/tenants/' . $tenantId);
        }

        if (!(new AuthService())->impersonateTenantUser($userId, $tenantId)) {
            Session::flash('error', 'No se pudo abrir el panel del comercio.');
            $this->redirect('/admin/tenants/' . $tenantId);
        }

        (new AuditService())->log(null, null, 'platform.tenant.enter', 'tenant', $tenantId);
        Session::flash('success', 'Ingresaste al panel del comercio. Usá «Volver al admin» para salir.');
        $this->redirect('/dashboard');
    }

    public function update(array $params): void
    {
        $tenantId = (int) $params['id'];
        $data = $this->input();

        $validator = new Validator();
        if (!$validator->validate($data, [
            'name' => 'required|min:2',
            'email' => 'required|email',
            'business_type' => 'required',
        ])) {
            Session::flash('error', implode(' ', $validator->errors()));
            $this->redirect('/admin/tenants/' . $tenantId);
        }

        try {
            (new PlatformTenantService())->update($tenantId, [
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'business_type' => $data['business_type'],
                'modules' => is_array($data['modules'] ?? null) ? array_values($data['modules']) : [],
            ]);
            (new AuditService())->log(null, null, 'platform.tenant.updated', 'tenant', $tenantId);
            Session::flash('success', 'Comercio actualizado.');
        } catch (\Throwable $e) {
            Session::flash('error', 'Error al guardar: ' . $e->getMessage());
        }

        $this->redirect('/admin/tenants/' . $tenantId);
    }

    public function toggle(array $params): void
    {
        $data = $this->input();
        $active = !empty($data['is_active']);
        $tenantId = (int) $params['id'];

        (new PlatformTenantService())->setActive($tenantId, $active);
        (new AuditService())->log(null, null, $active ? 'platform.tenant.activated' : 'platform.tenant.deactivated', 'tenant', $tenantId);

        Session::flash('success', $active ? 'Tenant activado.' : 'Tenant desactivado.');

        if (($this->input()['return'] ?? '') === 'list') {
            $this->redirect('/admin/tenants');
        }

        $this->redirect('/admin/tenants/' . $tenantId);
    }

    public function destroy(array $params): void
    {
        $tenantId = (int) $params['id'];
        $service = new PlatformTenantService();
        $detail = $service->detail($tenantId);

        if (!$detail) {
            Session::flash('error', 'Comercio no encontrado.');
            $this->redirect('/admin/tenants');
        }

        $tenant = $detail['tenant'];
        $confirmSlug = strtolower(trim((string) ($this->input()['confirm_slug'] ?? '')));
        $expectedSlug = strtolower((string) ($tenant['slug'] ?? ''));

        if ($confirmSlug === '' || $confirmSlug !== $expectedSlug) {
            Session::flash('error', 'Escribí el slug exacto («' . $expectedSlug . '») para confirmar la eliminación.');
            $this->redirect('/admin/tenants/' . $tenantId);
        }

        try {
            $name = (string) $tenant['name'];
            $service->delete($tenantId);
            (new AuditService())->log(null, null, 'platform.tenant.deleted', 'tenant', $tenantId, [
                'name' => $name,
                'slug' => $expectedSlug,
            ]);
            Session::flash('success', 'Comercio «' . $name . '» eliminado definitivamente.');
        } catch (\Throwable $e) {
            Session::flash('error', 'No se pudo eliminar: ' . $e->getMessage());
            $this->redirect('/admin/tenants/' . $tenantId);
        }

        $this->redirect('/admin/tenants');
    }
}
