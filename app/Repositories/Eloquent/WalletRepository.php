<?php

namespace App\Repositories\Eloquent;

use App\Models\Wallet;
use App\Repositories\Interfaces\WalletRepositoryInterface;
use Illuminate\Support\Facades\DB;

class WalletRepository extends BaseRepository implements WalletRepositoryInterface
{
    public function __construct(Wallet $model)
    {
        parent::__construct($model);
    }

    public function findByMerchantId(string $merchantId): ?Wallet
    {
        return $this->model->where('merchant_id', $merchantId)->first();
    }

    public function incrementBalance(string $walletId, float $amount): bool
    {
        return DB::transaction(function () use ($walletId, $amount) {
            $wallet = $this->model->lockForUpdate()->find($walletId);
            if (!$wallet) return false;
            
            $wallet->balance += $amount;
            return $wallet->save();
        });
    }

    public function decrementBalance(string $walletId, float $amount): bool
    {
        return DB::transaction(function () use ($walletId, $amount) {
            $wallet = $this->model->lockForUpdate()->find($walletId);
            if (!$wallet) return false;
            if ($wallet->balance < $amount) return false; // Insufficient balance checks

            $wallet->balance -= $amount;
            return $wallet->save();
        });
    }

    public function freezeBalance(string $walletId, float $amount): bool
    {
        return DB::transaction(function () use ($walletId, $amount) {
            $wallet = $this->model->lockForUpdate()->find($walletId);
            if (!$wallet) return false;
            if ($wallet->balance < $amount) return false;

            $wallet->balance -= $amount;
            $wallet->frozen_balance += $amount;
            return $wallet->save();
        });
    }

    public function unfreezeBalance(string $walletId, float $amount): bool
    {
        return DB::transaction(function () use ($walletId, $amount) {
            $wallet = $this->model->lockForUpdate()->find($walletId);
            if (!$wallet) return false;
            if ($wallet->frozen_balance < $amount) return false;

            $wallet->frozen_balance -= $amount;
            $wallet->balance += $amount;
            return $wallet->save();
        });
    }
}
