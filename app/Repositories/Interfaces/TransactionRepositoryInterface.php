<?php

namespace App\Repositories\Interfaces;

use App\Models\Transaction;
use Illuminate\Pagination\LengthAwarePaginator;

interface TransactionRepositoryInterface extends BaseRepositoryInterface
{
    public function findByReferenceId(string $referenceId): ?Transaction;

    public function findByClientReferenceId(string $merchantId, string $clientReferenceId): ?Transaction;

    public function getMerchantTransactions(string $merchantId, int $perPage = 15): LengthAwarePaginator;
}
