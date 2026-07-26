@extends('layouts.merchant')
@section('title', 'Credit Card to Bank')
@section('page_title', 'CC to Bank Transfer')

@section('content')
<div class="space-y-8">
    <div class="bg-gradient-to-tr from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 text-white shadow-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div class="space-y-1">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest block">Available Merchant Balance</span>
            <span class="text-4xl font-extrabold text-white font-display">₹{{ number_format($merchant->wallet->balance, 2) }}</span>
        </div>
        <div class="text-xs text-slate-300 space-y-1">
            <p>• Swipe any Credit Card to transfer funds straight into a beneficiary bank account.</p>
            <p>• CC payment processing fee: 3.0% flat. Payout fee applies dynamically.</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-12 gap-8">
        <!-- Transfer form -->
        <div class="lg:col-span-5 bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-6">
            <div>
                <h3 class="font-bold text-slate-900 text-lg">Swipe CC & Transfer</h3>
                <p class="text-xs text-slate-500">Submit credit card swipe details and beneficiary bank details.</p>
            </div>

            @if(session('success'))
                <div class="p-4 bg-green-50 text-green-700 text-xs font-semibold rounded-xl border border-green-100">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 bg-red-50 text-red-700 text-xs font-semibold rounded-xl border border-red-100">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('merchant.cc-to-bank.submit') }}" method="POST" class="space-y-4">
                @csrf
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block border-b border-slate-100 pb-1">1. Credit Card Details</span>
                
                <div class="space-y-1">
                    <label for="amount" class="text-[10px] font-bold text-slate-500 uppercase">Transfer Amount (Min: ₹500)</label>
                    <input type="number" name="amount" id="amount" min="500" placeholder="₹0.00" required
                           class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50 font-bold">
                </div>

                <div class="space-y-1">
                    <label for="card_number" class="text-[10px] font-bold text-slate-500 uppercase">Card Number</label>
                    <input type="text" name="card_number" id="card_number" placeholder="4111 2222 3333 4444" required
                           class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50 font-mono">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label for="card_holder" class="text-[10px] font-bold text-slate-500 uppercase">Card Holder</label>
                        <input type="text" name="card_holder" id="card_holder" placeholder="Holder Name" required
                               class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50">
                    </div>
                    <div class="space-y-1">
                        <label for="expiry" class="text-[10px] font-bold text-slate-500 uppercase">CVV / Expiry</label>
                        <input type="text" placeholder="12/28 | 123" required
                               class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50 font-mono">
                    </div>
                </div>

                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block border-b border-slate-100 pb-1 mt-6">2. Beneficiary Bank Details</span>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label for="bank_name" class="text-[10px] font-bold text-slate-500 uppercase">Bank Name</label>
                        <input type="text" name="bank_name" id="bank_name" placeholder="e.g. HDFC Bank" required
                               class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50">
                    </div>
                    <div class="space-y-1">
                        <label for="ifsc" class="text-[10px] font-bold text-slate-500 uppercase">IFSC Code</label>
                        <input type="text" name="ifsc" id="ifsc" placeholder="e.g. HDFC0001245" required
                               class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50 font-mono">
                    </div>
                </div>

                <div class="space-y-1">
                    <label for="account_number" class="text-[10px] font-bold text-slate-500 uppercase">Account Number</label>
                    <input type="text" name="account_number" id="account_number" placeholder="Beneficiary Account Number" required
                           class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50 font-mono">
                </div>

                <button type="submit" class="w-full h-11 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-lg text-xs shadow-lg shadow-indigo-500/10 transition-colors">
                    Execute CC to Bank Transfer
                </button>
            </form>
        </div>

        <!-- Transfer history -->
        <div class="lg:col-span-7 bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-6">
            <div>
                <h3 class="font-bold text-slate-900 text-lg">CC Transfer Logs</h3>
                <p class="text-xs text-slate-500">Inspecting completed CC to bank transfers.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-bold">
                        <tr>
                            <th class="px-4 py-3">Reference ID</th>
                            <th class="px-4 py-3">Bank Details</th>
                            <th class="px-4 py-3 text-right">Settled Amount</th>
                            <th class="px-4 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-slate-700">
                        @forelse($transfers as $t)
                            <tr>
                                <td class="px-4 py-4 font-mono font-bold text-slate-500">{{ $t->reference_id }}</td>
                                <td class="px-4 py-4">
                                    <div class="font-semibold text-slate-800">{{ $t->holder_name }}</div>
                                    <div class="text-[9px] text-slate-400">{{ $t->bank_name }} ({{ $t->account_number }})</div>
                                </td>
                                <td class="px-4 py-4 text-right font-bold text-slate-900">₹{{ number_format($t->amount, 2) }}</td>
                                <td class="px-4 py-4 text-center">
                                    <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-green-50 text-green-700 border border-green-200">
                                        {{ $t->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-slate-400 font-semibold">No CC transfers recorded.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $transfers->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
