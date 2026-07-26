<?php

namespace App\Http\Controllers;

use App\Models\ContactRequest;
use App\Models\Merchant;
use App\Models\Commission;
use App\Models\SupportTicket;
use App\Models\AuditLog;
use App\Models\ApiLog;
use App\Models\LoginHistory;
use App\Services\MerchantService;
use App\Services\WalletService;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{
    protected MerchantService $merchantService;
    protected WalletService $walletService;
    protected AuditLogService $auditLogService;

    public function __construct(
        MerchantService $merchantService,
        WalletService $walletService,
        AuditLogService $auditLogService
    ) {
        $this->merchantService = $merchantService;
        $this->walletService = $walletService;
        $this->auditLogService = $auditLogService;
    }

    protected function getAdmin()
    {
        return Auth::guard('admin')->user();
    }

    public function index()
    {
        $admin = $this->getAdmin();

        $merchantsCount = Merchant::count();
        $pendingKycCount = Merchant::where('kyc_status', 'submitted')->count();
        $openTicketsCount = SupportTicket::whereIn('status', ['open', 'replied'])->count();
        $pendingEnquiries = ContactRequest::where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('admin', 'merchantsCount', 'pendingKycCount', 'openTicketsCount', 'pendingEnquiries'));
    }

    public function merchants()
    {
        $merchants = Merchant::orderBy('created_at', 'desc')->get();
        return view('admin.merchants.index', compact('merchants'));
    }

    public function merchantView(string $id)
    {
        $merchant = Merchant::with(['wallet', 'profile', 'apiKeys', 'ipWhitelists', 'users'])->findOrFail($id);
        $transactions = $merchant->transactions()->orderBy('created_at', 'desc')->take(10)->get();
        $logins = LoginHistory::where('user_type', 'merchant_user')
            ->whereIn('user_id', $merchant->users()->pluck('id'))
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $commissions = Commission::where('merchant_id', $merchant->id)->get();

        return view('admin.merchants.view', compact('merchant', 'transactions', 'logins', 'commissions'));
    }

    public function merchantStatusUpdate(Request $request, string $id)
    {
        $request->validate(['status' => 'required|in:pending,active,suspended']);
        $merchant = Merchant::findOrFail($id);
        $oldStatus = $merchant->status;
        $merchant->update(['status' => $request->status]);

        $this->auditLogService->log(
            'admin',
            $this->getAdmin()->id,
            $merchant->id,
            'merchant_status_change',
            "Changed merchant status from {$oldStatus} to {$request->status}.",
            ['old_status' => $oldStatus, 'new_status' => $request->status]
        );

        return back()->with('success', 'Merchant status updated successfully.');
    }

    public function merchantKycUpdate(Request $request, string $id)
    {
        $request->validate(['kyc_status' => 'required|in:pending,submitted,approved,rejected']);
        $merchant = Merchant::findOrFail($id);
        $oldKyc = $merchant->kyc_status;
        $merchant->update(['kyc_status' => $request->kyc_status]);

        // If KYC is approved, activate merchant as well
        if ($request->kyc_status === 'approved' && $merchant->status === 'pending') {
            $merchant->update(['status' => 'active']);
        }

        $this->auditLogService->log(
            'admin',
            $this->getAdmin()->id,
            $merchant->id,
            'merchant_kyc_change',
            "Changed merchant KYC status from {$oldKyc} to {$request->kyc_status}.",
            ['old_kyc' => $oldKyc, 'new_kyc' => $request->kyc_status]
        );

        return back()->with('success', 'KYC compliance status updated successfully.');
    }

    public function merchantWalletAdjustment(Request $request, string $id)
    {
        $request->validate([
            'type' => 'required|in:credit,debit',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
        ]);

        $merchant = Merchant::findOrFail($id);
        $adminId = $this->getAdmin()->id;

        if ($request->type === 'credit') {
            $ledger = $this->walletService->creditWallet(
                $merchant->id,
                (float) $request->amount,
                "Manual Adjustment: " . $request->description,
                'manual_adjustment',
                null,
                $adminId
            );
        } else {
            $ledger = $this->walletService->debitWallet(
                $merchant->id,
                (float) $request->amount,
                "Manual Adjustment: " . $request->description,
                'manual_adjustment',
                null,
                $adminId
            );
        }

        if (!$ledger) {
            return back()->withErrors(['amount' => 'Failed to adjust wallet balance. Insufficient funds or invalid setup.']);
        }

        return back()->with('success', 'Wallet ledger balance adjusted successfully.');
    }

    public function enquiries()
    {
        $enquiries = ContactRequest::orderBy('created_at', 'desc')->get();
        return view('admin.enquiries', compact('enquiries'));
    }

    public function enquiryConvert(string $id)
    {
        $merchant = $this->merchantService->createMerchantFromEnquiry($id, $this->getAdmin()->id);
        if (!$merchant) {
            return back()->withErrors(['error' => 'Failed to convert contact request or already converted.']);
        }

        return redirect()->route('admin.merchants.view', $merchant->id)
            ->with('success', 'Contact request converted into Merchant profile successfully.');
    }

    public function commissions()
    {
        $commissions = Commission::orderBy('merchant_id', 'asc')
            ->orderBy('effective_date', 'desc')
            ->get();
        $merchants = Merchant::where('status', 'active')->get();

        return view('admin.commissions', compact('commissions', 'merchants'));
    }

    public function commissionStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'merchant_id' => 'nullable|exists:merchants,id', // null means global
            'type' => 'required|in:fixed,percentage,slab',
            'fixed_charge' => 'nullable|numeric|min:0',
            'percentage_charge' => 'nullable|numeric|min:0|max:100',
            'slab_rates' => 'nullable|string', // raw JSON string
            'min_charge' => 'nullable|numeric|min:0',
            'max_charge' => 'nullable|numeric|min:0',
            'gst_rate' => 'required|numeric|min:0|max:100',
            'effective_date' => 'required|date',
        ]);

        $slabs = null;
        if ($request->type === 'slab' && $request->slab_rates) {
            $slabs = json_decode($request->slab_rates, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withErrors(['slab_rates' => 'Invalid JSON structure for slab rates.'])->withInput();
            }
        }

        $commission = Commission::create([
            'name' => $request->name,
            'merchant_id' => $request->merchant_id,
            'type' => $request->type,
            'fixed_charge' => $request->type === 'fixed' ? $request->fixed_charge : 0,
            'percentage_charge' => $request->type === 'percentage' ? $request->percentage_charge : 0,
            'slab_rates' => $slabs,
            'min_charge' => $request->min_charge,
            'max_charge' => $request->max_charge,
            'gst_rate' => $request->gst_rate,
            'effective_date' => $request->effective_date,
            'is_active' => true,
        ]);

        $scope = $request->merchant_id ? "Merchant Override ({$request->merchant_id})" : 'Global default';
        $this->auditLogService->log(
            'admin',
            $this->getAdmin()->id,
            $request->merchant_id,
            'commission_rate_created',
            "Created commission rule '{$request->name}' under scope: {$scope}.",
            ['commission_id' => $commission->id]
        );

        return back()->with('success', 'Commission engine rate configuration saved successfully.');
    }

    public function tickets()
    {
        $tickets = SupportTicket::with('merchant')->orderBy('updated_at', 'desc')->get();
        return view('admin.tickets.index', compact('tickets'));
    }

    public function ticketView(string $id)
    {
        $ticket = SupportTicket::with('merchant')->findOrFail($id);
        return view('admin.tickets.view', compact('ticket'));
    }

    public function ticketReply(Request $request, string $id)
    {
        $request->validate(['message' => 'required|string']);
        $ticket = SupportTicket::findOrFail($id);
        $admin = $this->getAdmin();

        $replies = $ticket->replies ?? [];
        $replies[] = [
            'user_type' => 'admin',
            'user_name' => $admin->name,
            'message' => $request->message,
            'created_at' => now()->toDateTimeString(),
        ];

        $ticket->update([
            'replies' => $replies,
            'status' => 'replied',
        ]);

        $this->auditLogService->log(
            'admin',
            $admin->id,
            $ticket->merchant_id,
            'ticket_reply',
            "Administrator replied to ticket: '{$ticket->subject}'."
        );

        return back()->with('success', 'Reply posted successfully.');
    }

    public function auditLogs()
    {
        $logs = AuditLog::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.logs.audit', compact('logs'));
    }

    public function apiLogs()
    {
        $logs = ApiLog::with('merchant')->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.logs.api', compact('logs'));
    }

    public function impersonateMerchantUser(string $merchantUserId)
    {
        $user = \App\Models\MerchantUser::findOrFail($merchantUserId);
        
        Auth::guard('merchant')->logout();
        Auth::guard('merchant')->login($user);
        
        session()->put('impersonating_from_admin', true);
        session()->put('admin_id', $this->getAdmin()->id);
        
        $this->auditLogService->log(
            'admin',
            $this->getAdmin()->id,
            $user->merchant_id,
            'impersonation_start',
            "Started impersonating merchant user: {$user->email} ({$user->name})."
        );

        return redirect()->route('merchant.dashboard')->with('success', "Logged in as {$user->name}");
    }

    public function updateMerchantUser(Request $request, string $merchantUserId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:merchant_users,email,' . $merchantUserId,
            'phone' => 'required|string|max:15',
        ]);

        $user = \App\Models\MerchantUser::findOrFail($merchantUserId);
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        $this->auditLogService->log(
            'admin',
            $this->getAdmin()->id,
            $user->merchant_id,
            'merchant_user_update',
            "Updated profile details for merchant user: {$user->email}."
        );

        return back()->with('success', 'Merchant user profile updated successfully.');
    }

    public function changeMerchantUserPassword(Request $request, string $merchantUserId)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = \App\Models\MerchantUser::findOrFail($merchantUserId);
        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
        ]);

        $this->auditLogService->log(
            'admin',
            $this->getAdmin()->id,
            $user->merchant_id,
            'merchant_user_password_change',
            "Updated password for merchant user: {$user->email}."
        );

        return back()->with('success', 'Merchant user password reset successfully.');
    }

    public function settings()
    {
        $admin = $this->getAdmin();
        $settings = \App\Models\Setting::all()->pluck('value', 'key');
        return view('admin.settings', compact('admin', 'settings'));
    }

    public function settingsUpdate(Request $request)
    {
        $request->validate([
            'default_gateway' => 'required|string|in:mock,jiopay',
            'jiopay_mid' => 'required|string',
            'jiopay_key' => 'required|string',
            'jiopay_entity_id' => 'required|string',
            'jiopay_customer_id' => 'required|string',
        ]);

        \App\Models\Setting::set('default_gateway', $request->default_gateway);
        \App\Models\Setting::set('jiopay_mid', $request->jiopay_mid);
        \App\Models\Setting::set('jiopay_key', $request->jiopay_key);
        \App\Models\Setting::set('jiopay_entity_id', $request->jiopay_entity_id);
        \App\Models\Setting::set('jiopay_customer_id', $request->jiopay_customer_id);

        $this->auditLogService->log(
            'admin',
            $this->getAdmin()->id,
            null,
            'system_settings_update',
            "Updated Jiopay gateway upstream credentials."
        );

        return back()->with('success', 'Upstream gateway configurations updated successfully.');
    }
}
