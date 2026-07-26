@extends('layouts.public')
@section('title', 'Enterprise FinTech Merchant Portal & Payouts Engine')

@section('content')
<!-- Hero Section -->
<section class="relative overflow-hidden bg-slate-50 pt-16 pb-20 lg:pt-24 lg:pb-28">
    <div class="absolute inset-0 bg-[radial-gradient(ellipse_80%_80%_at_50%_-20%,rgba(67,97,238,0.12),rgba(255,255,255,0))]"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="grid lg:grid-cols-12 gap-12 items-center">
            <!-- Hero Text -->
            <div class="lg:col-span-7 space-y-8 text-center lg:text-left">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-brand-50 border border-brand-100 text-brand-600 text-xs font-semibold uppercase tracking-wider">
                    <i class="fa-solid fa-shield-halved text-brand-500"></i> Enterprise Payouts & Wallet System
                </div>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold font-display text-slate-900 tracking-tight leading-none">
                    High-Performance <br class="hidden sm:inline">
                    <span class="text-brand-500">FinTech Ledger</span> & Payouts Portal.
                </h1>
                <p class="text-lg text-slate-600 max-w-xl mx-auto lg:mx-0">
                    A premium, modular SaaS architecture offering instant merchant wallet management, double-entry ledger audits, dynamic commission slabs, and enterprise-grade HMAC signature APIs.
                </p>
                <div class="flex flex-col sm:flex-row justify-center lg:justify-start gap-4">
                    <a href="{{ route('contact') }}" class="btn-gradient text-white px-8 h-14 rounded-xl flex items-center justify-center font-semibold shadow-xl shadow-brand-500/20">
                        Request Invite Access <i class="fa-solid fa-arrow-right ml-2 text-sm"></i>
                    </a>
                    <a href="{{ route('docs') }}" class="bg-white border border-slate-200 hover:border-slate-300 text-slate-700 px-8 h-14 rounded-xl flex items-center justify-center font-semibold transition-all">
                        Explore Developer APIs
                    </a>
                </div>
                <!-- Mini Stats -->
                <div class="grid grid-cols-3 gap-6 pt-6 max-w-md mx-auto lg:mx-0">
                    <div class="text-left">
                        <div class="text-2xl font-bold text-slate-900">99.99%</div>
                        <div class="text-xs text-slate-500 font-medium">Uptime Guarantee</div>
                    </div>
                    <div class="text-left border-l border-slate-200 pl-6">
                        <div class="text-2xl font-bold text-slate-900">&lt; 80ms</div>
                        <div class="text-xs text-slate-500 font-medium">API Response Time</div>
                    </div>
                    <div class="text-left border-l border-slate-200 pl-6">
                        <div class="text-2xl font-bold text-slate-900">100%</div>
                        <div class="text-xs text-slate-500 font-medium">UUID & Nonce Secured</div>
                    </div>
                </div>
            </div>

            <!-- Hero Graphics (Mocking a premium Stripe-like dashboard card) -->
            <div class="lg:col-span-5 relative">
                <div class="relative bg-white border border-slate-200 rounded-3xl p-6 shadow-2xl shadow-slate-200/50 overflow-hidden">
                    <div class="flex justify-between items-center mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 rounded-full bg-red-400"></div>
                            <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                            <div class="w-3 h-3 rounded-full bg-green-400"></div>
                        </div>
                        <span class="text-xs font-semibold text-brand-500 bg-brand-50 px-2.5 py-1 rounded-md">Live Environment</span>
                    </div>

                    <div class="space-y-4">
                        <div class="border border-slate-100 rounded-2xl p-4 bg-slate-50/50">
                            <span class="text-xs text-slate-500 font-semibold block mb-1">WALLET LEDGER BALANCE</span>
                            <span class="text-3xl font-extrabold text-slate-900 font-display">₹14,89,250.45</span>
                            <span class="text-xs text-green-500 font-medium block mt-1"><i class="fa-solid fa-arrow-trend-up"></i> +12.4% vs last week</span>
                        </div>

                        <!-- Mini Transactions Mock -->
                        <div class="space-y-2">
                            <span class="text-xs text-slate-400 font-bold tracking-wider uppercase block">LATEST TRANSACTIONS</span>
                            <div class="flex items-center justify-between p-3 bg-white border border-slate-100 rounded-xl shadow-sm">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center text-green-600"><i class="fa-solid fa-arrow-down-long"></i></div>
                                    <div>
                                        <div class="text-xs font-bold text-slate-800">Txn-3021f-8291</div>
                                        <div class="text-[10px] text-slate-400">Merchant Payin - Success</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs font-extrabold text-slate-900">+₹50,000.00</div>
                                    <div class="text-[10px] text-slate-400">Fee: ₹15.00</div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between p-3 bg-white border border-slate-100 rounded-xl shadow-sm">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600"><i class="fa-solid fa-arrow-up-long"></i></div>
                                    <div>
                                        <div class="text-xs font-bold text-slate-800">Txn-401de-3912</div>
                                        <div class="text-[10px] text-slate-400">Client Payout - Success</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs font-extrabold text-slate-900">-₹2,50,000.00</div>
                                    <div class="text-[10px] text-slate-400">Fee: ₹25.00</div>
                                </div>
                            </div>
                        </div>

                        <!-- Signature verified tag -->
                        <div class="flex items-center gap-2 text-xs text-slate-500 justify-center border-t border-slate-100 pt-4">
                            <i class="fa-solid fa-lock text-green-500"></i>
                            <span>HMAC SHA-256 Signature Verified</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Grid Section -->
