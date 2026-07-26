<?php

namespace App\Repositories\Interfaces;

use App\Models\AuditLog;

interface AuditLogRepositoryInterface extends BaseRepositoryInterface
{
    public function logAction(
        string $userType,
        ?string $userId,
        ?string $merchantId,
        string $action,
        string $description,
        ?array $payload = null
    ): AuditLog;
}
