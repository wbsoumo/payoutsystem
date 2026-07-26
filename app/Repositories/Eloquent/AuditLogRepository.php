<?php

namespace App\Repositories\Eloquent;

use App\Models\AuditLog;
use App\Repositories\Interfaces\AuditLogRepositoryInterface;
use Illuminate\Support\Facades\Request;

class AuditLogRepository extends BaseRepository implements AuditLogRepositoryInterface
{
    public function __construct(AuditLog $model)
    {
        parent::__construct($model);
    }

    public function logAction(
        string $userType,
        ?string $userId,
        ?string $merchantId,
        string $action,
        string $description,
        ?array $payload = null
    ): AuditLog {
        return $this->create([
            'user_type' => $userType,
            'user_id' => $userId,
            'merchant_id' => $merchantId,
            'action' => $action,
            'description' => $description,
            'ip_address' => Request::ip() ?? '127.0.0.1',
            'user_agent' => Request::userAgent(),
            'payload' => $payload,
        ]);
    }
}
