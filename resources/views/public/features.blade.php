@extends('layouts.public')
@section('title', 'Platform Features')

@section('content')
<div class="relative overflow-hidden bg-slate-50 py-16 lg:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center space-y-4 mb-16">
            <h1 class="text-4xl font-extrabold font-display text-slate-900 tracking-tight">Platform Core Features</h1>
            <p class="text-slate-600 text-lg max-w-xl mx-auto">
                Discover the enterprise technologies powering Novexapay's merchant portal and transaction processing gateway.
            </p>
        </div>

        <div class="grid md:grid-cols-2 gap-12 items-center mb-20">
            <div class="space-y-6">
                <div class="w-12 h-12 rounded-2xl bg-brand-100 text-brand-600 flex items-center justify-center">
                    <i class="fa-solid fa-server text-xl"></i>
                </div>
                <h2 class="text-3xl font-extrabold font-display text-slate-900">Enterprise API Gateway</h2>
                <p class="text-slate-600 leading-relaxed">
                    Our gateway acts as a secure buffer between your client applications and the upstream providers. We never expose private upstream keys, routing all requests through Laravel's secure controller layers.
                </p>
                <ul class="space-y-2 text-sm text-slate-600 font-semibold">
                    <li><i class="fa-solid fa-circle-check text-green-500 mr-2"></i> HMAC-SHA256 Signatures</li>
                    <li><i class="fa-solid fa-circle-check text-green-500 mr-2"></i> Custom Timestamp Window Checks</li>
                    <li><i class="fa-solid fa-circle-check text-green-500 mr-2"></i> Nonce verification for Replay Protection</li>
                </ul>
            </div>
            <div class="bg-slate-900 rounded-3xl p-6 shadow-xl text-slate-200 font-mono text-xs overflow-x-auto">
                <div class="flex items-center gap-2 mb-4 border-b border-slate-800 pb-3">
                    <div class="w-2.5 h-2.5 rounded-full bg-red-400"></div>
                    <div class="w-2.5 h-2.5 rounded-full bg-yellow-400"></div>
                    <div class="w-2.5 h-2.5 rounded-full bg-green-400"></div>
                    <span class="text-[10px] text-slate-500 font-bold ml-2">GATEWAY HEADERS SECURITY</span>
                </div>
                <pre class="text-slate-400">
x-api-key: nvx_pk_live_3829ad27...
x-signature: 89a8f27dc0281b...
x-timestamp: 1785089201
x-nonce: d038291a-7b29...
                </pre>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-12 items-center">
            <div class="order-last md:order-first bg-white border border-slate-200 rounded-3xl p-8 shadow-lg shadow-slate-100">
                <div class="space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <span class="text-xs font-bold text-slate-500">LEDGER TXN FLOW</span>
                        <span class="text-xs font-bold text-green-500">Atomic Lock Active</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-600">Opening balance:</span>
                        <span class="font-bold text-slate-900">₹1,00,000.00</span>
                    </div>
                    <div class="flex justify-between text-sm text-red-600 font-semibold">
                        <span>Debited payout amount:</span>
                        <span>-₹10,000.00</span>
                    </div>
                    <div class="flex justify-between text-sm text-red-500">
                        <span>Calculated Commission + GST:</span>
                        <span>-₹17.70</span>
                    </div>
                    <div class="flex justify-between text-sm pt-3 border-t border-slate-100">
                        <span class="text-slate-600 font-bold">Closing balance:</span>
                        <span class="font-extrabold text-slate-900">₹89,982.30</span>
                    </div>
                </div>
            </div>
            <div class="space-y-6">
                <div class="w-12 h-12 rounded-2xl bg-brand-100 text-brand-600 flex items-center justify-center">
                    <i class="fa-solid fa-receipt text-xl"></i>
                </div>
                <h2 class="text-3xl font-extrabold font-display text-slate-900">Double-Entry Ledger</h2>
                <p class="text-slate-600 leading-relaxed">
                    Balance movements are locked at the database query level during transaction execution. All transactions calculate commission rates and GST before committing ledger debits.
                </p>
                <ul class="space-y-2 text-sm text-slate-600 font-semibold">
                    <li><i class="fa-solid fa-circle-check text-green-500 mr-2"></i> No negative balances allowed</li>
                    <li><i class="fa-solid fa-circle-check text-green-500 mr-2"></i> Atomic database locks preventing race conditions</li>
                    <li><i class="fa-solid fa-circle-check text-green-500 mr-2"></i> Complete ledger entries for credits/debits</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
