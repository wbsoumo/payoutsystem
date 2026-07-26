@extends('layouts.admin')
@section('title', 'Admin Dashboard')
@section('page_title', 'System Management Control Panel')

@section('content')
<div class="space-y-8">

    <!-- Active Gateway Router info panel -->
    <div class="p-6 bg-gradient-to-tr from-slate-900 to-indigo-950 rounded-3xl text-slate-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 shadow-md border border-slate-800">
        <div class="space-y-2">
            <span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-blue-500 text-white uppercase tracking-wider">Gateway Status</span>
            <h3 class="text-xl font-bold font-display">
                Active Upstream Gateway: 
                <span class="text-blue-400 capitalize">{{ \App\Models\Setting::get('default_gateway', 'mock') }} Gateway</span>
            </h3>
            <p class="text-xs text-slate-400 max-w-xl">
                Payout transactions are currently routed through the sandbox/production rails configured inside System Settings.
            </p>
        </div>
        <a href="{{ route('admin.settings') }}" class="px-5 py-2.5 bg-white/10 hover:bg-white/20 text-white font-bold rounded-xl text-xs transition-colors border border-white/10 flex items-center gap-1.5">
            <i class="fa-solid fa-gear"></i> Configure Gateway Settings
        </a>
    </div>

    <!-- Admin Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Merchants Count -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Merchants</span>
                <span class="text-3xl font-extrabold text-slate-900 font-display">{{ $merchantsCount }}</span>
                <span class="text-xs text-slate-500 block"><a href="{{ route('admin.merchants') }}" class="text-blue-600 font-bold hover:underline">View profiles</a></span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shadow-sm"><i class="fa-solid fa-users"></i></div>
        </div>

        <!-- Pending KYC -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">KYC Verification Queue</span>
                <span class="text-3xl font-extrabold text-slate-900 font-display">{{ $pendingKycCount }}</span>
                <span class="text-xs text-slate-500 block">Documents pending audit</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-yellow-50 text-yellow-600 flex items-center justify-center text-xl shadow-sm"><i class="fa-solid fa-circle-exclamation"></i></div>
        </div>

        <!-- System Volume (Mock/Calculated) -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Daily Payout Volume</span>
                <span class="text-3xl font-extrabold text-slate-900 font-display font-mono">₹4,82,500</span>
                <span class="text-xs text-green-500 font-bold block"><i class="fa-solid fa-arrow-trend-up"></i> +12.4% success</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-green-50 text-green-600 flex items-center justify-center text-xl shadow-sm"><i class="fa-solid fa-arrow-up-right-from-square"></i></div>
        </div>

        <!-- Support Tickets Count -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Active Support Tickets</span>
                <span class="text-3xl font-extrabold text-slate-900 font-display">{{ $openTicketsCount }}</span>
                <span class="text-xs text-slate-500 block"><a href="{{ route('admin.tickets') }}" class="text-blue-600 font-bold hover:underline">View help desk</a></span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl shadow-sm"><i class="fa-solid fa-life-ring"></i></div>
        </div>
    </div>

    <!-- Analytics Graph & Stats Panel -->
    <div class="grid lg:grid-cols-12 gap-8">
        <!-- Chart panel -->
        <div class="lg:col-span-8 bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-6">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-slate-900 text-lg">Transaction Activity</h3>
                    <p class="text-xs text-slate-500">Hourly processed volume across all registered merchant accounts.</p>
                </div>
                <span class="px-2.5 py-1 text-[10px] font-bold bg-green-50 text-green-700 border border-green-200 rounded-md">Live Uptime: 100%</span>
            </div>

            <!-- Dynamic line chart SVG -->
            <div class="h-44 w-full relative pt-4">
                <svg viewBox="0 0 500 100" class="w-full h-full text-blue-500 overflow-visible" preserveAspectRatio="none">
                    <defs>
                        <linearGradient id="chartGrad" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="rgb(59, 130, 246)" stop-opacity="0.2"/>
                            <stop offset="100%" stop-color="rgb(59, 130, 246)" stop-opacity="0.0"/>
                        </linearGradient>
                    </defs>
                    <!-- Area under curve -->
                    <path d="M 0 100 L 0 50 Q 125 10 250 80 T 500 40 L 500 100 Z" fill="url(#chartGrad)" />
                    <!-- Stroke curve -->
                    <path d="M 0 50 Q 125 10 250 80 T 500 40" fill="none" stroke="currentColor" stroke-width="2" />
                    <!-- Indicator dots -->
                    <circle cx="250" cy="80" r="4" fill="currentColor" />
                    <circle cx="500" cy="40" r="4" fill="currentColor" />
                </svg>
            </div>
        </div>

        <!-- Rails health status -->
        <div class="lg:col-span-4 bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-6">
            <div>
                <h3 class="font-bold text-slate-900 text-lg">Banking Rail Health</h3>
                <p class="text-xs text-slate-500">Live response speed & latency tests.</p>
            </div>

            <div class="space-y-4">
                <div class="flex justify-between items-center text-xs font-semibold p-3 bg-slate-50 rounded-xl">
                    <span class="text-slate-700 flex items-center gap-2"><span class="w-2 h-2 bg-green-500 rounded-full"></span> UPI Rails</span>
                    <span class="text-slate-900 font-mono font-bold">12ms (Normal)</span>
                </div>
                <div class="flex justify-between items-center text-xs font-semibold p-3 bg-slate-50 rounded-xl">
                    <span class="text-slate-700 flex items-center gap-2"><span class="w-2 h-2 bg-green-500 rounded-full"></span> IMPS Nodes</span>
                    <span class="text-slate-900 font-mono font-bold">45ms (Normal)</span>
                </div>
                <div class="flex justify-between items-center text-xs font-semibold p-3 bg-slate-50 rounded-xl">
                    <span class="text-slate-700 flex items-center gap-2"><span class="w-2 h-2 bg-green-500 rounded-full"></span> NEFT Gateway</span>
                    <span class="text-slate-900 font-mono font-bold">18ms (Normal)</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Request / Access Application Queue -->
    <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-6">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="font-bold text-slate-900 text-lg">Access Applications Queue</h3>
                <p class="text-xs text-slate-500">Businesses requesting invite access. Review volume projections and convert into active merchant profiles.</p>
            </div>
            <a href="{{ route('admin.enquiries') }}" class="text-xs font-bold text-blue-600 hover:underline">View All Enquiries <i class="fa-solid fa-arrow-right ml-1"></i></a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold">
                    <tr>
                        <th class="px-6 py-3">Company Name</th>
                        <th class="px-6 py-3">Contact Person</th>
                        <th class="px-6 py-3">Email & Phone</th>
                        <th class="px-6 py-3">Monthly Volume</th>
                        <th class="px-6 py-3">Business Entity</th>
                        <th class="px-6 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-slate-700">
                    @forelse($pendingEnquiries as $enquiry)
                        <tr>
                            <td class="px-6 py-4 font-bold text-slate-800">{{ $enquiry->company_name }}</td>
                            <td class="px-6 py-4 font-semibold text-slate-700">{{ $enquiry->full_name }}</td>
                            <td class="px-6 py-4">
                                <div class="font-bold">{{ $enquiry->email }}</div>
                                <div class="text-[10px] text-slate-400 mt-0.5">{{ $enquiry->phone }}</div>
                            </td>
                            <td class="px-6 py-4 font-mono font-semibold uppercase text-slate-600">{{ $enquiry->monthly_volume }}</td>
                            <td class="px-6 py-4 font-semibold text-slate-500">{{ ucfirst($enquiry->business_type) }}</td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('admin.enquiries.convert', $enquiry->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-lg transition-colors text-[10px] uppercase shadow-sm">
                                        Convert to Merchant
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-400 font-semibold">No pending access requests in queue.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
