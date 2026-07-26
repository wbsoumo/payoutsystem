<?php

namespace App\Repositories\Interfaces;

use App\Models\Wallet;

interface WalletRepositoryInterface extends BaseRepositoryInterface
{
    public function findByMerchantId(string $merchantId): ?Wallet;

    public function incrementBalance(string $walletId, float $amount): bool;

    public function decrementBalance(string $walletId, float $amount): bool;

    public function freezeBalance(string $walletId, float $amount): bool;

    public function unfreezeBalance(string $walletId, float $amount): bool;
}
