<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasAuditColumns;

class MerchantIpWhitelist extends Model
{
    use HasUuids, HasAuditColumns;

    protected $table = 'merchant_ip_whitelists';

    protected $fillable = [
        'merchant_id',
        'ip_address',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }
}
