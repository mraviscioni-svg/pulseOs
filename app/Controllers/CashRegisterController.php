<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;
use App\Services\CashRegisterService;

final class CashRegisterController extends Controller
{
    public function index(): void
    {
        $tenantId = $this->tenantId();
        $service = new CashRegisterService();
        $open = $service->getOpenRegister($tenantId);

        $history = Database::connection()->prepare(
            'SELECT * FROM cash_registers WHERE tenant_id = :tenant_id ORDER BY opened_at DESC LIMIT 20'
        );
        $history->execute(['tenant_id' => $tenantId]);

        $movements = [];
        if ($open) {
            $m = Database::connection()->prepare(
                'SELECT * FROM cash_movements WHERE cash_register_id = :id ORDER BY created_at DESC LIMIT 30'
            );
            $m->execute(['id' => $open['id']]);
            $movements = $m->fetchAll();
        }

        $this->view('cash/index', [
            'title' => 'Caja',
            'openCash' => $open,
            'history' => $history->fetchAll(),
            'movements' => $movements,
        ]);
    }

    public function open(): void
    {
        $data = $this->input();
        try {
            (new CashRegisterService())->open((float) ($data['opening_amount'] ?? 0), $this->userId());
            Session::flash('success', 'Caja abierta.');
        } catch (\Throwable $e) {
            Session::flash('error', $e->getMessage());
        }
        $this->redirect('/cash');
    }

    public function close(): void
    {
        $data = $this->input();
        $open = (new CashRegisterService())->getOpenRegister($this->tenantId());
        if (!$open) {
            Session::flash('error', 'No hay caja abierta.');
            $this->redirect('/cash');
        }
        try {
            (new CashRegisterService())->close(
                (int) $open['id'],
                (float) ($data['closing_amount'] ?? 0),
                $this->userId(),
                $data['notes'] ?? null
            );
            Session::flash('success', 'Caja cerrada.');
        } catch (\Throwable $e) {
            Session::flash('error', $e->getMessage());
        }
        $this->redirect('/cash');
    }

    public function movement(): void
    {
        $data = $this->input();
        $open = (new CashRegisterService())->getOpenRegister($this->tenantId());
        if (!$open) {
            Session::flash('error', 'Abrí la caja primero.');
            $this->redirect('/cash');
        }
        (new CashRegisterService())->addMovement(
            (int) $open['id'],
            $data['type'] ?? 'ingreso',
            (float) ($data['amount'] ?? 0),
            $data['description'] ?? null,
            userId: $this->userId()
        );
        Session::flash('success', 'Movimiento registrado.');
        $this->redirect('/cash');
    }
}
