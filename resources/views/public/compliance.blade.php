@extends('layouts.public')
@section('title', 'Regulatory Compliance')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-16 text-center space-y-8">
    <h1 class="text-4xl font-extrabold font-display text-slate-900">Regulatory Compliance</h1>
    <p class="text-slate-600 text-lg leading-relaxed max-w-xl mx-auto">
        Adhering strictly to digital payments compliance, KYC verification guidelines, and tax reporting requirements.
    </p>
    <div class="border border-slate-200 rounded-3xl bg-white p-8 text-left max-w-2xl mx-auto space-y-4">
        <h3 class="font-bold text-slate-900 text-lg">Merchant Verification Guidelines</h3>
        <p class="text-slate-500 text-sm leading-relaxed">
            Upon invite approval, merchants are required to submit valid business registration, PAN cards, and Goods and Services Tax (GST) details. Transaction capabilities are held pending compliance review by risk administrators.
        </p>
        <div class="h-[1px] bg-slate-100"></div>
        <h3 class="font-bold text-slate-900 text-lg">Double-Entry Audits</h3>
        <p class="text-slate-500 text-sm leading-relaxed">
            Every movement on merchant balances maps directly to wallet ledger transactions, providing clear ledger exports for annual regulatory auditing and tax filing.
        </p>
    </div>
</div>
@endsection
