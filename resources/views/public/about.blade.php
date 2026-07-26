@extends('layouts.public')
@section('title', 'About Us')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-16 text-center space-y-8">
    <h1 class="text-4xl font-extrabold font-display text-slate-900">About Novexapay</h1>
    <p class="text-slate-600 text-lg leading-relaxed max-w-2xl mx-auto">
        Novexapay is a high-performance FinTech SaaS product engineered for enterprise merchant wallets, ledger accounting, and secured APIs.
    </p>
    <div class="grid md:grid-cols-2 gap-8 text-left max-w-2xl mx-auto">
        <div class="border border-slate-200 rounded-2xl p-6 bg-white">
            <h3 class="font-bold text-slate-900 mb-2">Our Mission</h3>
            <p class="text-slate-500 text-sm leading-relaxed">
                To deliver lightning-fast payouts, robust double-entry balance validation, and absolute transaction transparency without exposing upstream interfaces.
            </p>
        </div>
        <div class="border border-slate-200 rounded-2xl p-6 bg-white">
            <h3 class="font-bold text-slate-900 mb-2">Our Technology</h3>
            <p class="text-slate-500 text-sm leading-relaxed">
                Built on a modern stack featuring Laravel 12, PHP 8.3/8.5, and strict database query locking to eliminate concurrency threats and transaction race conditions.
            </p>
        </div>
    </div>
</div>
@endsection
