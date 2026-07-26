<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasAuditColumns;

class MerchantUser extends Authenticatable
{
    use HasUuids, SoftDeletes, HasAuditColumns;

    protected $table = 'merchant_users';

    protected $fillable = [
        'merchant_id',
        'name',
        'email',
        'password',
        'status',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
        'otp_code',
        'otp_expires_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected $casts = [
        'password' => 'hashed',
        'two_factor_confirmed_at' => 'datetime',
        'otp_expires_at' => 'datetime',
    ];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }
}
