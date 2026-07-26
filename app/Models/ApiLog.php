<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ApiLog extends Model
{
    use HasUuids;

    protected $table = 'api_logs';

    const UPDATED_AT = null;

    protected $fillable = [
        'merchant_id',
        'endpoint',
        'method',
        'status_code',
        'headers',
        'body',
        'response',
        'execution_time_ms',
        'source_ip',
        'user_agent',
        'signature_result',
        'timestamp_validation',
        'nonce_validation',
    ];

    protected $casts = [
        'headers' => 'json',
        'body' => 'json',
        'response' => 'json',
        'signature_result' => 'boolean',
        'timestamp_validation' => 'boolean',
        'nonce_validation' => 'boolean',
    ];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }
}
