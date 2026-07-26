<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class MerchantBeneficiary extends Model
{
    use HasUuids;

    protected $table = 'merchant_beneficiaries';

    protected $fillable = [
        'merchant_id',
        'name',
        'bank_name',
        'account_number',
        'ifsc',
        'logo_url',
    ];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }
}
