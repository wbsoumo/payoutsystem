@extends('layouts.merchant')
@section('title', 'Disputes')
@section('page_title', 'Chargebacks & Dispute Management')

@section('content')
<div class="space-y-8">
    <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div class="space-y-1">
            <h2 class="text-xl font-bold text-slate-900 font-display">Customer Disputes</h2>
            <p class="text-xs text-slate-500">Respond to customer payment chargebacks. Upload evidence documents to defend transaction authenticity.</p>
        </div>
        <div class="text-xs text-slate-400 font-semibold uppercase">
            Dispute Win Rate: <span class="text-green-600 font-bold">100.0%</span>
        </div>
    </div>

    <!-- Active disputes table -->
    <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-6">
        <div>
            <h3 class="font-bold text-slate-900 text-lg">Chargebacks Ledger</h3>
            <p class="text-xs text-slate-500">Track and respond to disputes raised by payment processors.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold">
                    <tr>
                        <th class="px-4 py-3">Dispute ID</th>
                        <th class="px-4 py-3">Reason</th>
                        <th class="px-4 py-3 text-right">Disputed Amount</th>
                        <th class="px-4 py-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-slate-700">
                    @forelse($disputes as $d)
                        <tr>
                            <td class="px-4 py-4 font-mono font-bold text-slate-500">{{ $d->id }}</td>
                            <td class="px-4 py-4 font-semibold text-slate-800">{{ $d->reason }}</td>
                            <td class="px-4 py-4 text-right font-bold text-slate-900">₹{{ number_format($d->amount, 2) }}</td>
                            <td class="px-4 py-4 text-center">
                                <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-amber-50 text-amber-700 border border-amber-200">
                                    {{ $d->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-slate-400 font-semibold">No active payment disputes found. Your merchant health is excellent!</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $disputes->links() }}
        </div>
    </div>
</div>
@endsection
