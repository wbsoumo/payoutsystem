@extends('layouts.merchant')
@section('title', 'Wallet Ledger')
@section('page_title', 'Double-Entry Wallet Ledger')

@section('content')
<div class="space-y-6">

    <!-- Header Stats -->
    <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Wallet Balance ledger</h1>
            <p class="text-xs text-slate-500">Every single debit, credit, or frozen transaction is audited here.</p>
        </div>
        <div class="text-right">
            <span class="text-xs text-slate-400 block font-semibold uppercase">TOTAL ACTIVE BALANCE</span>
            <span class="text-3xl font-extrabold text-slate-900 font-display">₹{{ number_format($wallet->balance, 2) }}</span>
        </div>
    </div>

    <!-- Ledger Table -->
    <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-6">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold">
                    <tr>
                        <th class="px-6 py-3">Ledger ID</th>
                        <th class="px-6 py-3">Type</th>
                        <th class="px-6 py-3">Amount</th>
                        <th class="px-6 py-3">Opening Balance</th>
                        <th class="px-6 py-3">Closing Balance</th>
                        <th class="px-6 py-3">Description</th>
                        <th class="px-6 py-3">Timestamp</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-slate-700">
                    @forelse($ledgers as $ledger)
                        <tr>
                            <td class="px-6 py-4 font-mono font-bold text-slate-500">{{ $ledger->id }}</td>
                            <td class="px-6 py-4">
                                @if($ledger->type === 'credit')
                                    <span class="px-2 py-0.5 bg-green-50 text-green-700 border border-green-200 rounded-md font-bold uppercase text-[9px]"><i class="fa-solid fa-arrow-down mr-0.5"></i> Credit</span>
                                @else
                                    <span class="px-2 py-0.5 bg-red-50 text-red-700 border border-red-200 rounded-md font-bold uppercase text-[9px]"><i class="fa-solid fa-arrow-up mr-0.5"></i> Debit</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-extrabold text-slate-950">
                                @if($ledger->type === 'credit')
                                    <span class="text-green-600">+₹{{ number_format($ledger->amount, 2) }}</span>
                                @else
                                    <span class="text-red-600">-₹{{ number_format($ledger->amount, 2) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">₹{{ number_format($ledger->opening_balance, 2) }}</td>
                            <td class="px-6 py-4">₹{{ number_format($ledger->closing_balance, 2) }}</td>
                            <td class="px-6 py-4 font-semibold text-slate-700">{{ $ledger->description }}</td>
                            <td class="px-6 py-4">{{ $ledger->created_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-slate-400 font-semibold">No ledger entries audited.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pt-4 border-t border-slate-100">
            {{ $ledgers->links() }}
        </div>
    </div>

</div>
@endsection
