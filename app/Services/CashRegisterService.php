<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Core\TenantContext;
use PDO;

final class CashRegisterService
{
    public function __construct(
        private readonly AuditService $audit = new AuditService(),
    ) {
    }

    /** @return array<string, mixed>|null */
    public function getOpenRegister(int $tenantId): ?array
    {
        $db = Database::connection();
        $stmt = $db->prepare(
            "SELECT * FROM cash_registers WHERE tenant_id = :tenant_id AND status = 'abierta' ORDER BY id DESC LIMIT 1"
        );
        $stmt->execute(['tenant_id' => $tenantId]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function open(float $openingAmount, int $userId): int
    {
        $tenantId = TenantContext::id() ?? throw new \RuntimeException('Sin tenant');
        if ($this->getOpenRegister($tenantId)) {
            throw new \RuntimeException('Ya existe una caja abierta.');
        }

        $db = Database::connection();
        $stmt = $db->prepare(
            "INSERT INTO cash_registers (tenant_id, user_id, opened_at, opening_amount, status)
             VALUES (:tenant_id, :user_id, NOW(), :amount, 'abierta')"
        );
        $stmt->execute([
            'tenant_id' => $tenantId,
            'user_id' => $userId,
            'amount' => $openingAmount,
        ]);

        $id = (int) $db->lastInsertId();
        $this->audit->log($tenantId, $userId, 'cash.opened', 'cash_register', $id);

        return $id;
    }

    public function close(int $registerId, float $closingAmount, int $userId, ?string $notes = null): void
    {
        $tenantId = TenantContext::id() ?? throw new \RuntimeException('Sin tenant');
        $register = $this->findRegister($registerId, $tenantId);
        if (!$register || $register['status'] !== 'abierta') {
            throw new \RuntimeException('Caja no válida.');
        }

        $expected = $this->calculateExpected((int) $register['id'], (float) $register['opening_amount']);
        $difference = $closingAmount - $expected;

        $db = Database::connection();
        $stmt = $db->prepare(
            "UPDATE cash_registers SET status = 'cerrada', closed_at = NOW(),
             closing_amount = :closing, expected_amount = :expected, difference = :diff, notes = :notes
             WHERE id = :id AND tenant_id = :tenant_id"
        );
        $stmt->execute([
            'closing' => $closingAmount,
            'expected' => $expected,
            'diff' => $difference,
            'notes' => $notes,
            'id' => $registerId,
            'tenant_id' => $tenantId,
        ]);

        $this->audit->log($tenantId, $userId, 'cash.closed', 'cash_register', $registerId);
    }

    public function addMovement(
        int $registerId,
        string $type,
        float $amount,
        ?string $description,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?int $userId = null,
    ): void {
        $tenantId = TenantContext::id() ?? throw new \RuntimeException('Sin tenant');

        $db = Database::connection();
        $stmt = $db->prepare(
            'INSERT INTO cash_movements (tenant_id, cash_register_id, user_id, type, amount, description, reference_type, reference_id)
             VALUES (:tenant_id, :register_id, :user_id, :type, :amount, :description, :ref_type, :ref_id)'
        );
        $stmt->execute([
            'tenant_id' => $tenantId,
            'register_id' => $registerId,
            'user_id' => $userId,
            'type' => $type,
            'amount' => $amount,
            'description' => $description,
            'ref_type' => $referenceType,
            'ref_id' => $referenceId,
        ]);
    }

    private function calculateExpected(int $registerId, float $opening): float
    {
        $db = Database::connection();
        $stmt = $db->prepare(
            'SELECT COALESCE(SUM(
                CASE WHEN type IN ("ingreso","venta") THEN amount
                     WHEN type IN ("egreso","retiro") THEN -amount
                     ELSE 0 END
            ), 0) AS balance FROM cash_movements WHERE cash_register_id = :id'
        );
        $stmt->execute(['id' => $registerId]);
        $balance = (float) ($stmt->fetch()['balance'] ?? 0);

        return $opening + $balance;
    }

    /** @return array<string, mixed>|null */
    private function findRegister(int $id, int $tenantId): ?array
    {
        $db = Database::connection();
        $stmt = $db->prepare('SELECT * FROM cash_registers WHERE id = :id AND tenant_id = :tenant_id LIMIT 1');
        $stmt->execute(['id' => $id, 'tenant_id' => $tenantId]);

        return $stmt->fetch() ?: null;
    }
}
