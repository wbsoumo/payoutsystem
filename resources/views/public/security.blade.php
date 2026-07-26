@extends('layouts.public')
@section('title', 'Security')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-16 text-center space-y-8">
    <h1 class="text-4xl font-extrabold font-display text-slate-900">Security & Cryptography</h1>
    <p class="text-slate-600 text-lg leading-relaxed max-w-xl mx-auto font-medium">
        Enterprise-grade protection with zero exceptions. We execute strict cryptography protocols to protect ledger balances and transaction flows.
    </p>
    <div class="grid md:grid-cols-3 gap-6 text-left">
        <div class="border border-slate-200 rounded-2xl p-6 bg-white space-y-2">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-4"><i class="fa-solid fa-key"></i></div>
            <h3 class="font-bold text-slate-900">Encrypted Secrets</h3>
            <p class="text-slate-500 text-xs leading-relaxed">Merchant secret keys and credentials are encrypted using AES-256-GCM algorithms before persisting to database storage.</p>
        </div>
        <div class="border border-slate-200 rounded-2xl p-6 bg-white space-y-2">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-4"><i class="fa-solid fa-clock-rotate-left"></i></div>
            <h3 class="font-bold text-slate-900">Replay Protection</h3>
            <p class="text-slate-500 text-xs leading-relaxed">Requests enforce short-lived timestamps and cache-validated nonces to protect transaction endpoints from replay attacks.</p>
        </div>
        <div class="border border-slate-200 rounded-2xl p-6 bg-white space-y-2">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-4"><i class="fa-solid fa-shield"></i></div>
            <h3 class="font-bold text-slate-900">IP Whitelisting</h3>
            <p class="text-slate-500 text-xs leading-relaxed">Restrict gateway access. Only requests originating from whitelisted IP addresses are processed; others receive a 403 Forbidden error.</p>
        </div>
    </div>
</div>
@endsection
