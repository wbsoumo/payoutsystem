@extends('layouts.merchant')
@section('title', 'Dashboard')
@section('page_title', 'Analytics Dashboard')

@section('content')
<div class="space-y-8" x-data="{}">

    <!-- Overview Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Balance Card -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Wallet Balance</span>
                <span class="text-2xl font-extrabold text-slate-900 font-display">₹{{ number_format($wallet->balance, 2) }}</span>
                <span class="text-[10px] text-slate-400 block">Frozen: ₹{{ number_format($wallet->frozen_balance, 2) }}</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shadow-sm"><i class="fa-solid fa-wallet"></i></div>
        </div>

        <!-- Today's Volume -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Today's Payouts</span>
                <span class="text-2xl font-extrabold text-slate-900 font-display">₹{{ number_format($todayVolume, 2) }}</span>
                <span class="text-[10px] text-slate-400 block">Settled successfully</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-green-50 text-green-600 flex items-center justify-center text-xl shadow-sm"><i class="fa-solid fa-arrow-trend-up"></i></div>
        </div>

        <!-- Success Rate -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Success Rate</span>
                <span class="text-2xl font-extrabold text-slate-900 font-display">{{ $successRate }}%</span>
                <span class="text-[10px] text-slate-400 block">Today's transactions status</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl shadow-sm"><i class="fa-solid fa-circle-check"></i></div>
        </div>

        <!-- Pending / Failed counts -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Processing & Failed</span>
                <span class="text-2xl font-extrabold text-slate-900 font-display">{{ $pendingCount }} / {{ $failedCount }}</span>
                <span class="text-[10px] text-slate-400 block">Requires attention / retry</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shadow-sm"><i class="fa-solid fa-circle-exclamation"></i></div>
        </div>
    </div>

    <!-- Main Dashboard Section: Graph & Recent activity -->
    <div class="grid lg:grid-cols-3 gap-8">
        <!-- Volume Graph -->
        <div class="lg:col-span-2 bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-6">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-slate-900 text-lg">Transaction Volumes</h3>
                    <p class="text-xs text-slate-500">Hourly payouts processing volume analytics.</p>
                </div>
                <span class="text-xs font-bold text-slate-500 bg-slate-100 px-3 py-1 rounded-full uppercase">Last 7 Days</span>
            </div>

            <!-- Custom CSS Bar Chart -->
            <div class="h-64 flex items-end justify-between gap-4 pt-4 border-b border-slate-100">
                @php
                    $maxVolume = collect($chartData)->max('volume');
                    $maxVolume = $maxVolume > 0 ? $maxVolume : 1;
                @endphp
                @foreach($chartData as $data)
                    @php
                        $percentage = ($data['volume'] / $maxVolume) * 100;
                        $percentage = $percentage > 0 ? $percentage : 4; // minimum height
                    @endphp
                    <div class="flex-grow flex flex-col items-center gap-2 group">
                        <span class="text-[9px] font-bold text-slate-500 opacity-0 group-hover:opacity-100 transition-opacity">₹{{ number_format($data['volume'], 0) }}</span>
                        <div class="w-full bg-blue-100 group-hover:bg-blue-600 rounded-t-lg transition-all duration-300 shadow-sm"
                             style="height: {{ $percentage * 1.8 }}px;"></div>
                        <span class="text-xs font-semibold text-slate-400">{{ $data['day'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Announcements / Notifications Panel -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-6">
            <div>
                <h3 class="font-bold text-slate-900 text-lg">System Updates</h3>
                <p class="text-xs text-slate-500">Important notices and updates from administration.</p>
            </div>
            
            <div class="space-y-4">
                <div class="p-4 bg-brand-50 border border-brand-100 rounded-2xl space-y-1">
                    <div class="flex justify-between text-xs font-bold text-brand-600">
                        <span>API v1.2 Gateway Upgrade</span>
                        <span>Today</span>
                    </div>
                    <p class="text-xs text-brand-850 leading-relaxed">We upgraded our signature validation engine. The replay protection window is now locked at 300 seconds.</p>
                </div>

                <div class="p-4 bg-slate-50 rounded-2xl space-y-1">
                    <div class="flex justify-between text-xs font-bold text-slate-600">
                        <span>KYC Review Schedule</span>
                        <span>Yesterday</span>
                    </div>
                    <p class="text-xs text-slate-500 leading-relaxed">Merchants must submit GST and PAN documents on the settings page to unlock live payouts.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions List -->
    <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-6">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="font-bold text-slate-900 text-lg">Latest Transactions</h3>
                <p class="text-xs text-slate-500">A detailed chronological view of recent incoming and outgoing API transactions.</p>
            </div>
            <a href="{{ route('merchant.ledger') }}" class="text-xs font-bold text-blue-600 hover:underline">View All Ledger Logs <i class="fa-solid fa-arrow-right ml-1"></i></a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold">
                    <tr>
                        <th class="px-6 py-3">Reference ID</th>
                        <th class="px-6 py-3">Client Reference</th>
                        <th class="px-6 py-3">Amount</th>
                        <th class="px-6 py-3">Fees + GST</th>
                        <th class="px-6 py-3">Closing Balance</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Created At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-slate-700">
                    @forelse($latestTransactions as $txn)
                        <tr>
                            <td class="px-6 py-4 font-mono font-bold text-brand-600">{{ $txn->reference_id }}</td>
                            <td class="px-6 py-4 font-mono">{{ $txn->client_reference_id }}</td>
                            <td class="px-6 py-4 font-extrabold text-slate-900">₹{{ number_format($txn->amount, 2) }}</td>
                            <td class="px-6 py-4">₹{{ number_format($txn->total_charges, 2) }}</td>
                            <td class="px-6 py-4">₹{{ number_format($txn->closing_balance, 2) }}</td>
                            <td class="px-6 py-4">
                                @if($txn->status === 'success')
                                    <span class="px-2.5 py-0.5 bg-green-50 text-green-700 border border-green-200 rounded-md font-bold uppercase text-[10px]">Success</span>
                                @elseif($txn->status === 'pending' || $txn->status === 'processing')
                                    <span class="px-2.5 py-0.5 bg-yellow-50 text-yellow-700 border border-yellow-200 rounded-md font-bold uppercase text-[10px]">Processing</span>
                                @else
                                    <span class="px-2.5 py-0.5 bg-red-50 text-red-700 border border-red-200 rounded-md font-bold uppercase text-[10px]">Failed</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">{{ $txn->created_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-slate-400 font-semibold">No recent transactions processed.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