<section class="py-20 bg-white border-y border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto space-y-4 mb-16">
            <h2 class="text-3xl font-extrabold font-display text-slate-900 sm:text-4xl">Enterprise-Grade Features</h2>
            <p class="text-lg text-slate-600">Novexapay provides a secure and scalable wallet system and developer toolkit, designed specifically to scale to thousands of transactions per second.</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div class="border border-slate-100 rounded-3xl p-8 bg-slate-50/50 hover:bg-white hover:shadow-xl transition-all duration-300">
                <div class="w-12 h-12 rounded-2xl bg-brand-100 text-brand-600 flex items-center justify-center mb-6">
                    <i class="fa-solid fa-wallet text-xl"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">Double-Entry Wallet Ledger</h3>
                <p class="text-slate-600 text-sm leading-relaxed">
                    Double-entry bookkeeping is enforced database-wide. Balance updates are locked query-level, protecting against concurrent transactions or debit race conditions.
                </p>
            </div>

            <!-- Feature 2 -->
            <div class="border border-slate-100 rounded-3xl p-8 bg-slate-50/50 hover:bg-white hover:shadow-xl transition-all duration-300">
                <div class="w-12 h-12 rounded-2xl bg-green-100 text-green-600 flex items-center justify-center mb-6">
                    <i class="fa-solid fa-signature text-xl"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">HMAC Signed REST APIs</h3>
                <p class="text-slate-600 text-sm leading-relaxed">
                    No upstream exposure. All request payloads are signed using private merchant secret keys, validated with unique nonces, dynamic timestamps, and whitelisted IPs.
                </p>
            </div>

            <!-- Feature 3 -->
            <div class="border border-slate-100 rounded-3xl p-8 bg-slate-50/50 hover:bg-white hover:shadow-xl transition-all duration-300">
                <div class="w-12 h-12 rounded-2xl bg-purple-100 text-purple-600 flex items-center justify-center mb-6">
                    <i class="fa-solid fa-sliders text-xl"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">Commission Slab Engine</h3>
                <p class="text-slate-600 text-sm leading-relaxed">
                    Admin can create global default commissions or write merchant-specific slab overrides. Supports flat charges, percentage fees, and tax calculations automatically.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action Section -->
<section class="py-20 bg-slate-900 text-white relative overflow-hidden">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_bottom_left,rgba(67,97,238,0.2),rgba(255,255,255,0))]"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative text-center space-y-8">
        <h2 class="text-3xl font-extrabold font-display sm:text-5xl max-w-3xl mx-auto">
            Ready to integrate enterprise-grade payouts?
        </h2>
        <p class="text-slate-400 text-lg max-w-xl mx-auto">
            Submit your business application details today. Our risk compliance team will evaluate your volume requirements and set up your merchant wallet.
        </p>
        <div class="flex justify-center gap-4">
            <a href="{{ route('contact') }}" class="btn-gradient text-white px-8 h-14 rounded-xl flex items-center justify-center font-semibold shadow-xl shadow-brand-500/20">
                Submit Request Access Form
            </a>
        </div>
    </div>
</section>
@endsection
