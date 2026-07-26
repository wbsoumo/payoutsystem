@extends('layouts.public')
@section('title', 'API Documentation')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12" x-data="{ tab: 'curl', activeSection: 'overview' }">
    <div class="grid lg:grid-cols-12 gap-12">
        <!-- Sidebar Navigation -->
        <aside class="lg:col-span-3 space-y-6">
            <div class="space-y-2">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">API REFERENCE</span>
                <nav class="space-y-1">
                    <button @click="activeSection = 'overview'" :class="activeSection === 'overview' ? 'bg-blue-50 text-blue-600 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'" class="w-full text-left px-3 py-2 text-sm font-semibold rounded-lg transition-colors">Overview</button>
                    <button @click="activeSection = 'auth'" :class="activeSection === 'auth' ? 'bg-blue-50 text-blue-600 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'" class="w-full text-left px-3 py-2 text-sm font-semibold rounded-lg transition-colors">Authentication</button>
                    <button @click="activeSection = 'signatures'" :class="activeSection === 'signatures' ? 'bg-blue-50 text-blue-600 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'" class="w-full text-left px-3 py-2 text-sm font-semibold rounded-lg transition-colors">HMAC Verification</button>
                </nav>
            </div>
            <div class="space-y-2">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">CORE ENDPOINTS</span>
                <nav class="space-y-1">
                    <button @click="activeSection = 'balance'" :class="activeSection === 'balance' ? 'bg-blue-50 text-blue-600 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'" class="w-full text-left px-3 py-2 text-sm font-semibold rounded-lg transition-colors">
                        <span class="inline-block px-1.5 py-0.5 text-[9px] bg-blue-100 text-blue-700 font-bold rounded mr-1.5">GET</span> Get Wallet Balance
                    </button>
                    <button @click="activeSection = 'payout'" :class="activeSection === 'payout' ? 'bg-blue-50 text-blue-600 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'" class="w-full text-left px-3 py-2 text-sm font-semibold rounded-lg transition-colors">
                        <span class="inline-block px-1.5 py-0.5 text-[9px] bg-green-100 text-green-700 font-bold rounded mr-1.5">POST</span> Create Payout
                    </button>
                    <button @click="activeSection = 'status'" :class="activeSection === 'status' ? 'bg-blue-50 text-blue-600 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'" class="w-full text-left px-3 py-2 text-sm font-semibold rounded-lg transition-colors">
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
                                <td class="px-6 py-4 font-mono font-bold text-blue-600">x-merchant-id</td>
                                <td class="px-6 py-4 font-semibold">String</td>
                                <td class="px-6 py-4">Your Merchant Account ID (retrievable from the Merchant Portal developer tab).</td>
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
  -H "x-merchant-id: your_merchant_id" \
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

                <!-- Languages Tabs -->
                <div class="flex flex-wrap gap-2 border-b border-slate-200 pb-2 text-xs">
                    <button @click="tab = 'curl'" :class="tab === 'curl' ? 'border-blue-600 text-blue-600 font-bold border-b-2' : 'text-slate-400 hover:text-slate-600'" class="px-3 py-1.5 focus:outline-none transition-all">cURL</button>
                    <button @click="tab = 'php'" :class="tab === 'php' ? 'border-blue-600 text-blue-600 font-bold border-b-2' : 'text-slate-400 hover:text-slate-600'" class="px-3 py-1.5 focus:outline-none transition-all">PHP</button>
                    <button @click="tab = 'node'" :class="tab === 'node' ? 'border-blue-600 text-blue-600 font-bold border-b-2' : 'text-slate-400 hover:text-slate-600'" class="px-3 py-1.5 focus:outline-none transition-all">Node.js</button>
                    <button @click="tab = 'python'" :class="tab === 'python' ? 'border-blue-600 text-blue-600 font-bold border-b-2' : 'text-slate-400 hover:text-slate-600'" class="px-3 py-1.5 focus:outline-none transition-all">Python</button>
                    <button @click="tab = 'cpp'" :class="tab === 'cpp' ? 'border-blue-600 text-blue-600 font-bold border-b-2' : 'text-slate-400 hover:text-slate-600'" class="px-3 py-1.5 focus:outline-none transition-all">C++</button>
                    <button @click="tab = 'go'" :class="tab === 'go' ? 'border-blue-600 text-blue-600 font-bold border-b-2' : 'text-slate-400 hover:text-slate-600'" class="px-3 py-1.5 focus:outline-none transition-all">Go</button>
                    <button @click="tab = 'java'" :class="tab === 'java' ? 'border-blue-600 text-blue-600 font-bold border-b-2' : 'text-slate-400 hover:text-slate-600'" class="px-3 py-1.5 focus:outline-none transition-all">Java</button>
                    <button @click="tab = 'csharp'" :class="tab === 'csharp' ? 'border-blue-600 text-blue-600 font-bold border-b-2' : 'text-slate-400 hover:text-slate-600'" class="px-3 py-1.5 focus:outline-none transition-all">C#</button>
                    <button @click="tab = 'ruby'" :class="tab === 'ruby' ? 'border-blue-600 text-blue-600 font-bold border-b-2' : 'text-slate-400 hover:text-slate-600'" class="px-3 py-1.5 focus:outline-none transition-all">Ruby</button>
                    <button @click="tab = 'swift'" :class="tab === 'swift' ? 'border-blue-600 text-blue-600 font-bold border-b-2' : 'text-slate-400 hover:text-slate-600'" class="px-3 py-1.5 focus:outline-none transition-all">Swift</button>
                </div>

                <div class="grid lg:grid-cols-12 gap-8 items-start">
                    <div class="lg:col-span-5 space-y-4">
                        <div class="flex items-center gap-3">
                            <span class="px-2.5 py-1 bg-green-50 text-green-700 rounded-lg text-[10px] font-bold uppercase border border-green-200">POST</span>
                            <code class="text-xs font-bold font-mono text-slate-800">/payouts</code>
                        </div>

                        <div class="space-y-2">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">JSON PARAMETERS</span>
                            <pre class="bg-slate-900 text-slate-200 p-4 rounded-xl font-mono text-[10px] overflow-x-auto">{
  "client_reference_id": "unique_ref_101",
  "amount": 1500.00,
  "bank_name": "HDFC Bank",
  "bank_account_number": "5010022334455",
  "bank_ifsc": "HDFC0001245",
  "bank_holder_name": "John Doe"
}</pre>
                        </div>
                    </div>

                    <!-- Code block content -->
                    <div class="lg:col-span-7 bg-slate-900 rounded-2xl p-5 text-slate-200 overflow-hidden min-w-0">
                        <!-- cURL -->
                        <div x-show="tab === 'curl'" class="space-y-2">
                            <span class="text-xs font-bold text-slate-400 block uppercase">cURL Payload</span>
                            <pre class="font-mono text-[10px] overflow-x-auto text-slate-300">curl -X POST https://taskbazi.xyz/api/v1/payouts \
  -H "Content-Type: application/json" \
  -H "x-api-key: nvx_pk_live_your_key" \
  -H "x-merchant-id: your_merchant_id" \
  -H "x-signature: calculated_hmac_signature" \
  -H "x-timestamp: 1785089201" \
  -H "x-nonce: random_uuid_string" \
  -d '{
    "client_reference_id": "unique_ref_101",
    "amount": 1500.00,
    "bank_name": "HDFC Bank",
    "bank_account_number": "5010022334455",
    "bank_ifsc": "HDFC0001245",
    "bank_holder_name": "John Doe"
  }'</pre>
                        </div>

                        <!-- PHP -->
                        <div x-show="tab === 'php'" class="space-y-2">
                            <span class="text-xs font-bold text-slate-400 block uppercase">PHP Snippet</span>
                            <pre class="font-mono text-[10px] overflow-x-auto text-slate-300">&lt;?php
