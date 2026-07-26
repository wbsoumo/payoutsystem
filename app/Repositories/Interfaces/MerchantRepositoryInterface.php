<?php

namespace App\Repositories\Interfaces;

use App\Models\Merchant;

interface MerchantRepositoryInterface extends BaseRepositoryInterface
{
    public function findByEmail(string $email): ?Merchant;
    
    public function updateKycStatus(string $merchantId, string $status): bool;
}
