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

    public function __construct(ApiService $apiService, AuditLogService $auditLogService, WalletService $walletService)
    {
        $this->apiService = $apiService;
        $this->auditLogService = $auditLogService;
        $this->walletService = $walletService;
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
            'new_password' => 'required|min:8|confirmed',
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
        // 2FA Ready simulation: requires user verification or password confirmation
        $request->validate([
            'password' => 'required',
            'otp_code' => 'required|string|max:6', // Mock OTP logic
        ]);

        $user = Auth::guard('merchant')->user();
        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Invalid password confirmation.']);
        }

        if ($request->otp_code !== '123456') { // Mock valid OTP
            return back()->withErrors(['otp_code' => 'Invalid or expired OTP code. Use 123456 for testing.']);
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
}
