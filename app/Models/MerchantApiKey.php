<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasAuditColumns;

class MerchantApiKey extends Model
{
    use HasUuids, SoftDeletes, HasAuditColumns;

    protected $table = 'merchant_api_keys';

    protected $fillable = [
        'merchant_id',
        'name',
        'api_key_hash',
        'api_key_preview',
        'secret_key_encrypted',
        'webhook_secret_encrypted',
        'is_active',
    ];

    protected $casts = [
        'secret_key_encrypted' => 'encrypted',
        'webhook_secret_encrypted' => 'encrypted',
        'is_active' => 'boolean',
    ];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }
}
