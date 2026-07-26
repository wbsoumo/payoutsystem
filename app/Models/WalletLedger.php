<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class WalletLedger extends Model
{
    use HasUuids;

    protected $table = 'wallet_ledgers';

    protected $fillable = [
        'wallet_id',
        'type',
        'amount',
        'opening_balance',
        'closing_balance',
        'description',
        'reference_type',
        'reference_id',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:4',
        'opening_balance' => 'decimal:4',
        'closing_balance' => 'decimal:4',
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class, 'wallet_id');
    }
}
