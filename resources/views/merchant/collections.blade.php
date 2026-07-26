@extends('layouts.merchant')
@section('title', 'Collection Account')
@section('page_title', 'Virtual Collection Account')

@section('content')
<div class="space-y-8">
    <!-- Virtual Account Card -->
    <div class="bg-white border border-slate-200 rounded-3xl p-8 shadow-sm space-y-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div class="space-y-1">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Assigned Collection Partner</span>
                <h2 class="text-2xl font-extrabold text-slate-900 font-display">Virtual Collection Account (CMS)</h2>
                <p class="text-xs text-slate-500">Payments transferred to these bank details will auto-credit your merchant wallet instantly.</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-green-50 text-green-700 border border-green-200 uppercase">
                Active & Listening
            </span>
        </div>

        <div class="h-[1px] bg-slate-100"></div>

        <div class="grid md:grid-cols-4 gap-6 text-xs">
            <div class="p-4 bg-slate-50 rounded-2xl space-y-1">
                <span class="text-slate-400 font-bold block uppercase text-[10px]">Beneficiary Name</span>
                <span class="text-sm font-bold text-slate-800">{{ $virtualAccount['holder_name'] }}</span>
            </div>
            <div class="p-4 bg-slate-50 rounded-2xl space-y-1">
                <span class="text-slate-400 font-bold block uppercase text-[10px]">Bank Name</span>
                <span class="text-sm font-bold text-slate-800">{{ $virtualAccount['bank_name'] }}</span>
            </div>
            <div class="p-4 bg-slate-50 rounded-2xl space-y-1">
                <span class="text-slate-400 font-bold block uppercase text-[10px]">Account Number</span>
                <span class="text-sm font-bold text-slate-800 font-mono tracking-wider">{{ $virtualAccount['account_number'] }}</span>
            </div>
            <div class="p-4 bg-slate-50 rounded-2xl space-y-1">
                <span class="text-slate-400 font-bold block uppercase text-[10px]">IFSC Code</span>
                <span class="text-sm font-bold text-slate-800 font-mono tracking-wider">{{ $virtualAccount['ifsc'] }}</span>
            </div>
        </div>
    </div>

    <!-- Collection Log -->
    <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-6">
        <div>
            <h3 class="font-bold text-slate-900 text-lg">Virtual Collections Log</h3>
            <p class="text-xs text-slate-500">Log of incoming bank transfers and automatic deposits.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold">
                    <tr>
                        <th class="px-4 py-3">Reference ID</th>
                        <th class="px-4 py-3">Sender Name</th>
                        <th class="px-4 py-3 text-right">Deposited Amount</th>
                        <th class="px-4 py-3 text-center">Settled Date</th>
                        <th class="px-4 py-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-slate-700">
                    @forelse($collections as $c)
                        <tr>
                            <td class="px-4 py-4 font-mono font-bold text-slate-500">{{ $c->reference_id }}</td>
                            <td class="px-4 py-4 font-semibold text-slate-800">{{ $c->holder_name ?? 'IMPS / NEFT Sender' }}</td>
                            <td class="px-4 py-4 text-right font-bold text-slate-900">₹{{ number_format($c->amount, 2) }}</td>
                            <td class="px-4 py-4 text-center text-slate-500">{{ $c->created_at->format('Y-m-d H:i:s') }}</td>
                            <td class="px-4 py-4 text-center">
                                <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-green-50 text-green-700 border border-green-200">
                                    {{ $c->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-400 font-semibold">No virtual collections received yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $collections->links() }}
        </div>
    </div>
</div>
@endsection
