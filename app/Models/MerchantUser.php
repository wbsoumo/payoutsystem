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
        'transaction_pin',
        'pin_failed_attempts',
        'pin_locked_until',
        'status',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
        'otp_code',
        'otp_expires_at',
    ];

    protected $hidden = [
        'password',
        'transaction_pin',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected $casts = [
        'password' => 'hashed',
        'transaction_pin' => 'hashed',
        'pin_locked_until' => 'datetime',
        'two_factor_confirmed_at' => 'datetime',
        'otp_expires_at' => 'datetime',
    ];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }
}
