<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasAuditColumns;

class Merchant extends Model
{
    use HasUuids, SoftDeletes, HasAuditColumns;

    protected $table = 'merchants';

    protected $fillable = [
        'company_name',
        'business_name',
        'business_type',
        'website',
        'phone',
        'email',
        'country',
        'monthly_volume',
        'status',
        'kyc_status',
    ];

    public function users()
    {
        return $this->hasMany(MerchantUser::class, 'merchant_id');
    }

    public function profile()
    {
        return $this->hasOne(MerchantProfile::class, 'merchant_id');
    }

    public function apiKeys()
    {
        return $this->hasMany(MerchantApiKey::class, 'merchant_id');
    }

    public function ipWhitelists()
    {
        return $this->hasMany(MerchantIpWhitelist::class, 'merchant_id');
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class, 'merchant_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'merchant_id');
    }

    public function commissions()
    {
        return $this->hasMany(Commission::class, 'merchant_id');
    }
}
