@extends('layouts.merchant')
@section('title', 'Payment Links')
@section('page_title', 'Payment Collection Links')

@section('content')
<div class="space-y-8">
    <div class="grid lg:grid-cols-12 gap-8">
        <!-- New payment link generator -->
        <div class="lg:col-span-5 bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-6">
            <div>
                <h3 class="font-bold text-slate-900 text-lg">Generate Payment Link</h3>
                <p class="text-xs text-slate-500">Create a shareable link to collect customer invoice payments.</p>
            </div>

            @if(session('success'))
                <div class="p-4 bg-green-50 text-green-700 text-xs font-semibold rounded-xl border border-green-100">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('merchant.payment-links.submit') }}" method="POST" class="space-y-4">
                @csrf
                <div class="space-y-1">
                    <label for="amount" class="text-[10px] font-bold text-slate-500 uppercase">Collection Amount (INR)</label>
                    <input type="number" name="amount" id="amount" step="0.01" min="1" placeholder="₹0.00" required
                           class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50 font-bold">
                </div>

                <div class="space-y-1">
                    <label for="customer_name" class="text-[10px] font-bold text-slate-500 uppercase">Customer Name</label>
                    <input type="text" name="customer_name" id="customer_name" placeholder="John Doe" required
                           class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50">
                </div>

                <div class="space-y-1">
                    <label for="customer_email" class="text-[10px] font-bold text-slate-500 uppercase">Customer Email</label>
                    <input type="email" name="customer_email" id="customer_email" placeholder="customer@example.com" required
                           class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50 font-mono">
                </div>

                <div class="space-y-1">
                    <label for="description" class="text-[10px] font-bold text-slate-500 uppercase">Invoice Reason / Description</label>
                    <input type="text" name="description" id="description" placeholder="e.g. Design consulting invoice" required
                           class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50">
                </div>

                <button type="submit" class="w-full h-11 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-lg text-xs shadow-lg shadow-blue-500/10 transition-colors">
                    Build Shareable Link
                </button>
            </form>
        </div>

        <!-- Links history -->
        <div class="lg:col-span-7 bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-6">
            <div>
                <h3 class="font-bold text-slate-900 text-lg">Generated Links</h3>
                <p class="text-xs text-slate-500">Track paid, pending, or expired payment links.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-bold">
                        <tr>
                            <th class="px-4 py-3">Customer</th>
                            <th class="px-4 py-3 text-right">Amount</th>
                            <th class="px-4 py-3 text-center">Payment URL</th>
                            <th class="px-4 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-slate-700">
                        @forelse($links as $l)
                            <tr>
                                <td class="px-4 py-4">
                                    <div class="font-semibold text-slate-800">{{ $l->customer_name }}</div>
                                    <div class="text-[9px] text-slate-400">{{ $l->customer_email }}</div>
                                </td>
                                <td class="px-4 py-4 text-right font-bold text-slate-900">₹{{ number_format($l->amount, 2) }}</td>
                                <td class="px-4 py-4 text-center font-mono">
                                    <span class="text-blue-600 select-all cursor-pointer font-bold underline" onclick="
                                        navigator.clipboard.writeText('https://taskbazi.xyz/pay/{{ $l->id }}');
                                        alert('Link copied to clipboard!');
                                    ">Copy Pay Link</span>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-amber-50 text-amber-700 border border-amber-200">
                                        {{ $l->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-slate-400 font-semibold">No payment links generated.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $links->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
