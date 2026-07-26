@extends('layouts.merchant')
@section('title', 'Manual Payouts')
@section('page_title', 'Payout Hub')

@section('content')
<div class="space-y-8">
    <!-- Top Wallet Header -->
    <div class="bg-slate-900 rounded-3xl p-6 text-white shadow-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div class="space-y-1">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest block">Available Balance</span>
            <span class="text-4xl font-extrabold text-white font-display">₹{{ number_format($merchant->wallet->balance, 2) }}</span>
        </div>
        <div class="text-xs text-slate-400 space-y-1">
            <p>• Fast, secure bank payouts inside India.</p>
            <p>• Payout commissions apply dynamically per transaction tier.</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-12 gap-8">
        <!-- New Payout Form -->
        <div class="lg:col-span-5 bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-6">
            <div>
                <h3 class="font-bold text-slate-900 text-lg">Send New Payout</h3>
                <p class="text-xs text-slate-500">Initiate bank transfer directly from your wallet balance.</p>
            </div>

            @if($errors->any())
                <div class="p-4 bg-red-50 text-red-700 text-xs font-semibold rounded-xl border border-red-100">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('merchant.payouts.submit') }}" method="POST" class="space-y-4">
                @csrf
                <div class="space-y-1">
                    <label for="choose_beneficiary" class="text-[10px] font-bold text-slate-500 uppercase">Choose Saved Beneficiary (Optional)</label>
                    <select id="choose_beneficiary" class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50 font-semibold text-slate-800">
                        <option value="">-- Select Saved Beneficiary --</option>
                        @foreach($beneficiaries as $ben)
                            <option value="{{ $ben->id }}" data-name="{{ $ben->name }}" data-bank="{{ $ben->bank_name }}" data-ifsc="{{ $ben->ifsc }}" data-account="{{ $ben->account_number }}">
                                {{ $ben->name }} ({{ $ben->bank_name }} - {{ substr($ben->account_number, -4) }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-1">
                    <label for="amount" class="text-[10px] font-bold text-slate-500 uppercase">Transfer Amount (INR)</label>
                    <input type="number" name="amount" id="amount" step="0.01" min="1" placeholder="₹0.00" required
                           class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50 font-bold">
                </div>

                <div class="space-y-1">
                    <label for="holder_name" class="text-[10px] font-bold text-slate-500 uppercase">Beneficiary Name</label>
                    <input type="text" name="holder_name" id="holder_name" placeholder="Full Account Holder Name" required
                           class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50">
                </div>

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

                <button type="submit" class="w-full h-11 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-lg text-xs shadow-lg shadow-blue-500/10 transition-colors">
                    Confirm & Send Payout
                </button>
            </form>
        </div>

        <!-- Payout History -->
        <div class="lg:col-span-7 bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-6">
            <div>
                <h3 class="font-bold text-slate-900 text-lg">Recent Payouts</h3>
                <p class="text-xs text-slate-500">Track and inspect your manual bank settlements.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-bold">
                        <tr>
                            <th class="px-4 py-3">Reference ID</th>
                            <th class="px-4 py-3">Beneficiary</th>
                            <th class="px-4 py-3 text-right">Amount</th>
                            <th class="px-4 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-slate-700">
                        @forelse($payouts as $p)
                            <tr>
                                <td class="px-4 py-4 font-mono font-bold text-slate-500">{{ $p->reference_id }}</td>
                                <td class="px-4 py-4">
                                    <div class="font-semibold text-slate-800">{{ $p->holder_name }}</div>
                                    <div class="text-[9px] text-slate-400">{{ $p->bank_name }} ({{ $p->account_number }})</div>
                                </td>
                                <td class="px-4 py-4 text-right font-bold text-slate-900">₹{{ number_format($p->amount, 2) }}</td>
                                <td class="px-4 py-4 text-center">
                                    <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-green-50 text-green-700 border border-green-200">
                                        {{ $p->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-slate-400 font-semibold">No payout transactions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $payouts->links() }}
            </div>
        </div>
    </div>
</div>
<script>
    document.getElementById('choose_beneficiary').addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        if (option.value) {
            document.getElementById('holder_name').value = option.getAttribute('data-name');
            document.getElementById('bank_name').value = option.getAttribute('data-bank');
            document.getElementById('ifsc').value = option.getAttribute('data-ifsc');
            document.getElementById('account_number').value = option.getAttribute('data-account');
        } else {
            document.getElementById('holder_name').value = '';
            document.getElementById('bank_name').value = '';
            document.getElementById('ifsc').value = '';
            document.getElementById('account_number').value = '';
        }
    });
</script>
@endsection
