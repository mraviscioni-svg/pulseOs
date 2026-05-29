<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Controller;
use App\Models\CustomerModel;
use App\Models\CustomerVehicleModel;

final class CustomerApiController extends Controller
{
    private function authorizeApi(): void
    {
        if (!can('customers.manage') && !can('work_orders.manage')) {
            $this->json(['error' => 'Sin permiso'], 403);
            exit;
        }
    }

    public function search(): void
    {
        $this->authorizeApi();
        $q = trim((string) ($_GET['q'] ?? ''));
        $customers = (new CustomerModel())->search(
            $this->tenantId(),
            $q !== '' ? $q : null,
            'active',
            25
        );

        $this->json(['data' => $customers]);
    }

    public function show(array $params): void
    {
        $this->authorizeApi();
        $id = (int) $params['id'];
        $customer = (new CustomerModel())->find($id, $this->tenantId());
        if (!$customer || !(int) $customer['is_active']) {
            $this->json(['error' => 'Cliente no encontrado'], 404);
        }

        $vehicles = [];
        if (module_enabled('work_orders')) {
            $vehicles = (new CustomerVehicleModel())->forCustomer($this->tenantId(), $id, true);
        }

        $this->json([
            'data' => [
                'id' => (int) $customer['id'],
                'name' => $customer['name'],
                'company' => $customer['company'],
                'email' => $customer['email'],
                'phone' => $customer['phone'],
                'address' => $customer['address'],
                'vehicles' => $vehicles,
            ],
        ]);
    }
}
