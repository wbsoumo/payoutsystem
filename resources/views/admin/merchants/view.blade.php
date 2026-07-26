@extends('layouts.admin')
@section('title', 'Merchant Detail')
@section('page_title', 'Merchant Inspector')

@section('content')
<div class="space-y-6">

    @if($merchant->kyc_status === 'submitted')
        <div class="p-6 bg-yellow-50/80 border border-yellow-200 rounded-3xl flex flex-col md:flex-row justify-between items-start md:items-center gap-6 shadow-sm">
            <div class="space-y-1">
                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-yellow-500 text-white uppercase tracking-wider">KYC Pending Audit</span>
                <h4 class="text-sm font-bold text-slate-800">This Merchant has submitted new compliance documentation.</h4>
                <p class="text-xs text-slate-600">Please review their GST certificate, PAN card details, and bank account proofs below before changing their activation status.</p>
            </div>
            <div class="flex gap-2">
                <form action="{{ route('admin.merchants.kyc', $merchant->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="kyc_status" value="rejected">
                    <button type="submit" class="px-4 py-2 border border-red-200 hover:bg-red-50 text-red-600 font-bold rounded-xl text-xs transition-colors bg-white">
                        <i class="fa-solid fa-circle-xmark mr-1"></i> Reject Documents
                    </button>
                </form>
                <form action="{{ route('admin.merchants.kyc', $merchant->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="kyc_status" value="approved">
                    <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-500 text-white font-bold rounded-xl text-xs shadow-md shadow-green-500/10 transition-colors">
                        <i class="fa-solid fa-circle-check mr-1"></i> Approve Compliance
                    </button>
                </form>
            </div>
        </div>
    @endif

    <!-- Card Header Info -->
    <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm flex flex-col md:flex-row justify-between gap-6">
        <div class="space-y-2">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">MERCHANT ID: {{ $merchant->id }}</span>
            <h1 class="text-2xl font-bold text-slate-900">{{ $merchant->company_name }}</h1>
            <p class="text-slate-500 text-sm">Entity: <span class="font-semibold text-slate-700">{{ ucfirst($merchant->business_type) }}</span> | Brand: <span class="font-semibold text-slate-700">{{ $merchant->business_name }}</span></p>
        </div>
        
        <!-- Status configurations -->
        <div class="flex flex-wrap items-center gap-4">
            <!-- KYC Configuration form -->
            <form action="{{ route('admin.merchants.kyc', $merchant->id) }}" method="POST" class="flex items-center gap-2">
                @csrf
                <select name="kyc_status" onchange="this.form.submit()"
                        class="h-9 px-3 border border-slate-200 rounded-lg text-xs font-bold bg-slate-50">
                    <option value="pending" {{ $merchant->kyc_status === 'pending' ? 'selected' : '' }}>KYC: Pending Docs</option>
                    <option value="submitted" {{ $merchant->kyc_status === 'submitted' ? 'selected' : '' }}>KYC: Submitted</option>
                    <option value="approved" {{ $merchant->kyc_status === 'approved' ? 'selected' : '' }}>KYC: Approve Compliance</option>
                    <option value="rejected" {{ $merchant->kyc_status === 'rejected' ? 'selected' : '' }}>KYC: Reject Docs</option>
                </select>
            </form>

            <!-- Status Configuration form -->
            <form action="{{ route('admin.merchants.status', $merchant->id) }}" method="POST" class="flex items-center gap-2">
                @csrf
                <select name="status" onchange="this.form.submit()"
                        class="h-9 px-3 border border-slate-200 rounded-lg text-xs font-bold bg-slate-50">
                    <option value="pending" {{ $merchant->status === 'pending' ? 'selected' : '' }}>Status: Pending</option>
                    <option value="active" {{ $merchant->status === 'active' ? 'selected' : '' }}>Status: Activate Merchant</option>
                    <option value="suspended" {{ $merchant->status === 'suspended' ? 'selected' : '' }}>Status: Suspend Merchant</option>
                </select>
            </form>
        </div>
    </div>

    <!-- Inspector Core Tabs -->
    <div class="grid lg:grid-cols-12 gap-8">
        
        <!-- Details Column -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- Profile Info Card -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-4">
                <h3 class="font-bold text-slate-900 text-lg">Identity details</h3>
                
                <div class="grid md:grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-slate-400 font-bold block">PAN CARD</span>
                        <span class="font-mono text-sm font-bold text-slate-800 uppercase">{{ $merchant->profile->pan ?? 'Not submitted' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-bold block">GSTIN</span>
                        <span class="font-mono text-sm font-bold text-slate-800 uppercase">{{ $merchant->profile->gst ?? 'Not submitted' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-bold block">OFFICIAL BANK ACCOUNT</span>
                        <span class="font-semibold text-slate-800">
                            @if($merchant->profile && $merchant->profile->bank_name)
                                {{ $merchant->profile->bank_name }} - {{ $merchant->profile->bank_account_number }} (IFSC: {{ $merchant->profile->bank_ifsc }})
                            @else
                                Not submitted
                            @endif
                        </span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-bold block">KYC PROOF DOCUMENT</span>
                        @if($merchant->profile && $merchant->profile->kyc_document_path)
                            <a href="{{ asset('storage/' . $merchant->profile->kyc_document_path) }}" target="_blank" class="text-blue-600 font-bold hover:underline"><i class="fa-solid fa-file-pdf mr-1 text-red-500"></i> View Submitted KYC Document</a>
                        @else
                            <span class="text-slate-500 italic">No document uploaded</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Merchant Users & Access Control Card -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-6">
                <div>
                    <h3 class="font-bold text-slate-900 text-lg">Merchant Representative Users</h3>
                    <p class="text-xs text-slate-500">Logins configured for this merchant entity. You can edit credentials or login directly as any user.</p>
                </div>

                <div class="space-y-6">
                    @forelse($merchant->users as $u)
                        <div class="p-5 border border-slate-100 rounded-2xl bg-slate-50/50 space-y-4">
                            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                                <div class="space-y-1">
                                    <span class="text-sm font-bold text-slate-800 block">{{ $u->name }}</span>
                                    <span class="text-xs text-slate-500 block font-mono">{{ $u->email }} | {{ $u->phone }}</span>
                                    <span class="text-[10px] text-slate-400 font-bold block uppercase mt-1">Role: {{ ucfirst($u->role ?? 'admin') }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <!-- Impersonate Button -->
                                    <form action="{{ route('admin.merchants.impersonate', $u->id) }}" method="POST" target="_blank">
                                        @csrf
                                        <button type="submit" class="px-3 h-9 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-lg transition-colors text-xs flex items-center gap-1.5 shadow-sm">
                                            <i class="fa-solid fa-right-to-bracket"></i> Login as Merchant
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="h-[1px] bg-slate-200/60"></div>

                            <!-- Edit and Reset Password Panel -->
                            <div class="grid md:grid-cols-2 gap-6">
                                <!-- Update Profile form -->
                                <form action="{{ route('admin.merchants.user.update', $u->id) }}" method="POST" class="space-y-3">
                                    @csrf
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Update Profile Details</span>
                                    <div class="grid grid-cols-2 gap-2">
                                        <input type="text" name="name" value="{{ $u->name }}" required placeholder="Full Name"
                                               class="h-9 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-white">
                                        <input type="text" name="phone" value="{{ $u->phone }}" required placeholder="Phone Number"
                                               class="h-9 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-white">
                                    </div>
                                    <div class="flex gap-2">
                                        <input type="email" name="email" value="{{ $u->email }}" required placeholder="Email Address"
                                               class="w-full h-9 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-white">
                                        <button type="submit" class="px-4 h-9 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-lg text-xs transition-colors shrink-0">
                                            Save
                                        </button>
                                    </div>
                                </form>

                                <!-- Reset Password form -->
                                <form action="{{ route('admin.merchants.user.password', $u->id) }}" method="POST" class="space-y-3">
                                    @csrf
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Reset Login Password</span>
                                    <div class="grid grid-cols-2 gap-2">
                                        <input type="password" name="password" required placeholder="New Password"
                                               class="h-9 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-white">
                                        <input type="password" name="password_confirmation" required placeholder="Confirm"
                                               class="h-9 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-white">
                                    </div>
                                    <button type="submit" class="w-full h-9 bg-red-600 hover:bg-red-500 text-white font-bold rounded-lg text-xs transition-colors shadow-sm shadow-red-500/10">
                                        Reset User Password
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-slate-400 font-semibold text-xs">No representative users configured for this merchant.</div>
                    @endforelse
                </div>
            </div>

            <!-- Login histories/Map Mock -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-6">
                <div>
                    <h3 class="font-bold text-slate-900 text-lg">Geo-Location Login History</h3>
                    <p class="text-xs text-slate-500">Geographic tracking audits generated during dashboard login prompts.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-left text-xs">
                        <thead class="bg-slate-50 text-slate-500 font-bold">
                            <tr>
                                <th class="px-6 py-3">User IP</th>
                                <th class="px-6 py-3">Resolved Location</th>
                                <th class="px-6 py-3">OS & Browser</th>
                                <th class="px-6 py-3">Screen Size</th>
                                <th class="px-6 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 text-slate-700">
                            @forelse($logins as $login)
                                <tr>
                                    <td class="px-6 py-4 font-mono font-bold text-slate-500">{{ $login->ip_address }}</td>
                                    <td class="px-6 py-4">
                                        @if($login->latitude)
                                            <div class="font-semibold text-slate-800">{{ $login->city }}, {{ $login->state }}, {{ $login->country }}</div>
                                            <div class="text-[9px] text-slate-400 mt-0.5">Coordinates: {{ $login->latitude }}, {{ $login->longitude }}</div>
                                        @else
                                            <span class="text-slate-400 italic">Location Denied</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-semibold">{{ $login->operating_system }}</div>
                                        <div class="text-[9px] text-slate-400 mt-0.5">{{ $login->browser }}</div>
                                    </td>
                                    <td class="px-6 py-4 font-mono">{{ $login->screen_resolution ?? 'Unknown' }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase {{ $login->status === 'success' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200' }}">{{ $login->status }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-6 text-center text-slate-400 font-semibold">No login history recorded.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Wallet Adjustments / Actions Column -->
        <div class="lg:col-span-4 space-y-6">
            
            <!-- Wallet Balance Card -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-4">
                <h3 class="font-bold text-slate-900 text-sm">Wallet Balance</h3>
                
                <div class="space-y-1">
                    <span class="text-[10px] text-slate-400 font-bold block uppercase">CURRENT BALANCE</span>
                    <span class="text-3xl font-extrabold text-slate-900 font-display">₹{{ number_format($merchant->wallet->balance ?? 0.0, 2) }}</span>
                </div>

                <div class="h-[1px] bg-slate-100"></div>

                <!-- Balance manual credit/debit adjustment -->
                <form action="{{ route('admin.merchants.wallet.adjust', $merchant->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Manual Balance Adjustment</span>
                    
                    <div class="grid grid-cols-2 gap-2">
                        <label class="border border-slate-200 rounded-xl p-3 flex items-center gap-2 cursor-pointer hover:bg-slate-50 transition-colors">
                            <input type="radio" name="type" value="credit" checked class="text-blue-600 focus:ring-blue-500">
                            <span class="text-xs font-bold text-slate-700">Credit</span>
                        </label>
                        <label class="border border-slate-200 rounded-xl p-3 flex items-center gap-2 cursor-pointer hover:bg-slate-50 transition-colors">
                            <input type="radio" name="type" value="debit" class="text-blue-600 focus:ring-blue-500">
                            <span class="text-xs font-bold text-slate-700">Debit</span>
                        </label>
                    </div>

                    <div class="space-y-1">
                        <label for="amount" class="text-[10px] font-bold text-slate-500 uppercase">Amount</label>
                        <input type="number" name="amount" id="amount" step="0.01" min="0.01" placeholder="₹0.00" required
                               class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50 font-bold">
                    </div>

                    <div class="space-y-1">
                        <label for="description" class="text-[10px] font-bold text-slate-500 uppercase">Adjustment Reason</label>
                        <input type="text" name="description" id="description" placeholder="e.g. Settlement credit override" required
                               class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50">
                    </div>

                    <button type="submit" class="w-full h-11 btn-gradient text-white text-xs font-bold rounded-lg shadow-lg shadow-brand-500/10">
                        Execute Ledger Adjustment
                    </button>
                </form>
            </div>

        </div>

    </div>

</div>
@endsection
