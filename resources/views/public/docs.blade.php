@extends('layouts.public')
@section('title', 'API Documentation')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12" x-data="{ tab: 'curl' }">
    <div class="grid lg:grid-cols-12 gap-12">
        <!-- Sidebar Navigation -->
        <aside class="lg:col-span-3 space-y-6">
            <div class="space-y-2">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">API REFERENCE</span>
                <nav class="space-y-1">
                    <a href="#" class="block px-3 py-2 text-sm font-semibold rounded-lg bg-brand-50 text-brand-600">Overview</a>
                    <a href="#" class="block px-3 py-2 text-sm font-semibold rounded-lg text-slate-600 hover:bg-slate-50 hover:text-slate-900">Authentication</a>
                    <a href="#" class="block px-3 py-2 text-sm font-semibold rounded-lg text-slate-600 hover:bg-slate-50 hover:text-slate-900">Signatures</a>
                    <a href="#" class="block px-3 py-2 text-sm font-semibold rounded-lg text-slate-600 hover:bg-slate-50 hover:text-slate-900">Errors</a>
                </nav>
            </div>
            <div class="space-y-2">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">CORE ENDPOINTS</span>
                <nav class="space-y-1">
                    <a href="#" class="block px-3 py-2 text-sm font-semibold rounded-lg text-slate-600 hover:bg-slate-50 hover:text-slate-900"><span class="inline-block px-1.5 py-0.5 text-[10px] bg-green-100 text-green-700 font-bold rounded mr-1.5">POST</span> Create Payout</a>
                    <a href="#" class="block px-3 py-2 text-sm font-semibold rounded-lg text-slate-600 hover:bg-slate-50 hover:text-slate-900"><span class="inline-block px-1.5 py-0.5 text-[10px] bg-blue-100 text-blue-700 font-bold rounded mr-1.5">GET</span> Get Payout</a>
                    <a href="#" class="block px-3 py-2 text-sm font-semibold rounded-lg text-slate-600 hover:bg-slate-50 hover:text-slate-900"><span class="inline-block px-1.5 py-0.5 text-[10px] bg-blue-100 text-blue-700 font-bold rounded mr-1.5">GET</span> Wallet Balance</a>
                </nav>
            </div>
        </aside>

        <!-- Main Documentation Panel -->
        <main class="lg:col-span-9 space-y-12">
            <!-- Title -->
            <div class="space-y-4 border-b border-slate-200 pb-8">
                <h1 class="text-4xl font-extrabold font-display text-slate-900">API Reference & Authentication</h1>
                <p class="text-slate-600 text-lg leading-relaxed">
                    Novexapay provides a standard REST API endpoint. To ensure security, all requests must be signed with an HMAC-SHA256 signature using your secret key. Upstream APIs are protected behind the backend gateway.
                </p>
            </div>

            <!-- Signature verification explanation -->
            <div class="space-y-4">
                <h2 class="text-2xl font-bold text-slate-900">HMAC Signature Protocol</h2>
                <p class="text-slate-600 text-sm leading-relaxed">
                    All HTTP API requests must include the following custom headers. Requests without valid signatures, or requests containing replayed nonces, will be rejected with <code class="bg-slate-100 px-1 py-0.5 rounded text-red-500 font-mono text-xs">401 Unauthorized</code> or <code class="bg-slate-100 px-1 py-0.5 rounded text-red-500 font-mono text-xs">400 Bad Request</code>.
                </p>
                <div class="overflow-x-auto border border-slate-200 rounded-2xl bg-white shadow-sm">
                    <table class="min-w-full divide-y divide-slate-200 text-left text-xs">
                        <thead class="bg-slate-50 text-slate-500 font-bold">
                            <tr>
                                <th class="px-6 py-3">Header</th>
                                <th class="px-6 py-3">Description</th>
                                <th class="px-6 py-3">Example</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 text-slate-700">
                            <tr>
                                <td class="px-6 py-4 font-mono font-bold text-brand-600">x-api-key</td>
                                <td class="px-6 py-4">Your public API Key generated on the merchant settings page.</td>
                                <td class="px-6 py-4 font-mono">nvx_pk_live_aBcDeF12345...</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 font-mono font-bold text-brand-600">x-signature</td>
                                <td class="px-6 py-4">HMAC-SHA256 signature of request body concatenated with timestamp and nonce.</td>
                                <td class="px-6 py-4 font-mono">9f8e7d6c5b4a3...</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 font-mono font-bold text-brand-600">x-timestamp</td>
                                <td class="px-6 py-4">Current Unix timestamp in seconds. Rejects if older than 300 seconds.</td>
                                <td class="px-6 py-4 font-mono">1785089201</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 font-mono font-bold text-brand-600">x-nonce</td>
                                <td class="px-6 py-4">A unique, single-use random string (UUID or hash) to prevent replay attacks.</td>
                                <td class="px-6 py-4 font-mono">8f9b1c20-72bd...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Endpoint Detail & Code Examples -->
            <div class="space-y-6">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">Create Payout</h2>
                    <p class="text-slate-600 text-sm mt-1">Initiate a payout to a bank account or credit card. Funds are debited from your ledger wallet balance.</p>
                </div>

                <div class="grid lg:grid-cols-12 gap-8 items-start">
                    <!-- Request Details -->
                    <div class="lg:col-span-6 space-y-4">
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 bg-green-50 text-green-700 rounded-lg text-xs font-extrabold uppercase border border-green-200">POST</span>
                            <code class="text-sm font-bold font-mono text-slate-800">/api/v1/payouts</code>
                        </div>

                        <div class="space-y-2">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">JSON REQUEST BODY</span>
                            <pre class="bg-slate-900 text-slate-200 p-4 rounded-xl font-mono text-xs overflow-x-auto">{
  "client_reference_id": "req_pay_1001",
  "amount": 2500.00,
  "bank_name": "ICICI Bank",
  "bank_account_number": "10020030040",
  "bank_ifsc": "ICIC0001234",
  "bank_holder_name": "Raju Kumar"
}</pre>
                        </div>
                    </div>

                    <!-- Code snippet tabs -->
                    <div class="lg:col-span-6 space-y-4 bg-slate-900 rounded-2xl p-6 text-slate-200 shadow-xl shadow-slate-950/20">
                        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">INTEGRATION SNIPPET</span>
                            <div class="flex gap-2 text-xs">
                                <button @click="tab = 'curl'" :class="tab === 'curl' ? 'text-brand-400 font-bold' : 'text-slate-400 hover:text-white'" class="transition-colors">cURL</button>
                                <button @click="tab = 'php'" :class="tab === 'php' ? 'text-brand-400 font-bold' : 'text-slate-400 hover:text-white'" class="transition-colors">PHP</button>
                                <button @click="tab = 'js'" :class="tab === 'js' ? 'text-brand-400 font-bold' : 'text-slate-400 hover:text-white'" class="transition-colors">NodeJS</button>
                                <button @click="tab = 'python'" :class="tab === 'python' ? 'text-brand-400 font-bold' : 'text-slate-400 hover:text-white'" class="transition-colors">Python</button>
                            </div>
                        </div>

                        <!-- cURL Code -->
                        <div x-show="tab === 'curl'" class="font-mono text-xs leading-relaxed overflow-x-auto space-y-2">
                            <p class="text-slate-500"># Send using terminal cURL</p>
                            <pre class="text-slate-300">curl -X POST https://novexapay.com/api/v1/payouts \
  -H "Content-Type: application/json" \
  -H "x-api-key: nvx_pk_live_your_key_here" \
  -H "x-signature: calculated_hmac_signature" \
  -H "x-timestamp: 1785089201" \
  -H "x-nonce: random_uuid_string" \
  -d '{
    "client_reference_id": "req_pay_1001",
    "amount": 2500.00,
    "bank_name": "ICICI Bank",
    "bank_account_number": "10020030040",
    "bank_ifsc": "ICIC0001234",
    "bank_holder_name": "Raju Kumar"
  }'</pre>
                        </div>

                        <!-- PHP Code -->
                        <div x-show="tab === 'php'" class="font-mono text-xs leading-relaxed overflow-x-auto space-y-2">
                            <p class="text-slate-500">&lt;?php // Calculate HMAC signature & request</p>
                            <pre class="text-slate-300">$apiKey = 'nvx_pk_live_your_key_here';
