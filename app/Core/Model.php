<?php

declare(strict_types=1);

namespace App\Core;

use PDO;

abstract class Model
{
    protected PDO $db;

    protected string $table;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    protected function scopedTenant(): int
    {
        $tenantId = TenantContext::id();
        if ($tenantId === null) {
            throw new \RuntimeException('Operación sin contexto de tenant.');
        }

        return $tenantId;
    }

    /** @return array<string, mixed>|null */
    public function find(int $id, ?int $tenantId = null): ?array
    {
        $tenantId ??= $this->scopedTenant();
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE id = :id AND tenant_id = :tenant_id LIMIT 1"
        );
        $stmt->execute(['id' => $id, 'tenant_id' => $tenantId]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    /** @return list<array<string, mixed>> */
    public function allForTenant(?int $tenantId = null, string $orderBy = 'id DESC'): array
    {
        $tenantId ??= $this->scopedTenant();
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE tenant_id = :tenant_id ORDER BY {$orderBy}"
        );
        $stmt->execute(['tenant_id' => $tenantId]);

        return $stmt->fetchAll();
    }
}
