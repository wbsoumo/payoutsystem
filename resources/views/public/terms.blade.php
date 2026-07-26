@extends('layouts.public')
@section('title', 'Terms of Service')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-16 space-y-8">
    <h1 class="text-4xl font-extrabold font-display text-slate-900 text-center">Terms of Service</h1>
    <div class="bg-white border border-slate-200 rounded-3xl p-8 md:p-12 space-y-6 text-sm text-slate-600 leading-relaxed shadow-sm">
        <h3 class="font-bold text-slate-900 text-lg">1. Agreement to Terms</h3>
        <p>By using Novexapay's developer APIs or logging into the Merchant Portal, you agree to comply with our transaction policies, security protocols, and operational terms. Accounts found violating transaction rules or performing high-risk payouts will be suspended immediately.</p>
        
        <h3 class="font-bold text-slate-900 text-lg">2. Wallet & Settlement Rules</h3>
        <p>Ledger balances are debited in real-time when payout requests are processed. Transaction commissions are resolved using active default pricing or approved merchant override slabs. GST at 18% is calculated and debited automatically on all commissions.</p>
    </div>
</div>
@endsection