$apiKey = 'nvx_pk_live_your_key';
$secretKey = 'nvx_sk_live_your_secret';
$merchantId = 'your_merchant_id';
$timestamp = time();
$nonce = bin2hex(random_bytes(16));

$body = json_encode([
    'client_reference_id' => 'unique_ref_101',
    'amount' => 1500.00,
    'bank_name' => 'HDFC Bank',
    'bank_account_number' => '5010022334455',
    'bank_ifsc' => 'HDFC0001245',
    'bank_holder_name' => 'John Doe'
]);

$stringToSign = $timestamp . '.' . $nonce . '.' . $body;
$signature = hash_hmac('sha256', $stringToSign, $secretKey);
// Send POST request with x-api-key, x-signature, x-timestamp, x-nonce, x-merchant-id headers
?&gt;</pre>
                        </div>

                        <!-- Node.js -->
                        <div x-show="tab === 'node'" class="space-y-2">
                            <span class="text-xs font-bold text-slate-400 block uppercase">Node.js Snippet</span>
                            <pre class="font-mono text-[10px] overflow-x-auto text-slate-300">const crypto = require('crypto');
const axios = require('axios');

const apiKey = 'nvx_pk_live_your_key';
const secretKey = 'nvx_sk_live_your_secret';
const merchantId = 'your_merchant_id';
const timestamp = Math.floor(Date.now() / 1000).toString();
const nonce = crypto.randomUUID();

