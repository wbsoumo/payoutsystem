<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use App\Models\MerchantProfile;
use App\Models\MerchantApiKey;
use App\Models\MerchantIpWhitelist;
use App\Models\Transaction;
use App\Models\WalletLedger;
use App\Models\SupportTicket;
use App\Services\ApiService;
use App\Services\AuditLogService;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MerchantDashboardController extends Controller
{
    protected ApiService $apiService;
    protected AuditLogService $auditLogService;
    protected WalletService $walletService;
    protected \App\Services\CommissionService $commissionService;
    protected \App\Services\JioPayService $jioPayService;

    public function __construct(
        ApiService $apiService, 
        AuditLogService $auditLogService, 
        WalletService $walletService,
        \App\Services\CommissionService $commissionService,
        \App\Services\JioPayService $jioPayService
    ) {
        $this->apiService = $apiService;
        $this->auditLogService = $auditLogService;
        $this->walletService = $walletService;
        $this->commissionService = $commissionService;
        $this->jioPayService = $jioPayService;
    }

    protected function getMerchant()
    {
        return Auth::guard('merchant')->user()->merchant;
    }

    public function index()
    {
        $merchant = $this->getMerchant();
        $wallet = $merchant->wallet;

        // Stats calculations
        $todayTxns = Transaction::where('merchant_id', $merchant->id)
            ->whereDate('created_at', now()->toDateString())
            ->get();

        $todayVolume = $todayTxns->where('status', 'success')->sum('amount');
        $successCount = $todayTxns->where('status', 'success')->count();
        $failedCount = $todayTxns->where('status', 'failed')->count();
        $pendingCount = $todayTxns->where('status', 'pending')->count();
        $totalCount = $todayTxns->count();

        $successRate = $totalCount > 0 ? round(($successCount / $totalCount) * 100, 2) : 100.0;

        $latestTransactions = Transaction::where('merchant_id', $merchant->id)
            ->orderBy('created_at', 'desc')
            ->take(7)
            ->get();

        // Simple monthly data for graph (last 7 days of volumes)
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $volume = Transaction::where('merchant_id', $merchant->id)
                ->where('status', 'success')
                ->whereDate('created_at', $date)
                ->sum('amount');
            $chartData[] = [
                'day' => now()->subDays($i)->format('D'),
                'volume' => (float)$volume
            ];
        }

        return view('merchant.dashboard', compact(
            'merchant', 'wallet', 'todayVolume', 'successRate', 
            'failedCount', 'pendingCount', 'latestTransactions', 'chartData'
        ));
    }

    public function ledger()
    {
        $merchant = $this->getMerchant();
        $wallet = $merchant->wallet;
        $ledgers = WalletLedger::where('wallet_id', $wallet->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('merchant.ledger', compact('merchant', 'wallet', 'ledgers'));
    }

    public function profile()
    {
        $merchant = $this->getMerchant();
        $profile = $merchant->profile;
        $user = Auth::guard('merchant')->user();

        return view('merchant.profile', compact('merchant', 'profile', 'user'));
    }

    public function profileUpdate(Request $request)
    {
        $merchant = $this->getMerchant();
        $profile = $merchant->profile;

        $validator = Validator::make($request->all(), [
            'gst' => 'nullable|string|max:15',
            'pan' => 'nullable|string|max:10',
            'bank_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:30',
            'bank_ifsc' => 'nullable|string|max:11',
            'bank_holder_name' => 'nullable|string|max:255',
            'address_line1' => 'nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'kyc_document' => 'nullable|file|mimes:pdf,jpg,png|max:5120',
            'profile_image' => 'nullable|file|image|max:2048',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->except(['kyc_document', 'profile_image']);

        // Handle file uploads
        if ($request->hasFile('kyc_document')) {
            $path = $request->file('kyc_document')->store('kyc_documents', 'public');
            $data['kyc_document_path'] = $path;
            
            // Mark KYC submitted if documents are uploaded
            $merchant->update(['kyc_status' => 'submitted']);
        }

        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $data['profile_image_path'] = $path;
        }

        $profile->update($data);

        $this->auditLogService->log(
            'merchant_user',
            Auth::guard('merchant')->user()->id,
            $merchant->id,
            'profile_update',
            "Merchant profile details updated."
        );

        return back()->with('success', 'Profile settings updated successfully.');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        $user = Auth::guard('merchant')->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password does not match.']);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        $this->auditLogService->log(
            'merchant_user',
            $user->id,
            $user->merchant_id,
            'password_change',
            "User changed login password."
        );

        return back()->with('success', 'Password updated successfully.');
    }

    public function apiKeys()
    {
        $merchant = $this->getMerchant();
        $keys = MerchantApiKey::where('merchant_id', $merchant->id)->get();
        $whitelists = MerchantIpWhitelist::where('merchant_id', $merchant->id)->get();

        return view('merchant.api-keys', compact('merchant', 'keys', 'whitelists'));
    }

    public function generateApiKeys(Request $request)
    {
        $request->validate([
            'password' => 'required',
        ]);

        $user = Auth::guard('merchant')->user();
        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Invalid password confirmation.']);
        }

        $keys = $this->apiService->generateKeys($user->merchant_id, $request->input('key_name', 'Production Key'));

        $this->auditLogService->log(
            'merchant_user',
            $user->id,
            $user->merchant_id,
            'api_key_generation',
            "Regenerated active API credentials. Old credentials invalidated."
        );

        // Flash plain keys once to screen
        return back()->with([
            'success' => 'API Credentials generated successfully. Make sure to copy them now as the secret key won\'t be shown again.',
            'api_key' => $keys['api_key'],
            'secret_key' => $keys['secret_key'],
            'webhook_secret' => $keys['webhook_secret'],
        ]);
    }

    public function downloadApiKeys()
    {
        $apiKey = session('api_key');
        $secretKey = session('secret_key');
        $webhookSecret = session('webhook_secret');

        if (!$apiKey || !$secretKey) {
            return redirect()->route('merchant.api-docs')->withErrors(['download' => 'No active session credentials found to download.']);
        }

        // Reflash the keys so they stay in the session
        session()->keep(['api_key', 'secret_key', 'webhook_secret']);

        $headers = [
            'Content-type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename=novexapay_api_credentials.csv',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0'
        ];

        $columns = ['Credential Name', 'Value'];

        $callback = function() use ($apiKey, $secretKey, $webhookSecret, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, ['Public Key (Client ID)', $apiKey]);
            fputcsv($file, ['Secret Key (Client Secret)', $secretKey]);
            fputcsv($file, ['Webhook Secret', $webhookSecret]);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function deleteApiKey(string $id)
    {
        $merchant = $this->getMerchant();
        $key = \App\Models\MerchantApiKey::where('merchant_id', $merchant->id)->findOrFail($id);
        $key->update(['is_active' => false]);
        $key->delete(); // Soft delete

        $this->auditLogService->log(
            'merchant_user',
            Auth::guard('merchant')->user()->id,
            $merchant->id,
            'api_key_revoked',
            "Revoked and deleted production API key: {$key->name}."
        );

        return back()->with('success', 'API key deleted/revoked successfully.');
    }

    public function addIpWhitelist(Request $request)
    {
        $request->validate([
            'ip_address' => 'required|ip',
            'description' => 'nullable|string|max:255',
        ]);

        $merchant = $this->getMerchant();

        // Check if already whitelisted
        $exists = MerchantIpWhitelist::where('merchant_id', $merchant->id)
            ->where('ip_address', $request->ip_address)
            ->exists();

        if ($exists) {
            return back()->withErrors(['ip_address' => 'This IP address is already whitelisted.']);
        }

        MerchantIpWhitelist::create([
            'merchant_id' => $merchant->id,
            'ip_address' => $request->ip_address,
            'description' => $request->description,
            'is_active' => true,
        ]);

        $this->auditLogService->log(
            'merchant_user',
            Auth::guard('merchant')->user()->id,
            $merchant->id,
            'ip_whitelist_added',
            "Added IP Whitelist: {$request->ip_address}"
        );

        return back()->with('success', 'IP address added to whitelist.');
    }

    public function deleteIpWhitelist(string $id)
    {
        $merchant = $this->getMerchant();
        $ip = MerchantIpWhitelist::where('merchant_id', $merchant->id)->findOrFail($id);
        $ipAddress = $ip->ip_address;
        $ip->delete();

        $this->auditLogService->log(
            'merchant_user',
            Auth::guard('merchant')->user()->id,
            $merchant->id,
            'ip_whitelist_deleted',
            "Deleted IP Whitelist: {$ipAddress}"
        );

        return back()->with('success', 'IP address removed from whitelist.');
    }

    public function toggleIpWhitelist(string $id)
    {
        $merchant = $this->getMerchant();
        $ip = MerchantIpWhitelist::where('merchant_id', $merchant->id)->findOrFail($id);
        $ip->update(['is_active' => !$ip->is_active]);

        $this->auditLogService->log(
            'merchant_user',
            Auth::guard('merchant')->user()->id,
            $merchant->id,
            'ip_whitelist_toggled',
            "Toggled IP Whitelist state for {$ip->ip_address}."
        );

        return back()->with('success', 'IP whitelist configuration toggled.');
    }

    public function tickets()
    {
        $merchant = $this->getMerchant();
        $tickets = SupportTicket::where('merchant_id', $merchant->id)
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('merchant.tickets.index', compact('merchant', 'tickets'));
    }

    public function createTicket(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'priority' => 'required|in:low,medium,high',
        ]);

        $merchant = $this->getMerchant();

        $ticket = SupportTicket::create([
            'merchant_id' => $merchant->id,
            'subject' => $request->subject,
            'message' => $request->message,
            'priority' => $request->priority,
            'status' => 'open',
            'replies' => [],
        ]);

        $this->auditLogService->log(
            'merchant_user',
            Auth::guard('merchant')->user()->id,
            $merchant->id,
            'ticket_creation',
            "Created support ticket: {$request->subject}."
        );

        return back()->with('success', 'Support ticket submitted successfully.');
    }

    public function viewTicket(string $id)
    {
        $merchant = $this->getMerchant();
        $ticket = SupportTicket::where('merchant_id', $merchant->id)->findOrFail($id);

        return view('merchant.tickets.view', compact('merchant', 'ticket'));
    }

    public function replyTicket(Request $request, string $id)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $merchant = $this->getMerchant();
        $ticket = SupportTicket::where('merchant_id', $merchant->id)->findOrFail($id);
        $user = Auth::guard('merchant')->user();

        $replies = $ticket->replies ?? [];
        $replies[] = [
            'user_type' => 'merchant_user',
            'user_name' => $user->name,
            'message' => $request->message,
            'created_at' => now()->toDateTimeString(),
        ];

        $ticket->update([
            'replies' => $replies,
            'status' => 'open', // Reopens/maintains open status on user reply
        ]);

        return back()->with('success', 'Reply posted successfully.');
    }

    // --- PAYOUTS ---
    public function payouts()
    {
        $merchant = $this->getMerchant();
        $payouts = Transaction::where('merchant_id', $merchant->id)
            ->where('type', 'payout')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        $beneficiaries = $merchant->beneficiaries()->orderBy('name', 'asc')->get();

        return view('merchant.payouts', compact('merchant', 'payouts', 'beneficiaries'));
    }

    public function submitPayout(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'ifsc' => 'required|string|max:20',
            'holder_name' => 'required|string|max:255',
        ]);

        $merchant = $this->getMerchant();
        $amount = (float)$request->amount;

        // Calculate commissions
        $calc = $this->commissionService->calculate($merchant->id, $amount, 'bank_transfer');
        $fee = $calc['fee'];
        $gst = $calc['gst'];
        $totalDebit = $amount + $fee + $gst;

        if ($merchant->wallet->balance < $totalDebit) {
            return back()->withErrors(['amount' => 'Insufficient wallet balance for this payout plus associated fees.'])->withInput();
        }

        $referenceId = 'TXN_' . strtoupper(Str::random(12));

        // Deduct wallet balance
        $this->walletService->debit($merchant->wallet->id, $totalDebit, 'payout_debit', "Payout of ₹{$amount} to {$request->account_number}. Fee: ₹{$fee}, GST: ₹{$gst}.");

        // Dispatch JioPay Upstream
        $jioResult = $this->jioPayService->transfer([
            'order_id' => $referenceId,
            'beneficiary_name' => $request->holder_name,
            'account_number' => $request->account_number,
            'ifsc' => $request->ifsc,
            'amount' => $amount,
        ]);

        $status = $jioResult['status'] === 'success' ? 'success' : 'failed';
        $providerRef = $jioResult['provider_reference_id'] ?? null;
        $failureReason = $jioResult['status'] === 'failed' ? $jioResult['message'] : null;

        // Log transaction
        $txn = Transaction::create([
            'merchant_id' => $merchant->id,
            'reference_id' => $referenceId,
            'type' => 'payout',
            'amount' => $amount,
            'fee' => $fee + $gst,
            'status' => $status,
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'ifsc' => $request->ifsc,
            'holder_name' => $request->holder_name,
            'provider_reference_id' => $providerRef,
            'response_payload' => $jioResult['response'] ?? []
        ]);

        if ($status === 'failed') {
            // Return funds back to merchant
            $this->walletService->credit($merchant->wallet->id, $totalDebit, 'refund', "Reversal payout failed: {$referenceId}");
            return back()->withErrors(['amount' => "Payout declined by Jiopay provider: {$failureReason}"])->withInput();
        }

        $this->auditLogService->log(
            'merchant_user',
            Auth::guard('merchant')->user()->id,
            $merchant->id,
            'payout_initiated',
            "Manual payout of ₹{$amount} initiated to account {$request->account_number}."
        );

        return back()->with('success', 'Payout processed successfully!');
    }

    // --- SETTLEMENTS ---
    public function settlements()
    {
        $merchant = $this->getMerchant();
        $settlements = \App\Models\MerchantSettlement::where('merchant_id', $merchant->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('merchant.settlements', compact('merchant', 'settlements'));
    }

    public function requestSettlement(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:100',
        ]);

        $merchant = $this->getMerchant();
        $amount = (float)$request->amount;

        // Settlements take 2% processing fee
        $fee = $amount * 0.02;
        $totalDebit = $amount + $fee;

        if ($merchant->wallet->balance < $totalDebit) {
            return back()->withErrors(['amount' => 'Insufficient wallet balance for this settlement request.'])->withInput();
        }

        // Deduct from wallet
        $this->walletService->debit($merchant->wallet->id, $totalDebit, 'settlement_debit', "Settlement transfer request of ₹{$amount}. Fee: ₹{$fee}.");

        // Record settlement
        $settlement = \App\Models\MerchantSettlement::create([
            'merchant_id' => $merchant->id,
            'reference_id' => 'SETL_' . strtoupper(Str::random(12)),
            'amount' => $amount,
            'fee' => $fee,
            'bank_name' => $merchant->profile->bank_name ?? 'Settlement Bank',
            'account_number' => $merchant->profile->bank_account_number ?? '000000000',
            'ifsc' => $merchant->profile->bank_ifsc ?? 'IFSC0000',
            'status' => 'success',
        ]);

        $this->auditLogService->log(
            'merchant_user',
            Auth::guard('merchant')->user()->id,
            $merchant->id,
            'settlement_requested',
            "Requested settlement of ₹{$amount}."
        );

        return back()->with('success', 'Settlement completed successfully!');
    }

    // --- COLLECTION ACCOUNT ---
    public function collections()
    {
        $merchant = $this->getMerchant();
        
        // Mock collection account details
        $virtualAccount = [
            'bank_name' => 'YES BANK LTD',
            'account_number' => '222' . substr(str_replace('-', '', $merchant->id), 0, 10),
            'ifsc' => 'YESB0CMSNOC',
            'holder_name' => $merchant->company_name,
        ];

        // Fetch success transactions as mock collections
        $collections = Transaction::where('merchant_id', $merchant->id)
            ->where('type', 'collection')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('merchant.collections', compact('merchant', 'virtualAccount', 'collections'));
    }

    // --- CREDIT CARD TO BANK ---
    public function ccToBank()
    {
        $merchant = $this->getMerchant();
        $transfers = Transaction::where('merchant_id', $merchant->id)
            ->where('type', 'cc_transfer')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('merchant.cc_to_bank', compact('merchant', 'transfers'));
    }

    public function processCcToBank(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:500',
            'card_number' => 'required|string|min:16',
            'card_holder' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'ifsc' => 'required|string|max:20',
        ]);

        $merchant = $this->getMerchant();
        $amount = (float)$request->amount;

        // Credit Card swiping takes 3% flat fee
        $ccFee = $amount * 0.03;
        $creditedAmount = $amount - $ccFee;

        // Add to wallet balance first (simulated collection credit)
        $this->walletService->credit($merchant->wallet->id, $creditedAmount, 'cc_payment_swipe', "CC swipe collection. Amount: ₹{$amount}, Fee: ₹{$ccFee}.");

        // Immediately initiate Bank Payout from wallet
        $calc = $this->commissionService->calculate($merchant->id, $creditedAmount, 'bank_transfer');
        $payoutFee = $calc['fee'] + $calc['gst'];
        
        $payoutAmount = $creditedAmount - $payoutFee;

        $this->walletService->debit($merchant->wallet->id, $creditedAmount, 'cc_payout_debit', "Payout CC transfer to {$request->account_number}.");

        // Record payout transaction
        Transaction::create([
            'merchant_id' => $merchant->id,
            'reference_id' => 'CC_' . strtoupper(Str::random(12)),
            'type' => 'cc_transfer',
            'amount' => $payoutAmount,
            'fee' => $ccFee + $payoutFee,
            'status' => 'success',
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'ifsc' => $request->ifsc,
            'holder_name' => $request->card_holder,
            'response_payload' => ['card_swipe' => 'processed', 'status' => 'settled']
        ]);

        $this->auditLogService->log(
            'merchant_user',
            Auth::guard('merchant')->user()->id,
            $merchant->id,
            'cc_to_bank_processed',
            "Completed CC to Bank transfer of ₹{$payoutAmount}."
        );

        return back()->with('success', 'Credit card to Bank transfer processed and settled successfully!');
    }

    // --- VIRTUAL ACCOUNTS ---
    public function virtualAccounts()
    {
        $merchant = $this->getMerchant();
        
        // Mock virtual accounts
        $virtualAccounts = [
            [
                'customer_name' => 'John Doe Enterprises',
                'account_number' => 'VA' . strtoupper(Str::random(8)),
                'ifsc' => 'ICIC0000104',
                'status' => 'active'
            ],
            [
                'customer_name' => 'Acme Corp Pvt Ltd',
                'account_number' => 'VA' . strtoupper(Str::random(8)),
                'ifsc' => 'ICIC0000104',
                'status' => 'active'
            ]
        ];

        return view('merchant.virtual_accounts', compact('merchant', 'virtualAccounts'));
    }

    // --- DYNAMIC QR ---
    public function dynamicQr()
    {
        $merchant = $this->getMerchant();
        return view('merchant.dynamic_qr', compact('merchant'));
    }

    // --- PAYMENT LINKS ---
    public function paymentLinks()
    {
        $merchant = $this->getMerchant();
        $links = \App\Models\MerchantPaymentLink::where('merchant_id', $merchant->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('merchant.payment_links', compact('merchant', 'links'));
    }

    public function createPaymentLink(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'description' => 'required|string|max:255',
        ]);

        $merchant = $this->getMerchant();

        $link = \App\Models\MerchantPaymentLink::create([
            'merchant_id' => $merchant->id,
            'reference_id' => 'PLNK_' . strtoupper(Str::random(12)),
            'amount' => $request->amount,
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'description' => $request->description,
            'status' => 'pending'
        ]);

        $this->auditLogService->log(
            'merchant_user',
            Auth::guard('merchant')->user()->id,
            $merchant->id,
            'payment_link_created',
            "Generated payment link for customer: {$request->customer_email}."
        );

        return back()->with('success', 'Payment link created successfully!');
    }

    // --- DEVELOPER: API DOCS & WEBHOOKS ---
    public function apiDocs()
    {
        $merchant = $this->getMerchant();
        $keys = $merchant->apiKeys;

        return view('merchant.api_docs', compact('merchant', 'keys'));
    }

    public function webhooks()
    {
        $merchant = $this->getMerchant();
        $webhook = \App\Models\MerchantWebhook::where('merchant_id', $merchant->id)->first();
        
        $logs = \App\Models\ApiLog::where('merchant_id', $merchant->id)
            ->where('endpoint', 'like', '%webhook%')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('merchant.webhooks', compact('merchant', 'webhook', 'logs'));
    }

    public function updateWebhooks(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
        ]);

        $merchant = $this->getMerchant();
        $webhook = \App\Models\MerchantWebhook::where('merchant_id', $merchant->id)->first();

        if ($webhook) {
            $webhook->update([
                'url' => $request->url,
                'is_active' => $request->has('is_active')
            ]);
        } else {
            \App\Models\MerchantWebhook::create([
                'merchant_id' => $merchant->id,
                'url' => $request->url,
                'secret_key' => 'whsec_' . Str::random(24),
                'is_active' => true
            ]);
        }

        $this->auditLogService->log(
            'merchant_user',
            Auth::guard('merchant')->user()->id,
            $merchant->id,
            'webhook_updated',
            "Updated webhook URL endpoint to {$request->url}."
        );

        return back()->with('success', 'Webhook configuration updated successfully.');
    }

    // --- ACCOUNT: KYC, DISPUTES, SETTINGS ---
    public function kyc()
    {
        $merchant = $this->getMerchant();
        $profile = $merchant->profile;

        return view('merchant.kyc', compact('merchant', 'profile'));
    }

    public function uploadKyc(Request $request)
    {
        $request->validate([
            'pan' => 'required|string|max:10',
            'gst' => 'required|string|max:15',
            'bank_name' => 'required|string|max:255',
            'bank_account_number' => 'required|string|max:255',
            'bank_ifsc' => 'required|string|max:20',
            'kyc_doc' => 'required|file|mimes:pdf,jpg,png|max:5120',
        ]);

        $merchant = $this->getMerchant();

        $path = $request->file('kyc_doc')->store('kyc_documents', 'public');

        $profile = $merchant->profile;
        if ($profile) {
            $profile->update([
                'pan' => strtoupper($request->pan),
                'gst' => strtoupper($request->gst),
                'bank_name' => $request->bank_name,
                'bank_account_number' => $request->bank_account_number,
                'bank_ifsc' => strtoupper($request->bank_ifsc),
                'kyc_document_path' => $path,
            ]);
        } else {
            \App\Models\MerchantProfile::create([
                'merchant_id' => $merchant->id,
                'pan' => strtoupper($request->pan),
                'gst' => strtoupper($request->gst),
                'bank_name' => $request->bank_name,
                'bank_account_number' => $request->bank_account_number,
                'bank_ifsc' => strtoupper($request->bank_ifsc),
                'kyc_document_path' => $path,
            ]);
        }

        $merchant->update(['kyc_status' => 'submitted']);

        $this->auditLogService->log(
            'merchant_user',
            Auth::guard('merchant')->user()->id,
            $merchant->id,
            'kyc_uploaded',
            "Uploaded KYC compliance documentation."
        );

        return back()->with('success', 'KYC compliance documents uploaded successfully!');
    }

    public function disputes()
    {
        $merchant = $this->getMerchant();
        $disputes = \App\Models\MerchantDispute::where('merchant_id', $merchant->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('merchant.disputes', compact('merchant', 'disputes'));
    }

    public function settings()
    {
        $merchant = $this->getMerchant();
        return view('merchant.settings', compact('merchant'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'business_type' => 'required|string|max:255',
        ]);

        $merchant = $this->getMerchant();
        $merchant->update([
            'business_name' => $request->business_name,
            'business_type' => $request->business_type,
        ]);

        return back()->with('success', 'Merchant settings updated successfully.');
    }
}