$secretKey = 'nvx_sk_live_your_secret_here';
$timestamp = time();
$nonce = bin2hex(random_bytes(16));

$body = json_encode([
    'client_reference_id' => 'req_pay_1001',
    'amount' => 2500.00,
    'bank_name' => 'ICICI Bank',
    'bank_account_number' => '10020030040',
    'bank_ifsc' => 'ICIC0001234',
    'bank_holder_name' => 'Raju Kumar'
]);

$stringToSign = $timestamp . '.' . $nonce . '.' . $body;
$signature = hash_hmac('sha256', $stringToSign, $secretKey);

// Send using cURL or Guzzle...</pre>
                        </div>

                        <!-- JS Code -->
                        <div x-show="tab === 'js'" class="font-mono text-xs leading-relaxed overflow-x-auto space-y-2">
                            <p class="text-slate-500">// NodeJS integration example</p>
                            <pre class="text-slate-300">const crypto = require('crypto');
const axios = require('axios');

const apiKey = 'nvx_pk_live_your_key_here';
const secretKey = 'nvx_sk_live_your_secret_here';
const timestamp = Math.floor(Date.now() / 1000).toString();
const nonce = crypto.randomUUID();

const payload = {
    client_reference_id: "req_pay_1001",
    amount: 2500.00,
    bank_name: "ICICI Bank",
    bank_account_number: "10020030040",
    bank_ifsc: "ICIC0001234",
    bank_holder_name: "Raju Kumar"
};

const bodyString = JSON.stringify(payload);
const stringToSign = `${timestamp}.${nonce}.${bodyString}`;
const signature = crypto
  .createHmac('sha256', secretKey)
  .update(stringToSign)
  .digest('hex');

// Axios request configuration...</pre>
                        </div>

                        <!-- Python Code -->
                        <div x-show="tab === 'python'" class="font-mono text-xs leading-relaxed overflow-x-auto space-y-2">
                            <p class="text-slate-500"># Python 3 integration example</p>
                            <pre class="text-slate-300">import time
import uuid
import hmac
import hashlib
import json
import requests

api_key = 'nvx_pk_live_your_key_here'
secret_key = 'nvx_sk_live_your_secret_here'
timestamp = str(int(time.time()))
nonce = str(uuid.uuid4())

payload = {
    "client_reference_id": "req_pay_1001",
    "amount": 2500.00,
    "bank_name": "ICICI Bank",
    "bank_account_number": "10020030040",
    "bank_ifsc": "ICIC0001234",
    "bank_holder_name": "Raju Kumar"
}

body_str = json.dumps(payload, separators=(',', ':'))
string_to_sign = f"{timestamp}.{nonce}.{body_str}"
signature = hmac.new(
    secret_key.encode('utf-8'),
    string_to_sign.encode('utf-8'),
    hashlib.sha256
).hexdigest()

# Send requests.post()...</pre>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection
