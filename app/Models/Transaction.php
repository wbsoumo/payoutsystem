<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Transaction extends Model
{
    use HasUuids;

    protected $table = 'transactions';

    protected $fillable = [
        'merchant_id',
        'reference_id',
        'client_reference_id',
        'type',
        'amount',
        'fee',
        'commission',
        'gst',
        'total_charges',
        'opening_balance',
        'closing_balance',
        'status',
        'provider_name',
        'provider_reference_id',
        'response_time_ms',
        'ip_address',
        'latitude',
        'longitude',
        'browser',
        'risk_score',
        'api_request_payload',
        'api_response_payload',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'amount' => 'decimal:4',
        'fee' => 'decimal:4',
        'commission' => 'decimal:4',
        'gst' => 'decimal:4',
        'total_charges' => 'decimal:4',
        'opening_balance' => 'decimal:4',
        'closing_balance' => 'decimal:4',
        'api_request_payload' => 'json',
        'api_response_payload' => 'json',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }
}
