@extends('layouts.public')
@section('title', 'Pricing')

@section('content')
<div class="relative overflow-hidden bg-slate-50 py-16 lg:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center space-y-4 mb-16">
            <h1 class="text-4xl font-extrabold font-display text-slate-900 tracking-tight">Simple, Transparent Pricing</h1>
            <p class="text-slate-600 text-lg max-w-xl mx-auto">
                No setup fees. No hidden charges. Only pay for what you process with our industry-leading commission slab rates.
            </p>
        </div>

        <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
            <!-- Plan 1 -->
            <div class="bg-white border border-slate-200 rounded-3xl p-8 shadow-md relative overflow-hidden">
                <div class="space-y-4">
                    <span class="text-xs font-bold text-brand-600 bg-brand-50 px-3 py-1 rounded-full uppercase tracking-wider">STANDARD MERCHANDISING</span>
                    <h3 class="text-2xl font-bold text-slate-900">Standard Payout</h3>
                    <p class="text-slate-500 text-sm">Best for early stage startups and emerging businesses.</p>
                    <div class="h-[1px] bg-slate-100 my-4"></div>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-slate-600">Commission Rate</span>
                            <span class="font-bold text-slate-900">1.80% per transaction</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-slate-600">Minimum Charge</span>
                            <span class="font-bold text-slate-900">₹5.00</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-slate-600">Maximum Charge</span>
                            <span class="font-bold text-slate-900">₹25.00</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-slate-600">GST on Commission</span>
                            <span class="font-bold text-slate-900">18.00%</span>
                        </div>
                    </div>
                    <div class="h-[1px] bg-slate-100 my-4"></div>
                    <a href="{{ route('contact') }}" class="w-full h-12 border border-slate-200 hover:border-brand-500 hover:text-brand-500 rounded-xl flex items-center justify-center font-bold text-slate-700 transition-colors">Request Standard Account</a>
                </div>
            </div>

            <!-- Plan 2 -->
            <div class="bg-white border border-brand-200 rounded-3xl p-8 shadow-xl relative overflow-hidden">
                <div class="absolute top-0 right-0 bg-brand-500 text-white text-[10px] font-bold uppercase tracking-wider px-4 py-1.5 rounded-bl-xl">Custom overrides</div>
                <div class="space-y-4">
                    <span class="text-xs font-bold text-brand-600 bg-brand-50 px-3 py-1 rounded-full uppercase tracking-wider">ENTERPRISE VOLUME</span>
                    <h3 class="text-2xl font-bold text-slate-900">Slab-Based Pricing</h3>
                    <p class="text-slate-500 text-sm">Custom pricing structures for high-volume corporate organizations.</p>
                    <div class="h-[1px] bg-slate-100 my-4"></div>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-slate-600">Slab 1 (₹0 - ₹10,000)</span>
                            <span class="font-bold text-slate-900">₹15.00 Flat Fee</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-slate-600">Slab 2 (₹10,001 - ₹1 Lakh)</span>
                            <span class="font-bold text-slate-900">1.20% Commission</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-slate-600">Slab 3 (Above ₹1 Lakh)</span>
                            <span class="font-bold text-slate-900">0.90% Commission</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-slate-600">GST on Commission</span>
                            <span class="font-bold text-slate-900">18.00%</span>
                        </div>
                    </div>
                    <div class="h-[1px] bg-slate-100 my-4"></div>
                    <a href="{{ route('contact') }}" class="w-full h-12 btn-gradient text-white rounded-xl flex items-center justify-center font-bold shadow-lg shadow-brand-500/10">Request Enterprise Pricing</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
