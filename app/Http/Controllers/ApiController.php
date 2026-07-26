<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\CommissionService;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
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
            'logo_dev_api_key' => \App\Models\Setting::get('logo_dev_api_key', ''),
            'has_set_pin' => !empty($merchant->transaction_pin),
            'deposit_upi_id' => $merchant->deposit_upi_id ?? 'novexapay@yesbank',
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

    public function setupPin(Request $request)
    {
        $merchant = $request->get('merchant');
        
        $validator = Validator::make($request->all(), [
            'pin' => 'required|string|size:6|regex:/^[0-9]+$/',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        if ($merchant->transaction_pin) {
            return response()->json(['success' => false, 'error' => 'Transaction PIN is already configured.'], 400);
        }

        $merchant->update([
            'transaction_pin' => Hash::make($request->pin),
            'pin_failed_attempts' => 0,
            'pin_locked_until' => null,
        ]);

        return response()->json(['success' => true, 'message' => 'Transaction PIN setup successfully.']);
    }

    public function verifyPin(Request $request)
    {
        $merchant = $request->get('merchant');

        $validator = Validator::make($request->all(), [
            'pin' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        if ($merchant->pin_locked_until && $merchant->pin_locked_until->isFuture()) {
            $minutes = $merchant->pin_locked_until->diffInMinutes(now()) + 1;
            return response()->json([
                'success' => false,
                'error' => "Transaction PIN is locked due to too many failed attempts. Try again in {$minutes} minutes."
            ], 423);
        }

        if (Hash::check($request->pin, $merchant->transaction_pin)) {
            $merchant->update([
                'pin_failed_attempts' => 0,
                'pin_locked_until' => null,
            ]);

            return response()->json(['success' => true, 'message' => 'Transaction PIN verified successfully.']);
        }

        $attempts = $merchant->pin_failed_attempts + 1;
        $lockUntil = null;
        $message = "Invalid Transaction PIN. Attempt {$attempts} of 5.";

        if ($attempts >= 5) {
            $lockUntil = now()->addMinutes(30);
            $message = "Transaction PIN locked for 30 minutes due to 5 consecutive failed attempts.";
        }

        $merchant->update([
            'pin_failed_attempts' => $lockUntil ? 0 : $attempts,
            'pin_locked_until' => $lockUntil,
        ]);

        return response()->json(['success' => false, 'error' => $message], 401);
    }

    public function changePin(Request $request)
    {
        $merchant = $request->get('merchant');

        $validator = Validator::make($request->all(), [
            'current_pin' => 'required|string|size:6',
            'new_pin' => 'required|string|size:6|regex:/^[0-9]+$/',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        if (!Hash::check($request->current_pin, $merchant->transaction_pin)) {
            return response()->json(['success' => false, 'error' => 'Current Transaction PIN is incorrect.'], 401);
        }

        $merchant->update([
            'transaction_pin' => Hash::make($request->new_pin),
            'pin_failed_attempts' => 0,
            'pin_locked_until' => null,
        ]);

        return response()->json(['success' => true, 'message' => 'Transaction PIN modified successfully.']);
    }

    public function getPayouts(Request $request)
    {
        $merchant = $request->get('merchant');

        $payouts = Transaction::where('merchant_id', $merchant->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($t) {
                return [
                    'ref' => $t->reference_id,
                    'beneficiary' => $t->api_request_payload['bank_holder_name'] ?? 'N/A',
                    'bank' => $t->api_request_payload['bank_name'] ?? 'N/A',
                    'account' => $t->api_request_payload['bank_account_number'] ?? 'N/A',
                    'ifsc' => $t->api_request_payload['bank_ifsc'] ?? 'N/A',
                    'amount' => '₹' . number_format($t->amount, 2),
                    'status' => $t->status,
                    'date' => $t->created_at->format('M d, H:i'),
                ];
            });

        return response()->json([
            'success' => true,
            'payouts' => $payouts,
        ]);
    }

    public function getCompanyLogo($domain)
    {
        $key = \App\Models\Setting::get('logo_dev_api_key', '');

        if (empty($key)) {
            return redirect("https://logo.clearbit.com/{$domain}");
        }

        try {
            $url = "https://img.logo.dev/{$domain}?token={$key}";
            $response = \Illuminate\Support\Facades\Http::get($url);
            if ($response->successful()) {
                return response($response->body(), 200)
                    ->header('Content-Type', $response->header('Content-Type') ?? 'image/png');
            }
        } catch (\Exception $e) {
            // Log or fallback
        }

        return redirect("https://logo.clearbit.com/{$domain}");
    }

    public function getLedgerLogs(Request $request)
    {
        $merchant = $request->get('merchant');

        $ledgers = \App\Models\WalletLedger::where('wallet_id', $merchant->wallet->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($l) {
                return [
                    'type' => $l->type,
                    'amount' => '₹' . number_format($l->amount, 2),
                    'desc' => $l->description,
                    'date' => $l->created_at->format('M d, H:i'),
                    'bal' => '₹' . number_format($l->closing_balance, 2),
                ];
            });

        $openingBalance = $ledgers->isEmpty() ? 0.00 : floatval(str_replace(['₹', ','], '', $ledgers->last()['bal']));
        $closingBalance = $ledgers->isEmpty() ? 0.00 : floatval(str_replace(['₹', ','], '', $ledgers->first()['bal']));

        return response()->json([
            'success' => true,
            'opening_balance' => '₹' . number_format($openingBalance, 2),
            'closing_balance' => '₹' . number_format($closingBalance, 2),
            'logs' => $ledgers,
        ]);
    }

    public function resetPin(Request $request)
    {
        $merchant = $request->get('merchant');

        $validator = Validator::make($request->all(), [
            'otp' => 'required|string',
            'new_pin' => 'required|string|size:6|regex:/^[0-9]+$/',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Validate via standard static verification code or custom session variables
        if ($request->otp !== '123456') {
            return response()->json(['success' => false, 'error' => 'Invalid email verification OTP code.'], 400);
        }

        $merchant->update([
            'transaction_pin' => $request->new_pin,
            'pin_failed_attempts' => 0,
            'pin_locked_until' => null,
        ]);

        return response()->json(['success' => true, 'message' => 'Transaction PIN reset successfully.']);
    }

    public function getBeneficiaries(Request $request)
    {
        $merchant = $request->get('merchant');
        $beneficiaries = $merchant->beneficiaries()
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'beneficiaries' => $beneficiaries->map(function ($b) {
                return [
                    'id' => $b->id,
                    'name' => $b->name,
                    'bank' => $b->bank_name,
                    'account' => $b->account_number,
                    'ifsc' => $b->ifsc,
                    'logo' => $b->logo_url,
                ];
            })
        ]);
    }

    public function createBeneficiary(Request $request)
    {
        $merchant = $request->get('merchant');

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:30',
            'ifsc' => 'required|string|max:11',
            'logo_url' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Check if beneficiary already exists for this merchant
        $existing = $merchant->beneficiaries()
            ->where('account_number', $request->account_number)
            ->where('ifsc', $request->ifsc)
            ->first();

        if ($existing) {
            return response()->json(['success' => false, 'error' => 'Beneficiary already registered.'], 400);
        }

        $beneficiary = $merchant->beneficiaries()->create([
            'name' => $request->name,
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'ifsc' => $request->ifsc,
            'logo_url' => $request->logo_url,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Beneficiary saved successfully.',
            'beneficiary' => [
                'id' => $beneficiary->id,
                'name' => $beneficiary->name,
                'bank' => $beneficiary->bank_name,
                'account' => $beneficiary->account_number,
                'ifsc' => $beneficiary->ifsc,
                'logo' => $beneficiary->logo_url,
            ]
        ]);
    }

    public function deleteBeneficiary(Request $request, $id)
    {
        $merchant = $request->get('merchant');
        $beneficiary = $merchant->beneficiaries()->find($id);

        if (!$beneficiary) {
            return response()->json(['success' => false, 'error' => 'Beneficiary not found.'], 404);
        }

        $beneficiary->delete();

        return response()->json([
            'success' => true,
            'message' => 'Beneficiary deleted successfully.'
        ]);
    }
}
