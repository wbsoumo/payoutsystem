@extends('layouts.merchant')
@section('title', 'API Credentials & Docs')
@section('page_title', 'Developer Integration Hub')

@section('content')
<div class="space-y-8" x-data="{ activeTab: 'keys' }">
    <!-- Tab headers -->
    <div class="border-b border-slate-200">
        <div class="flex gap-6 -mb-px">
            <button @click="activeTab = 'keys'" 
                    :class="activeTab === 'keys' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-400 hover:text-slate-600'"
                    class="pb-4 border-b-2 font-bold text-sm transition-all focus:outline-none">
                API Access Keys & Whitelists
            </button>
            <button @click="activeTab = 'docs'" 
                    :class="activeTab === 'docs' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-400 hover:text-slate-600'"
                    class="pb-4 border-b-2 font-bold text-sm transition-all focus:outline-none">
                Integration Reference & Code Snippets
            </button>
        </div>
    </div>

    <!-- API Keys Tab -->
    <div x-show="activeTab === 'keys'" class="space-y-8">
        <!-- Merchant ID card -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-3">
            <div>
                <h3 class="font-bold text-slate-900 text-lg">Merchant Account Details</h3>
                <p class="text-xs text-slate-500">Provide this Merchant ID along with your API credentials to authorize requests.</p>
            </div>
            <div class="p-4 border border-slate-100 rounded-2xl bg-slate-50/50 flex justify-between items-center text-xs">
                <div>
                    <span class="text-slate-400 block text-[9px] uppercase font-bold">Merchant ID (x-merchant-id)</span>
                    <span class="text-slate-800 font-mono font-bold text-base select-all">{{ $merchant->id }}</span>
                </div>
                <button onclick="navigator.clipboard.writeText('{{ $merchant->id }}'); alert('Merchant ID copied!');" 
                        class="px-4 py-2 border border-slate-200 hover:border-slate-300 hover:bg-slate-50 font-bold rounded-lg transition-colors">
                    <i class="fa-regular fa-copy mr-1"></i> Copy
                </button>
            </div>
        </div>

        <!-- API Credentials Card -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-6">
            <div>
                <h3 class="font-bold text-slate-900 text-lg">API Secrets</h3>
                <p class="text-xs text-slate-500">Authentication credentials used for authorizing API connection requests.</p>
            </div>

            <div class="space-y-4">
                @forelse($keys as $key)
                    <div class="p-4 border border-slate-100 rounded-2xl bg-slate-50/50 space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-bold text-slate-700 uppercase">{{ ucfirst($key->environment) }} Environment Key</span>
                            <span class="px-2 py-0.5 rounded text-[8px] font-bold uppercase bg-green-50 text-green-700 border border-green-200">Active</span>
                        </div>
                        <div class="grid md:grid-cols-2 gap-4 text-xs font-semibold">
                            <div>
                                <span class="text-slate-400 block text-[9px] uppercase font-bold">Public Key (Client ID)</span>
                                <span class="text-slate-800 font-mono select-all">{{ $key->public_key }}</span>
                            </div>
                            <div x-data="{ showSecret: false }">
                                <span class="text-slate-400 block text-[9px] uppercase font-bold">Secret Key (Client Secret)</span>
                                <div class="flex items-center gap-2">
                                    <span class="text-slate-800 font-mono select-all truncate" x-text="showSecret ? '{{ $key->secret_key }}' : '••••••••••••••••••••••••••••••••'"></span>
                                    <button @click="showSecret = !showSecret" class="text-slate-400 hover:text-slate-600"><i class="fa-solid" :class="showSecret ? 'fa-eye-slash' : 'fa-eye'"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6 text-slate-400 font-semibold text-xs">No keys generated. Click below to initialize your credentials.</div>
                @endforelse

                <form action="{{ route('merchant.api-keys.generate') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 h-10 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl text-xs transition-colors shadow-sm">
                        Generate New Production Keys
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Code Docs Tab -->
    <div x-show="activeTab === 'docs'" class="space-y-6">
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-6">
            <div>
                <h3 class="font-bold text-slate-900 text-lg">Quick-Start Integration</h3>
                <p class="text-xs text-slate-500">Authenticate requests by passing your Public Key and computed HMAC Signature in the headers.</p>
            </div>

            <!-- Integration Code Snippets -->
            <div class="space-y-4 text-xs">
                <div class="space-y-1">
                    <span class="font-bold text-slate-700 block">cURL Command Example:</span>
                    <pre class="bg-slate-900 text-slate-200 p-4 rounded-xl font-mono overflow-x-auto text-[10px]">
curl -X POST https://taskbazi.xyz/api/v1/payout \
  -H "Content-Type: application/json" \
  -H "X-API-KEY: nvx_pk_live_your_public_key" \
  -H "X-SIGNATURE: computed_sha256_hmac_signature" \
  -d '{
    "amount": 5000.00,
    "bank_name": "HDFC Bank",
    "account_number": "5010022334455",
    "ifsc": "HDFC0000001",
    "holder_name": "John Doe"
  }'</pre>
                </div>

                <div class="space-y-1">
                    <span class="font-bold text-slate-700 block">PHP HMAC Signature Calculation:</span>
                    <pre class="bg-slate-900 text-slate-200 p-4 rounded-xl font-mono overflow-x-auto text-[10px]">
&lt;?php
$payload = json_encode([
    'amount' => 5000.00,
    'bank_name' => 'HDFC Bank',
    'account_number' => '5010022334455',
    'ifsc' => 'HDFC0000001',
    'holder_name' => 'John Doe'
]);

$secretKey = 'nvx_sk_live_your_secret_key';
$signature = hash_hmac('sha256', $payload, $secretKey);
?&gt;</pre>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
