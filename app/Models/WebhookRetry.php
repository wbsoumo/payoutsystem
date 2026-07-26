<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class WebhookRetry extends Model
{
    use HasUuids;

    protected $table = 'webhook_retries';

    protected $fillable = [
        'merchant_id',
        'transaction_id',
        'url',
        'payload',
        'status',
        'attempts',
        'last_attempt_at',
        'next_attempt_at',
        'response_status',
        'response_body',
    ];

    protected $casts = [
        'payload' => 'json',
        'last_attempt_at' => 'datetime',
        'next_attempt_at' => 'datetime',
    ];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }
}
