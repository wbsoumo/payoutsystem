@extends('layouts.public')
@section('title', 'System Status')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-16 space-y-8">
    <div class="text-center space-y-4">
        <h1 class="text-4xl font-extrabold font-display text-slate-900">System Status</h1>
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-green-50 border border-green-200 text-green-700 text-sm font-bold">
            <i class="fa-solid fa-circle-check"></i> All Systems Operational
        </div>
    </div>

    <!-- Status Components -->
    <div class="bg-white border border-slate-200 rounded-3xl p-8 space-y-6 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-800">API Gateway</h3>
                <p class="text-xs text-slate-500">Gateway servers and signature authentication.</p>
            </div>
            <span class="text-xs font-bold text-green-600 bg-green-50 px-3 py-1 rounded-full uppercase">Operational</span>
        </div>
        <div class="h-[1px] bg-slate-100"></div>
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-800">Wallet Ledger System</h3>
                <p class="text-xs text-slate-500">Atomic database logs and double-entry credits/debits.</p>
            </div>
            <span class="text-xs font-bold text-green-600 bg-green-50 px-3 py-1 rounded-full uppercase">Operational</span>
        </div>
        <div class="h-[1px] bg-slate-100"></div>
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-800">Merchant Portal Dashboard</h3>
                <p class="text-xs text-slate-500">Blade layout rendering and authentication sessions.</p>
            </div>
            <span class="text-xs font-bold text-green-600 bg-green-50 px-3 py-1 rounded-full uppercase">Operational</span>
        </div>
    </div>
</div>
@endsection
