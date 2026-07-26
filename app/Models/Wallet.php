<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasAuditColumns;

class Wallet extends Model
{
    use HasUuids, HasAuditColumns;

    protected $table = 'wallets';

    protected $fillable = [
        'merchant_id',
        'balance',
        'frozen_balance',
        'currency',
    ];

    protected $casts = [
        'balance' => 'decimal:4',
        'frozen_balance' => 'decimal:4',
    ];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }

    public function ledgers()
    {
        return $this->hasMany(WalletLedger::class, 'wallet_id');
    }
}
