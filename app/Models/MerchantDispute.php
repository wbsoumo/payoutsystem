<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class MerchantDispute extends Model
{
    use HasUuids;

    protected $fillable = [
        'merchant_id',
        'transaction_id',
        'amount',
        'reason',
        'status',
    ];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
