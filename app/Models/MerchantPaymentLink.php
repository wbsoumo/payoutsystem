<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class MerchantPaymentLink extends Model
{
    use HasUuids;

    protected $fillable = [
        'merchant_id',
        'reference_id',
        'amount',
        'customer_name',
        'customer_email',
        'description',
        'status',
    ];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }
}
