<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class MerchantWebhook extends Model
{
    use HasUuids;

    protected $fillable = [
        'merchant_id',
        'url',
        'secret_key',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }
}
