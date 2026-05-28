<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

final class AuditService
{
    public function log(
        ?int $tenantId,
        ?int $userId,
        string $action,
        ?string $entityType = null,
        ?int $entityId = null,
        ?array $payload = null,
    ): void {
        $db = Database::connection();
        $stmt = $db->prepare(
            'INSERT INTO audit_logs (tenant_id, user_id, action, entity_type, entity_id, ip_address, user_agent, payload)
             VALUES (:tenant_id, :user_id, :action, :entity_type, :entity_id, :ip, :ua, :payload)'
        );
        $stmt->execute([
            'tenant_id' => $tenantId,
            'user_id' => $userId,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
            'ua' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
            'payload' => $payload ? json_encode($payload) : null,
        ]);
    }
}
