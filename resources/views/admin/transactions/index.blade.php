@extends('layouts.admin')
@section('title', 'Transactions Directory')
@section('page_title', 'All Transactions')

@section('content')
<div class="space-y-6">
    <!-- Filter form -->
    <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm">
        <form action="{{ route('admin.transactions') }}" method="GET" class="flex flex-wrap items-end gap-4">
            <div class="space-y-1 flex-1 min-w-[200px]">
                <label for="status" class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Status</label>
                <select name="status" id="status" class="w-full h-11 px-3 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50 font-semibold">
                    <option value="">All Statuses</option>
                    <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>Success</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>

            <div class="space-y-1 flex-1 min-w-[200px]">
                <label for="merchant_id" class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Merchant</label>
                <select name="merchant_id" id="merchant_id" class="w-full h-11 px-3 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50 font-semibold">
                    <option value="">All Merchants</option>
                    @foreach($merchants as $m)
                        <option value="{{ $m->id }}" {{ request('merchant_id') === $m->id ? 'selected' : '' }}>{{ $m->business_name }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="h-11 px-6 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition-all shadow-sm">
                Filter
            </button>
            <a href="{{ route('admin.transactions') }}" class="h-11 px-6 border border-slate-200 hover:bg-slate-50 text-slate-600 rounded-xl text-xs font-bold transition-all flex items-center justify-center">
                Reset
            </a>
        </form>
    </div>

    <!-- Table of Transactions -->
    <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center">
            <h4 class="font-bold text-slate-800 text-sm">Transaction Logs</h4>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider text-[10px]">
                        <th class="py-4 px-6">Reference ID</th>
                        <th class="py-4 px-6">Merchant</th>
                        <th class="py-4 px-6">Amount</th>
                        <th class="py-4 px-6">Fees & Comm.</th>
                        <th class="py-4 px-6">Status</th>
                        <th class="py-4 px-6">Provider</th>
                        <th class="py-4 px-6">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @forelse($transactions as $t)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-4 px-6 font-mono font-bold select-all text-slate-900">{{ $t->reference_id }}</td>
                            <td class="py-4 px-6">{{ $t->merchant->business_name ?? 'N/A' }}</td>
                            <td class="py-4 px-6 text-slate-900 font-bold">₹{{ number_format($t->amount, 2) }}</td>
                            <td class="py-4 px-6">
                                <span class="text-red-500">₹{{ number_format($t->fee, 2) }}</span> / 
                                <span class="text-green-600">₹{{ number_format($t->commission, 2) }}</span>
                            </td>
                            <td class="py-4 px-6">
                                @if($t->status === 'success')
                                    <span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-green-50 text-green-700 border border-green-200 uppercase">Success</span>
                                @elseif($t->status === 'pending')
                                    <span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200 uppercase animate-pulse">Pending</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-red-50 text-red-700 border border-red-200 uppercase">Failed</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-slate-500 uppercase">{{ $t->provider_name ?? 'N/A' }}</td>
                            <td class="py-4 px-6 text-slate-400">{{ $t->created_at->format('M d, H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 px-6 text-center text-slate-400 font-semibold">No transactions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
            <div class="p-6 border-t border-slate-100">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
