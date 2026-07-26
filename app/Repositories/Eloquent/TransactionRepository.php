<?php

namespace App\Repositories\Eloquent;

use App\Models\Transaction;
use App\Repositories\Interfaces\TransactionRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class TransactionRepository extends BaseRepository implements TransactionRepositoryInterface
{
    public function __construct(Transaction $model)
    {
        parent::__construct($model);
    }

    public function findByReferenceId(string $referenceId): ?Transaction
    {
        return $this->model->where('reference_id', $referenceId)->first();
    }

    public function findByClientReferenceId(string $merchantId, string $clientReferenceId): ?Transaction
    {
        return $this->model->where('merchant_id', $merchantId)
                           ->where('client_reference_id', $clientReferenceId)
                           ->first();
    }

    public function getMerchantTransactions(string $merchantId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->where('merchant_id', $merchantId)
                           ->orderBy('created_at', 'desc')
                           ->paginate($perPage);
    }
}
