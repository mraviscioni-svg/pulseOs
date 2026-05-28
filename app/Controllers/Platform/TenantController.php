<?php

declare(strict_types=1);

namespace App\Controllers\Platform;

use App\Core\Controller;
use App\Core\Session;
use App\Services\AuditService;
use App\Services\PlatformTenantService;

final class TenantController extends Controller
{
    public function index(): void
    {
        $q = trim((string) ($_GET['q'] ?? ''));
        $this->view('admin/tenants/index', [
            'title' => 'Gestión de tenants',
            'tenants' => (new PlatformTenantService())->list($q ?: null),
            'q' => $q,
        ], 'layouts/admin');
    }

    public function show(array $params): void
    {
        $detail = (new PlatformTenantService())->detail((int) $params['id']);
        if (!$detail) {
            Session::flash('error', 'Tenant no encontrado.');
            $this->redirect('/admin/tenants');
        }

        $this->view('admin/tenants/show', [
            'title' => $detail['tenant']['name'],
            'detail' => $detail,
            'businessTypes' => config('business_types'),
        ], 'layouts/admin');
    }

    public function toggle(array $params): void
    {
        $data = $this->input();
        $active = !empty($data['is_active']);
        $tenantId = (int) $params['id'];

        (new PlatformTenantService())->setActive($tenantId, $active);
        (new AuditService())->log(null, null, $active ? 'platform.tenant.activated' : 'platform.tenant.deactivated', 'tenant', $tenantId);

        Session::flash('success', $active ? 'Tenant activado.' : 'Tenant desactivado.');
        $this->redirect('/admin/tenants/' . $tenantId);
    }
}