const payload = {
  client_reference_id: "unique_ref_101",
  amount: 1500.00,
  bank_name: "HDFC Bank",
  bank_account_number: "5010022334455",
  bank_ifsc: "HDFC0001245",
  bank_holder_name: "John Doe"
};

const bodyString = JSON.stringify(payload);
const signature = crypto
  .createHmac('sha256', secretKey)
  .update(`${timestamp}.${nonce}.${bodyString}`)
  .digest('hex');</pre>
                        </div>

                        <!-- Python -->
                        <div x-show="tab === 'python'" class="space-y-2">
                            <span class="text-xs font-bold text-slate-400 block uppercase">Python 3 Snippet</span>
                            <pre class="font-mono text-[10px] overflow-x-auto text-slate-300">import time, uuid, hmac, hashlib, json, requests

api_key = 'nvx_pk_live_your_key'
secret_key = 'nvx_sk_live_your_secret'
merchant_id = 'your_merchant_id'
timestamp = str(int(time.time()))
nonce = str(uuid.uuid4())

payload = {
    "client_reference_id": "unique_ref_101",
    "amount": 1500.00,
    "bank_name": "HDFC Bank",
    "bank_account_number": "5010022334455",
    "bank_ifsc": "HDFC0001245",
    "bank_holder_name": "John Doe"
}

body_str = json.dumps(payload, separators=(',', ':'))
string_to_sign = f"{timestamp}.{nonce}.{body_str}"
signature = hmac.new(secret_key.encode(), string_to_sign.encode(), hashlib.sha256).hexdigest()</pre>
                        </div>

                        <!-- C++ -->
                        <div x-show="tab === 'cpp'" class="space-y-2">
                            <span class="text-xs font-bold text-slate-400 block uppercase">C++ OpenSSL & CPR Snippet</span>
                            <pre class="font-mono text-[10px] overflow-x-auto text-slate-300">#include &lt;iostream&gt;
#include &lt;sstream&gt;
#include &lt;iomanip&gt;
#include &lt;openssl/hmac.h&gt;
#include &lt;cpr/cpr.h&gt;

std::string compute_hmac(const std::string& key, const std::string& msg) {
    unsigned char hash[32];
    unsigned int len = 32;
    HMAC(EVP_sha256(), key.c_str(), key.length(), (unsigned char*)msg.c_str(), msg.length(), hash, &len);
    std::stringstream ss;
    for(int i = 0; i &lt; 32; i++) {
        ss &lt;&lt; std::hex &lt;&lt; std::setw(2) &lt;&lt; std::setfill('0') &lt;&lt; (int)hash[i];
    }
    return ss.str();
}</pre>
                        </div>

                        <!-- Go -->
                        <div x-show="tab === 'go'" class="space-y-2">
                            <span class="text-xs font-bold text-slate-400 block uppercase">Go Snippet</span>
                            <pre class="font-mono text-[10px] overflow-x-auto text-slate-300">package main
