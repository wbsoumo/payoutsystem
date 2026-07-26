<?php

namespace App\Repositories\Eloquent;

use App\Models\Merchant;
use App\Repositories\Interfaces\MerchantRepositoryInterface;

class MerchantRepository extends BaseRepository implements MerchantRepositoryInterface
{
    public function __construct(Merchant $model)
    {
        parent::__construct($model);
    }

    public function findByEmail(string $email): ?Merchant
    {
        return $this->model->where('email', $email)->first();
    }

    public function updateKycStatus(string $merchantId, string $status): bool
    {
        return $this->update($merchantId, ['kyc_status' => $status]);
    }
}
