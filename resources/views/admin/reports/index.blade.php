@extends('layouts.admin')
@section('title', 'Reports & Business Intelligence')
@section('page_title', 'System Reports')

@section('content')
<div class="space-y-8">

    <!-- Metrics Cards -->
    <div class="grid md:grid-cols-3 gap-6">
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total Processed Volume (GTV)</span>
            <h3 class="text-2xl font-extrabold text-slate-900 font-display mt-2">₹{{ number_format($totalVolume, 2) }}</h3>
            <p class="text-[10px] text-slate-400 mt-1">Successfully settled payouts</p>
        </div>

        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Novexapay Revenue (Commission)</span>
            <h3 class="text-2xl font-extrabold text-slate-900 font-display mt-2 text-green-600">₹{{ number_format($totalCommissions, 2) }}</h3>
            <p class="text-[10px] text-slate-400 mt-1">Revenue cut from merchant payouts</p>
        </div>

        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Transaction Success Rate</span>
            @php
                $totalCount = $successCount + $failedCount + $pendingCount;
                $successRate = $totalCount > 0 ? ($successCount / $totalCount) * 100 : 100;
            @endphp
            <h3 class="text-2xl font-extrabold text-slate-900 font-display mt-2">{{ number_format($successRate, 1) }}%</h3>
            <p class="text-[10px] text-slate-400 mt-1">From total {{ $totalCount }} logged API requests</p>
        </div>
    </div>

    <!-- Charts and Tables -->
    <div class="grid lg:grid-cols-12 gap-6">
        <!-- Status Breakdown -->
        <div class="lg:col-span-5 bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-4">
            <h4 class="font-bold text-slate-800 text-sm">Payout Status Breakdown</h4>
            <div class="space-y-4 pt-2">
                <div>
                    <div class="flex justify-between text-xs font-semibold mb-1">
                        <span class="text-slate-500">Success ({{ $successCount }})</span>
                        <span class="text-slate-800">{{ $totalCount > 0 ? number_format(($successCount / $totalCount) * 100, 0) : 0 }}%</span>
                    </div>
                    <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full bg-green-500" style="width: {{ $totalCount > 0 ? ($successCount / $totalCount) * 100 : 0 }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between text-xs font-semibold mb-1">
                        <span class="text-slate-500">Pending ({{ $pendingCount }})</span>
                        <span class="text-slate-800">{{ $totalCount > 0 ? number_format(($pendingCount / $totalCount) * 100, 0) : 0 }}%</span>
                    </div>
                    <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full bg-amber-500" style="width: {{ $totalCount > 0 ? ($pendingCount / $totalCount) * 100 : 0 }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between text-xs font-semibold mb-1">
                        <span class="text-slate-500">Failed ({{ $failedCount }})</span>
                        <span class="text-slate-800">{{ $totalCount > 0 ? number_format(($failedCount / $totalCount) * 100, 0) : 0 }}%</span>
                    </div>
                    <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full bg-red-500" style="width: {{ $totalCount > 0 ? ($failedCount / $totalCount) * 100 : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Merchants -->
        <div class="lg:col-span-7 bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-4">
            <h4 class="font-bold text-slate-800 text-sm">Top Merchants by Processed Volume</h4>
            <div class="overflow-x-auto pt-2">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider text-[9px]">
                            <th class="py-2.5 px-4">Merchant Name</th>
                            <th class="py-2.5 px-4">Payouts Count</th>
                            <th class="py-2.5 px-4 text-right">Processed Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @forelse($merchantRankings as $rank)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="py-3 px-4 font-bold text-slate-900">{{ $rank->merchant->business_name ?? 'Unknown' }}</td>
                                <td class="py-3 px-4 text-slate-500">{{ $rank->tx_count }} txs</td>
                                <td class="py-3 px-4 text-right font-extrabold text-slate-900">₹{{ number_format($rank->total_amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-8 px-4 text-center text-slate-400 font-semibold">No data processed yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