import (
	"crypto/hmac"
	"crypto/sha256"
	"encoding/hex"
	"fmt"
	"time"
)

func main() {
	secret := "nvx_sk_live_your_secret"
	timestamp := fmt.Sprintf("%d", time.Now().Unix())
	nonce := "uuid_or_random_hash"
	body := `{"client_reference_id":"unique_ref_101","amount":1500.00}`

	h := hmac.New(sha256.New, []byte(secret))
	h.Write([]byte(timestamp + "." + nonce + "." + body))
	signature := hex.EncodeToString(h.Sum(nil))
}</pre>
                        </div>

                        <!-- Java -->
                        <div x-show="tab === 'java'" class="space-y-2">
                            <span class="text-xs font-bold text-slate-400 block uppercase">Java okHttp Snippet</span>
                            <pre class="font-mono text-[10px] overflow-x-auto text-slate-300">import javax.crypto.Mac;
import javax.crypto.spec.SecretKeySpec;
import okhttp3.*;

public class NovexaPay {
    public static String calculateHMAC(String data, String key) throws Exception {
        SecretKeySpec secretKeySpec = new SecretKeySpec(key.getBytes(), "HmacSHA256");
        Mac mac = Mac.getInstance("HmacSHA256");
        mac.init(secretKeySpec);
        byte[] rawHmac = mac.doFinal(data.getBytes());
        return hex(rawHmac);
    }
}</pre>
                        </div>

                        <!-- C# -->
                        <div x-show="tab === 'csharp'" class="space-y-2">
                            <span class="text-xs font-bold text-slate-400 block uppercase">C# RestSharp Snippet</span>
                            <pre class="font-mono text-[10px] overflow-x-auto text-slate-300">using System;
using System.Security.Cryptography;
using System.Text;
using RestSharp;

public class NovexaPay {
    public static string CreateHMAC(string message, string secret) {
        byte[] keyByte = Encoding.UTF8.GetBytes(secret);
        byte[] messageBytes = Encoding.UTF8.GetBytes(message);
        using (var hmacsha256 = new HMACSHA256(keyByte)) {
            byte[] hashmessage = hmacsha256.ComputeHash(messageBytes);
            return BitConverter.ToString(hashmessage).Replace("-", "").ToLower();
        }
    }
}</pre>
                        </div>

                        <!-- Ruby -->
                        <div x-show="tab === 'ruby'" class="space-y-2">
                            <span class="text-xs font-bold text-slate-400 block uppercase">Ruby Snippet</span>
                            <pre class="font-mono text-[10px] overflow-x-auto text-slate-300">require 'openssl'
require 'net/http'
require 'json'
require 'securerandom'

secret = "nvx_sk_live_your_secret"
timestamp = Time.now.to_i.to_s
nonce = SecureRandom.uuid
body = { client_reference_id: "unique_ref_101", amount: 1500.00 }.to_json

string_to_sign = "#{timestamp}.#{nonce}.#{body}"
signature = OpenSSL::HMAC.hexdigest('SHA256', secret, string_to_sign)</pre>
                        </div>

                        <!-- Swift -->
                        <div x-show="tab === 'swift'" class="space-y-2">
                            <span class="text-xs font-bold text-slate-400 block uppercase">Swift CryptoKit Snippet</span>
                            <pre class="font-mono text-[10px] overflow-x-auto text-slate-300 font-bold text-slate-300">import Foundation
import CryptoKit

let secret = "nvx_sk_live_your_secret"
let timestamp = String(Int(Date().timeIntervalSince1970))
let nonce = UUID().uuidString
let body = "{\"client_reference_id\":\"unique_ref_101\",\"amount\":1500.00}"

let key = SymmetricKey(data: secret.data(using: .utf8)!)
let code = HMAC&lt;SHA256&gt;.authenticationCode(for: (timestamp + "." + nonce + "." + body).data(using: .utf8)!, using: key)</pre>
                        </div>
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
  -H "x-merchant-id: your_merchant_id" \
  -H "x-signature: computed_signature"</pre>
                    </div>
                </div>
            </div>

        </main>
    </div>
</div>
@endsection
