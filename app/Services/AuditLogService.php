<?php

namespace App\Services;

use App\Repositories\Interfaces\AuditLogRepositoryInterface;
use App\Models\AuditLog;

class AuditLogService
{
    protected AuditLogRepositoryInterface $auditLogRepository;

    public function __construct(AuditLogRepositoryInterface $auditLogRepository)
    {
        $this->auditLogRepository = $auditLogRepository;
    }

    public function log(
        string $userType,
        ?string $userId,
        ?string $merchantId,
        string $action,
        string $description,
        ?array $payload = null
    ): AuditLog {
        return $this->auditLogRepository->logAction($userType, $userId, $merchantId, $action, $description, $payload);
    }
}
