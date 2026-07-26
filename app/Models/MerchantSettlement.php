<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class MerchantSettlement extends Model
{
    use HasUuids;

    protected $fillable = [
        'merchant_id',
        'reference_id',
        'amount',
        'fee',
        'bank_name',
        'account_number',
        'ifsc',
        'status',
    ];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }
}
