<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\CommissionService;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ApiController extends Controller
{
    protected WalletService $walletService;
    protected CommissionService $commissionService;
    protected \App\Services\JioPayService $jioPayService;

    public function __construct(
        WalletService $walletService, 
        CommissionService $commissionService,
        \App\Services\JioPayService $jioPayService
    ) {
        $this->walletService = $walletService;
        $this->commissionService = $commissionService;
        $this->jioPayService = $jioPayService;
    }

    public function getBalance(Request $request)
    {
        $merchant = $request->get('merchant'); // Attached by ApiSignatureMiddleware
        $wallet = $merchant->wallet;

        return response()->json([
            'success' => true,
            'balance' => (float) $wallet->balance,
            'frozen_balance' => (float) $wallet->frozen_balance,
            'currency' => $wallet->currency,
        ]);
    }

    public function createPayout(Request $request)
    {
        $merchant = $request->get('merchant');

        $validator = Validator::make($request->all(), [
            'client_reference_id' => 'required|string|max:100',
            'amount' => 'required|numeric|min:1.00',
            'bank_name' => 'required|string|max:255',
            'bank_account_number' => 'required|string|max:30',
            'bank_ifsc' => 'required|string|max:11',
            'bank_holder_name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();
        $amount = (float) $validated['amount'];

        // 1. Idempotency Check
        $existing = Transaction::where('merchant_id', $merchant->id)
            ->where('client_reference_id', $validated['client_reference_id'])
            ->first();

        if ($existing) {
            return response()->json([
                'success' => true,
                'is_duplicate' => true,
                'transaction_id' => $existing->id,
                'reference_id' => $existing->reference_id,
                'client_reference_id' => $existing->client_reference_id,
                'amount' => (float) $existing->amount,
                'total_charges' => (float) $existing->total_charges,
                'status' => $existing->status,
                'created_at' => $existing->created_at->toISOString(),
            ]);
        }

        // 2. Calculate Commission & GST
        $fees = $this->commissionService->calculateCommission($merchant->id, $amount);
        $totalCharges = (float) $fees['total'];
        $totalDebitAmount = $amount + $totalCharges;

        // Check balance
        $wallet = $merchant->wallet;
        if ((float) $wallet->balance < $totalDebitAmount) {
            return response()->json([
                'success' => false,
                'error' => 'Insufficient wallet balance for this payout plus fee.',
                'required_balance' => $totalDebitAmount,
                'current_balance' => (float) $wallet->balance,
            ], 402);
        }

        // 3. Process Payout with Pessimistic DB locking
        $startTime = microtime(true);
        $referenceId = 'tx_' . Str::lower(Str::random(16));

        $transaction = DB::transaction(function () use ($merchant, $amount, $fees, $totalCharges, $totalDebitAmount, $validated, $referenceId) {
            // Debit wallet
            $ledger = $this->walletService->debitWallet(
                $merchant->id,
                $totalDebitAmount,
                "Payout Reference: {$referenceId}",
                'transaction',
                null // Will link ID after saving
            );

            if (!$ledger) {
                throw new \Exception("Ledger debit failed.");
            }

            // Create Transaction record
            $txn = Transaction::create([
                'merchant_id' => $merchant->id,
                'reference_id' => $referenceId,
                'client_reference_id' => $validated['client_reference_id'],
                'type' => 'payout',
                'amount' => $amount,
                'fee' => (float) $fees['commission'],
                'commission' => 0.00, // standard fees mapped to commission field in ledger
                'gst' => (float) $fees['gst'],
                'total_charges' => $totalCharges,
                'opening_balance' => $ledger->opening_balance,
                'closing_balance' => $ledger->closing_balance,
                'status' => 'pending', // Starts pending
                'provider_name' => 'mock_gateway',
                'ip_address' => request()->ip() ?? '127.0.0.1',
                'api_request_payload' => $validated,
            ]);

            // Update ledger reference_id to the created transaction ID
            $ledger->update(['reference_id' => $txn->id]);

            return $txn;
        });

        // 4. Dispatch Upstream Provider (Jiopay Production API)
        $jioResult = $this->jioPayService->transfer([
            'order_id' => $referenceId,
            'beneficiary_name' => $validated['bank_holder_name'],
            'account_number' => $validated['bank_account_number'],
            'ifsc' => $validated['bank_ifsc'],
            'amount' => $amount,
        ]);

        $status = $jioResult['status'] === 'success' ? 'success' : 'failed';
        $providerRef = $jioResult['provider_reference_id'] ?? null;
        $failureReason = $jioResult['status'] === 'failed' ? $jioResult['message'] : null;
        $responseTimeMs = (int) ((microtime(true) - $startTime) * 1000);

        // 5. Update Transaction status and credit back wallet if failed
        DB::transaction(function () use ($transaction, $status, $providerRef, $responseTimeMs, $failureReason, $totalDebitAmount, $merchant, $referenceId, $jioResult) {
            $transaction->update([
                'status' => $status,
                'provider_reference_id' => $providerRef,
                'response_time_ms' => $responseTimeMs,
                'api_response_payload' => $jioResult['response'] ?? [],
            ]);

            if ($status === 'failed') {
                // Return funds back to merchant
                $this->walletService->creditWallet(
                    $merchant->id,
                    $totalDebitAmount,
                    "Reversal payout failed: {$referenceId}",
                    'refund',
                    $transaction->id
                );
            }
        });

        $transaction->refresh();

        return response()->json([
            'success' => ($status === 'success'),
            'transaction_id' => $transaction->id,
            'reference_id' => $transaction->reference_id,
            'client_reference_id' => $transaction->client_reference_id,
            'amount' => (float) $transaction->amount,
            'total_charges' => (float) $transaction->total_charges,
            'status' => $transaction->status,
            'provider_reference' => $transaction->provider_reference_id,
            'error_message' => $failureReason,
            'created_at' => $transaction->created_at->toISOString(),
        ], ($status === 'success') ? 201 : 400);
    }

    public function getPayout(Request $request, string $referenceId)
    {
        $merchant = $request->get('merchant');
        $transaction = Transaction::where('merchant_id', $merchant->id)
            ->where('reference_id', $referenceId)
            ->first();

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'error' => 'Payout transaction not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'transaction_id' => $transaction->id,
            'reference_id' => $transaction->reference_id,
            'client_reference_id' => $transaction->client_reference_id,
            'amount' => (float) $transaction->amount,
            'total_charges' => (float) $transaction->total_charges,
            'status' => $transaction->status,
            'provider_reference' => $transaction->provider_reference_id,
            'created_at' => $transaction->created_at->toISOString(),
        ]);
    }
}
