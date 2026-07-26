@extends('layouts.public')
@section('title', 'API Documentation')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:lg:px-8 py-12" x-data="{ tab: 'curl', activeSection: 'overview' }">
    <div class="grid lg:grid-cols-12 gap-12">
        <!-- Sidebar Navigation -->
        <aside class="lg:col-span-3 space-y-6">
            <div class="space-y-2">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">API REFERENCE</span>
                <nav class="space-y-1">
                    <button @click="activeSection = 'overview'" :class="activeSection === 'overview' ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'" class="w-full text-left px-3 py-2 text-sm font-semibold rounded-lg transition-colors">Overview</button>
                    <button @click="activeSection = 'auth'" :class="activeSection === 'auth' ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'" class="w-full text-left px-3 py-2 text-sm font-semibold rounded-lg transition-colors">Authentication</button>
                    <button @click="activeSection = 'signatures'" :class="activeSection === 'signatures' ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'" class="w-full text-left px-3 py-2 text-sm font-semibold rounded-lg transition-colors">HMAC Verification</button>
                </nav>
            </div>
            <div class="space-y-2">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">CORE ENDPOINTS</span>
                <nav class="space-y-1">
                    <button @click="activeSection = 'balance'" :class="activeSection === 'balance' ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'" class="w-full text-left px-3 py-2 text-sm font-semibold rounded-lg transition-colors">
                        <span class="inline-block px-1.5 py-0.5 text-[9px] bg-blue-100 text-blue-700 font-bold rounded mr-1.5">GET</span> Get Wallet Balance
                    </button>
                    <button @click="activeSection = 'payout'" :class="activeSection === 'payout' ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'" class="w-full text-left px-3 py-2 text-sm font-semibold rounded-lg transition-colors">
                        <span class="inline-block px-1.5 py-0.5 text-[9px] bg-green-100 text-green-700 font-bold rounded mr-1.5">POST</span> Create Payout
                    </button>
                    <button @click="activeSection = 'status'" :class="activeSection === 'status' ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'" class="w-full text-left px-3 py-2 text-sm font-semibold rounded-lg transition-colors">
                        <span class="inline-block px-1.5 py-0.5 text-[9px] bg-blue-100 text-blue-700 font-bold rounded mr-1.5">GET</span> Get Payout Status
                    </button>
                </nav>
            </div>
        </aside>

        <!-- Main Documentation Panel -->
        <main class="lg:col-span-9 space-y-12">
            
            <!-- OVERVIEW -->
            <div x-show="activeSection === 'overview'" class="space-y-6">
                <div class="space-y-4 border-b border-slate-200 pb-8">
                    <h1 class="text-4xl font-extrabold font-display text-slate-900">API Documentation</h1>
                    <p class="text-slate-600 text-lg leading-relaxed">
                        Welcome to the Novexapay API reference. Our RESTful API exposes endpoints to automate payouts, check wallet balances, and track transactions in real time.
                    </p>
                </div>
                <div class="space-y-2 text-sm text-slate-600 leading-relaxed">
                    <h3 class="font-bold text-slate-900 text-base">Gateway Details</h3>
                    <p>Base URL: <code class="bg-slate-100 px-2 py-0.5 rounded font-mono text-xs text-blue-600">https://taskbazi.xyz/api/v1</code></p>
                    <p>All requests must use JSON bodies and include signature headers to pass the security shield.</p>
                </div>
            </div>

            <!-- AUTHENTICATION -->
            <div x-show="activeSection === 'auth'" class="space-y-6">
                <div>
                    <h2 class="text-3xl font-extrabold font-display text-slate-900">Authentication Headers</h2>
                    <p class="text-slate-500 text-xs mt-1">Include these required HTTP headers in every request.</p>
                </div>
                <div class="overflow-x-auto border border-slate-200 rounded-2xl bg-white shadow-sm">
                    <table class="min-w-full divide-y divide-slate-200 text-left text-xs">
                        <thead class="bg-slate-50 text-slate-500 font-bold">
                            <tr>
                                <th class="px-6 py-3">Header Name</th>
                                <th class="px-6 py-3">Type</th>
                                <th class="px-6 py-3">Description</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 text-slate-700">
                            <tr>
                                <td class="px-6 py-4 font-mono font-bold text-blue-600">x-api-key</td>
                                <td class="px-6 py-4 font-semibold">String</td>
                                <td class="px-6 py-4">Your merchant Public API Key generated inside the Merchant Portal.</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 font-mono font-bold text-blue-600">x-signature</td>
                                <td class="px-6 py-4 font-semibold">String</td>
                                <td class="px-6 py-4">HMAC-SHA256 signature calculated from the request timestamp, nonce, and request body.</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 font-mono font-bold text-blue-600">x-timestamp</td>
                                <td class="px-6 py-4 font-semibold">Integer</td>
                                <td class="px-6 py-4">Current Unix timestamp in seconds. Replayed requests older than 5 minutes will be rejected.</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 font-mono font-bold text-blue-600">x-nonce</td>
                                <td class="px-6 py-4 font-semibold">String</td>
                                <td class="px-6 py-4">A unique UUID or high-entropy random string generated for the request. Used to block replay attempts.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- SIGNATURES -->
            <div x-show="activeSection === 'signatures'" class="space-y-6">
                <div>
                    <h2 class="text-3xl font-extrabold font-display text-slate-900">HMAC Verification Protocol</h2>
                    <p class="text-slate-500 text-xs mt-1">To construct a valid signature:</p>
                </div>
                
                <div class="p-6 bg-slate-50 rounded-2xl border border-slate-100 text-xs space-y-4 text-slate-700">
                    <span class="font-bold text-slate-900 text-sm block">1. Concatenate fields with a dot (.) separator:</span>
                    <pre class="bg-slate-900 text-slate-200 p-4 rounded-xl font-mono text-[10px]">stringToSign = timestamp + "." + nonce + "." + requestBodyJsonString</pre>
                    
                    <span class="font-bold text-slate-900 text-sm block mt-4">2. Calculate SHA256 HMAC using your Secret Key:</span>
                    <pre class="bg-slate-900 text-slate-200 p-4 rounded-xl font-mono text-[10px]">signature = hmacSha256(stringToSign, yourSecretKey)</pre>
                </div>
            </div>

            <!-- BALANCE ENDPOINT -->
            <div x-show="activeSection === 'balance'" class="space-y-6">
                <div>
                    <h2 class="text-3xl font-extrabold font-display text-slate-900">Get Wallet Balance</h2>
                    <p class="text-slate-500 text-xs mt-1">Retrieve current available and frozen balance details.</p>
                </div>

                <div class="grid lg:grid-cols-12 gap-8 items-start">
                    <div class="lg:col-span-6 space-y-4">
                        <div class="flex items-center gap-3">
                            <span class="px-2.5 py-1 bg-blue-50 text-blue-700 rounded-lg text-[10px] font-bold uppercase border border-blue-200">GET</span>
                            <code class="text-xs font-bold font-mono text-slate-800">/wallet/balance</code>
                        </div>
                        
                        <div class="space-y-2">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">SUCCESS RESPONSE (200)</span>
                            <pre class="bg-slate-900 text-slate-200 p-4 rounded-xl font-mono text-[10px] overflow-x-auto">{
  "success": true,
  "balance": 154500.50,
  "frozen_balance": 0.00,
  "currency": "INR"
}</pre>
                        </div>
                    </div>

                    <div class="lg:col-span-6 bg-slate-900 rounded-2xl p-5 text-slate-200">
                        <span class="text-xs font-bold text-slate-400 block uppercase mb-3">cURL Request</span>
                        <pre class="font-mono text-[10px] overflow-x-auto text-slate-300">curl -X GET https://taskbazi.xyz/api/v1/wallet/balance \
  -H "x-api-key: nvx_pk_live_your_key" \
  -H "x-signature: computed_signature" \
  -H "x-timestamp: 1785089201" \
  -H "x-nonce: random_uuid_string"</pre>
                    </div>
                </div>
            </div>

            <!-- PAYOUT ENDPOINT -->
            <div x-show="activeSection === 'payout'" class="space-y-6">
                <div>
                    <h2 class="text-3xl font-extrabold font-display text-slate-900">Create Payout</h2>
                    <p class="text-slate-500 text-xs mt-1">Initiate a transfer to a customer bank account. Deducts from wallet balance.</p>
                </div>

                <div class="grid lg:grid-cols-12 gap-8 items-start">
                    <div class="lg:col-span-6 space-y-4">
                        <div class="flex items-center gap-3">
                            <span class="px-2.5 py-1 bg-green-50 text-green-700 rounded-lg text-[10px] font-bold uppercase border border-green-200">POST</span>
                            <code class="text-xs font-bold font-mono text-slate-800">/payouts</code>
                        </div>

                        <div class="space-y-2">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">JSON PARAMETERS</span>
                            <pre class="bg-slate-900 text-slate-200 p-4 rounded-xl font-mono text-[10px] overflow-x-auto">{
  "client_reference_id": "unique_merchant_payout_id_101",
  "amount": 1500.00,
  "bank_name": "HDFC Bank",
  "bank_account_number": "5010022334455",
  "bank_ifsc": "HDFC0001245",
  "bank_holder_name": "John Doe"
}</pre>
                        </div>
                    </div>

                    <div class="lg:col-span-6 bg-slate-900 rounded-2xl p-5 text-slate-200 space-y-4">
                        <span class="text-xs font-bold text-slate-400 block uppercase">cURL Request</span>
                        <pre class="font-mono text-[10px] overflow-x-auto text-slate-300">curl -X POST https://taskbazi.xyz/api/v1/payouts \
  -H "Content-Type: application/json" \
  -H "x-api-key: nvx_pk_live_your_key" \
  -H "x-signature: computed_signature" \
  -d '{
    "client_reference_id": "ref_101",
    "amount": 1500.00,
    "bank_name": "HDFC Bank",
    "bank_account_number": "5010022334455",
    "bank_ifsc": "HDFC0001245",
    "bank_holder_name": "John Doe"
  }'</pre>
                    </div>
                </div>
            </div>

            <!-- STATUS ENDPOINT -->
            <div x-show="activeSection === 'status'" class="space-y-6">
                <div>
                    <h2 class="text-3xl font-extrabold font-display text-slate-900">Get Payout Status</h2>
                    <p class="text-slate-500 text-xs mt-1">Check status and provider reference details for a specific payout.</p>
                </div>

                <div class="grid lg:grid-cols-12 gap-8 items-start">
                    <div class="lg:col-span-6 space-y-4">
                        <div class="flex items-center gap-3">
                            <span class="px-2.5 py-1 bg-blue-50 text-blue-700 rounded-lg text-[10px] font-bold uppercase border border-blue-200">GET</span>
                            <code class="text-xs font-bold font-mono text-slate-800">/payouts/{reference_id}</code>
                        </div>

                        <div class="space-y-2">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">SUCCESS RESPONSE (200)</span>
                            <pre class="bg-slate-900 text-slate-200 p-4 rounded-xl font-mono text-[10px] overflow-x-auto">{
  "success": true,
  "transaction_id": "tx_abc123xyz",
  "reference_id": "tx_abc123xyz",
  "client_reference_id": "ref_101",
  "amount": 1500.00,
  "status": "success",
  "provider_reference": "PAY202607052228",
  "created_at": "2026-07-26T12:00:00Z"
}</pre>
                        </div>
                    </div>

                    <div class="lg:col-span-6 bg-slate-900 rounded-2xl p-5 text-slate-200">
                        <span class="text-xs font-bold text-slate-400 block uppercase mb-3">cURL Request</span>
                        <pre class="font-mono text-[10px] overflow-x-auto text-slate-300 font-bold text-slate-300">curl -X GET https://taskbazi.xyz/api/v1/payouts/tx_abc123xyz \
  -H "x-api-key: nvx_pk_live_your_key" \
  -H "x-signature: computed_signature"</pre>
                    </div>
                </div>
            </div>

        </main>
    </div>
</div>
@endsection
