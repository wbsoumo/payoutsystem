<?php

namespace App\Services;

use App\Repositories\Interfaces\WalletRepositoryInterface;
use App\Models\Wallet;
use App\Models\WalletLedger;
use Illuminate\Support\Facades\DB;

class WalletService
{
    protected WalletRepositoryInterface $walletRepository;
    protected AuditLogService $auditLogService;

    public function __construct(WalletRepositoryInterface $walletRepository, AuditLogService $auditLogService)
    {
        $this->walletRepository = $walletRepository;
        $this->auditLogService = $auditLogService;
    }

    public function creditWallet(
        string $merchantId,
        float $amount,
        string $description,
        ?string $refType = null,
        ?string $refId = null,
        ?string $creatorId = null
    ): ?WalletLedger {
        return DB::transaction(function () use ($merchantId, $amount, $description, $refType, $refId, $creatorId) {
            $wallet = $this->walletRepository->findByMerchantId($merchantId);
            if (!$wallet) return null;

            $openingBalance = (float) $wallet->balance;
            $success = $this->walletRepository->incrementBalance($wallet->id, $amount);

            if ($success) {
                $wallet->refresh();
                $closingBalance = (float) $wallet->balance;

                $ledger = WalletLedger::create([
                    'wallet_id' => $wallet->id,
                    'type' => 'credit',
                    'amount' => $amount,
                    'opening_balance' => $openingBalance,
                    'closing_balance' => $closingBalance,
                    'description' => $description,
                    'reference_type' => $refType,
                    'reference_id' => $refId,
                    'created_by' => $creatorId,
                ]);

                $this->auditLogService->log(
                    $creatorId ? 'admin' : 'system',
                    $creatorId,
                    $merchantId,
                    'wallet_credit',
                    "Wallet credited with amount: {$amount}. Description: {$description}",
                    ['ledger_id' => $ledger->id, 'amount' => $amount]
                );

                return $ledger;
            }

            return null;
        });
    }

    public function debitWallet(
        string $merchantId,
        float $amount,
        string $description,
        ?string $refType = null,
        ?string $refId = null,
        ?string $creatorId = null
    ): ?WalletLedger {
        return DB::transaction(function () use ($merchantId, $amount, $description, $refType, $refId, $creatorId) {
            $wallet = $this->walletRepository->findByMerchantId($merchantId);
            if (!$wallet) return null;

            if ($wallet->balance < $amount) {
                return null; // Insufficient balance
            }

            $openingBalance = (float) $wallet->balance;
            $success = $this->walletRepository->decrementBalance($wallet->id, $amount);

            if ($success) {
                $wallet->refresh();
                $closingBalance = (float) $wallet->balance;

                $ledger = WalletLedger::create([
                    'wallet_id' => $wallet->id,
                    'type' => 'debit',
                    'amount' => $amount,
                    'opening_balance' => $openingBalance,
                    'closing_balance' => $closingBalance,
                    'description' => $description,
                    'reference_type' => $refType,
                    'reference_id' => $refId,
                    'created_by' => $creatorId,
                ]);

                $this->auditLogService->log(
                    $creatorId ? 'admin' : 'system',
                    $creatorId,
                    $merchantId,
                    'wallet_debit',
                    "Wallet debited with amount: {$amount}. Description: {$description}",
                    ['ledger_id' => $ledger->id, 'amount' => $amount]
                );

                return $ledger;
            }

            return null;
        });
    }

    public function freezeAmount(
        string $merchantId,
        float $amount,
        string $description,
        ?string $refType = null,
        ?string $refId = null,
        ?string $creatorId = null
    ): bool {
        return DB::transaction(function () use ($merchantId, $amount, $description, $refType, $refId, $creatorId) {
            $wallet = $this->walletRepository->findByMerchantId($merchantId);
            if (!$wallet || $wallet->balance < $amount) return false;

            $success = $this->walletRepository->freezeBalance($wallet->id, $amount);
            if ($success) {
                $this->auditLogService->log(
                    $creatorId ? 'admin' : 'system',
                    $creatorId,
                    $merchantId,
                    'wallet_freeze',
                    "Wallet balance frozen with amount: {$amount}. Description: {$description}",
                    ['amount' => $amount]
                );
                return true;
            }
            return false;
        });
    }

    public function unfreezeAmount(
        string $merchantId,
        float $amount,
        string $description,
        ?string $refType = null,
        ?string $refId = null,
        ?string $creatorId = null
    ): bool {
        return DB::transaction(function () use ($merchantId, $amount, $description, $refType, $refId, $creatorId) {
            $wallet = $this->walletRepository->findByMerchantId($merchantId);
            if (!$wallet || $wallet->frozen_balance < $amount) return false;

            $success = $this->walletRepository->unfreezeBalance($wallet->id, $amount);
            if ($success) {
                $this->auditLogService->log(
                    $creatorId ? 'admin' : 'system',
                    $creatorId,
                    $merchantId,
                    'wallet_unfreeze',
                    "Wallet balance unfrozen with amount: {$amount}. Description: {$description}",
                    ['amount' => $amount]
                );
                return true;
            }
            return false;
        });
    }
}
