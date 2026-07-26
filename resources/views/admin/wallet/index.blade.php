@extends('layouts.admin')
@section('title', 'Wallet & Settlement Console')
@section('page_title', 'Wallet Management')

@section('content')
<div class="space-y-8">

    <!-- Overview Grids -->
    <div class="grid md:grid-cols-3 gap-6">
        @foreach($wallets as $w)
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm flex items-center justify-between">
                <div class="space-y-1">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ $w->merchant->business_name }} Wallet</span>
                    <h3 class="text-xl font-extrabold text-slate-900 font-display">₹{{ number_format($w->balance, 2) }}</h3>
                    <p class="text-[10px] text-slate-400">Frozen: ₹{{ number_format($w->frozen_balance, 2) }}</p>
                </div>
                <a href="{{ route('admin.merchants.view', $w->merchant_id) }}" class="p-3 border border-slate-100 hover:bg-slate-50 text-slate-600 rounded-2xl text-xs font-bold transition-all">
                    Adjust Balance
                </a>
            </div>
        @endforeach
    </div>

    <!-- Settlement Requests -->
    <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h4 class="font-bold text-slate-800 text-sm">Settlement Payout Requests</h4>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider text-[10px]">
                        <th class="py-4 px-6">ID</th>
                        <th class="py-4 px-6">Merchant</th>
                        <th class="py-4 px-6">Settlement Amount</th>
                        <th class="py-4 px-6">Status</th>
                        <th class="py-4 px-6 font-center">Actions</th>
                        <th class="py-4 px-6">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @forelse($settlements as $s)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-4 px-6 font-mono select-all">{{ $s->id }}</td>
                            <td class="py-4 px-6 font-bold text-slate-900">{{ $s->merchant->business_name }}</td>
                            <td class="py-4 px-6 text-slate-900 font-extrabold">₹{{ number_format($s->amount, 2) }}</td>
                            <td class="py-4 px-6">
                                @if($s->status === 'approved')
                                    <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-green-50 text-green-700 border border-green-200 uppercase">Approved</span>
                                @elseif($s->status === 'pending')
                                    <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-amber-50 text-amber-700 border border-amber-200 uppercase animate-pulse">Pending</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-red-50 text-red-700 border border-red-200 uppercase">Rejected</span>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                @if($s->status === 'pending')
                                    <div class="flex gap-2">
                                        <form action="{{ route('admin.wallet.settlement.status', $s->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="px-2.5 py-1 bg-green-600 hover:bg-green-500 text-white rounded-lg text-[10px] font-bold uppercase transition-colors">Approve</button>
                                        </form>
                                        <form action="{{ route('admin.wallet.settlement.status', $s->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="px-2.5 py-1 bg-red-600 hover:bg-red-500 text-white rounded-lg text-[10px] font-bold uppercase transition-colors">Reject</button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-slate-400">Processed</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-slate-400">{{ $s->created_at->format('M d, H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 px-6 text-center text-slate-400 font-semibold">No settlements found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Ledger Audit Trail -->
    <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h4 class="font-bold text-slate-800 text-sm">Wallet Ledger Logs</h4>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider text-[10px]">
                        <th class="py-4 px-6">ID</th>
                        <th class="py-4 px-6">Merchant</th>
                        <th class="py-4 px-6">Type</th>
                        <th class="py-4 px-6">Amount</th>
                        <th class="py-4 px-6">Fees & Tax</th>
                        <th class="py-4 px-6">Balances</th>
                        <th class="py-4 px-6">Description</th>
                        <th class="py-4 px-6">Timestamp</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @forelse($ledgers as $l)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-4 px-6 font-mono select-all text-slate-400">{{ substr($l->id, 0, 8) }}...</td>
                            <td class="py-4 px-6 font-bold text-slate-900">{{ $l->wallet->merchant->business_name ?? 'N/A' }}</td>
                            <td class="py-4 px-6 uppercase">
                                @if($l->type === 'credit')
                                    <span class="px-2 py-0.5 rounded text-[8px] font-bold bg-green-50 text-green-700 border border-green-200">Credit</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[8px] font-bold bg-red-50 text-red-700 border border-red-200">Debit</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-slate-900 font-extrabold">₹{{ number_format($l->amount, 2) }}</td>
                            <td class="py-4 px-6 text-red-500">₹{{ number_format($l->fee + $l->gst, 2) }}</td>
                            <td class="py-4 px-6 text-slate-500">₹{{ number_format($l->opening_balance, 2) }} → ₹{{ number_format($l->closing_balance, 2) }}</td>
                            <td class="py-4 px-6 text-slate-600 truncate max-w-[200px]">{{ $l->description }}</td>
                            <td class="py-4 px-6 text-slate-400">{{ $l->created_at->format('M d, H:i:s') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 px-6 text-center text-slate-400 font-semibold">No ledger logs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($ledgers->hasPages())
            <div class="p-6 border-t border-slate-100">
                {{ $ledgers->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
