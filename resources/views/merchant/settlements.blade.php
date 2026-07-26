@extends('layouts.merchant')
@section('title', 'Settlements')
@section('page_title', 'Settlement Center')

@section('content')
<div class="space-y-8">
    <div class="bg-gradient-to-tr from-slate-800 to-indigo-900 rounded-3xl p-6 text-white shadow-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div class="space-y-1">
            <span class="text-xs font-bold text-slate-300 uppercase tracking-widest block">Settlement Wallet Balance</span>
            <span class="text-4xl font-extrabold text-white font-display">₹{{ number_format($merchant->wallet->balance, 2) }}</span>
        </div>
        <div class="text-xs text-slate-300 space-y-1">
            <p>• Automated settlements clear daily to your primary bank.</p>
            <p>• Manual settlements are processed instantly with a 2% flat gateway fee.</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-12 gap-8">
        <!-- New Settlement Request -->
        <div class="lg:col-span-5 bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-6">
            <div>
                <h3 class="font-bold text-slate-900 text-lg">Request Instant Settlement</h3>
                <p class="text-xs text-slate-500">Transfer funds to your registered company bank account instantly.</p>
            </div>

            @if($errors->any())
                <div class="p-4 bg-red-50 text-red-700 text-xs font-semibold rounded-xl border border-red-100">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('merchant.settlements.request') }}" method="POST" class="space-y-4">
                @csrf
                <div class="space-y-1">
                    <label for="amount" class="text-[10px] font-bold text-slate-500 uppercase">Settlement Amount (Min: ₹100)</label>
                    <input type="number" name="amount" id="amount" min="100" step="0.01" placeholder="₹0.00" required
                           class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50 font-bold">
                </div>

                <div class="p-4 bg-slate-50 rounded-xl space-y-2 text-xs">
                    <span class="font-bold text-slate-700 block">Registered Bank Details:</span>
                    <p class="text-slate-600 font-semibold">Bank: <span class="text-slate-900">{{ $merchant->profile->bank_name ?? 'Not Set' }}</span></p>
                    <p class="text-slate-600 font-semibold">Account: <span class="text-slate-900 font-mono">{{ $merchant->profile->bank_account_number ?? 'Not Set' }}</span></p>
                    <p class="text-slate-600 font-semibold">IFSC: <span class="text-slate-900 font-mono">{{ $merchant->profile->bank_ifsc ?? 'Not Set' }}</span></p>
                </div>

                <button type="submit" class="w-full h-11 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-lg text-xs shadow-lg shadow-blue-500/10 transition-colors">
                    Initiate Instant Settlement
                </button>
            </form>
        </div>

        <!-- Settlement Log -->
        <div class="lg:col-span-7 bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-6">
            <div>
                <h3 class="font-bold text-slate-900 text-lg">Settlement History</h3>
                <p class="text-xs text-slate-500">Log of automated and manual payouts to bank.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-bold">
                        <tr>
                            <th class="px-4 py-3">Reference ID</th>
                            <th class="px-4 py-3 text-right">Amount</th>
                            <th class="px-4 py-3 text-right">Fee (2%)</th>
                            <th class="px-4 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-slate-700">
                        @forelse($settlements as $s)
                            <tr>
                                <td class="px-4 py-4 font-mono font-bold text-slate-500">{{ $s->reference_id }}</td>
                                <td class="px-4 py-4 text-right font-bold text-slate-900">₹{{ number_format($s->amount, 2) }}</td>
                                <td class="px-4 py-4 text-right font-semibold text-slate-400">₹{{ number_format($s->fee, 2) }}</td>
                                <td class="px-4 py-4 text-center">
                                    <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-green-50 text-green-700 border border-green-200">
                                        {{ $s->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-slate-400 font-semibold">No settlements recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $settlements->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
