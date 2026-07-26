@extends('layouts.public')
@section('title', 'Novexapay - Instant Money Transfers & Payout APIs for India')

@section('content')
<!-- Hero Section -->
<section class="relative overflow-hidden bg-white pt-24 pb-28 lg:pt-32 lg:pb-36 border-b border-slate-100">
    <!-- Soft blue gradient background -->
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_80%_20%,rgba(59,130,246,0.06),rgba(255,255,255,0))]"></div>
    <div class="absolute inset-x-0 top-0 h-40 bg-gradient-to-b from-blue-50/20 to-transparent"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="grid lg:grid-cols-12 gap-12 items-center">
            <!-- Hero Text -->
            <div class="lg:col-span-7 space-y-8 text-center lg:text-left">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-50 border border-blue-100 text-blue-600 text-[11px] font-extrabold uppercase tracking-wider">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-ping"></span>
                    Next-Generation Money Transfers for India
                </div>
                
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold font-display text-slate-900 tracking-tight leading-[1.1] sm:leading-none">
                    Business Money Transfers. <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">Built for India.</span>
                </h1>
                
                <p class="text-slate-600 text-base sm:text-lg max-w-xl mx-auto lg:mx-0 leading-relaxed">
                    Automate instant payouts to bank accounts and UPI IDs. Plug our robust money transfer APIs into your application and scale your product with secure Indian banking rails.
                </p>

                <!-- CTA buttons -->
                <div class="flex flex-col sm:flex-row justify-center lg:justify-start gap-4">
                    <a href="{{ route('contact') }}" class="h-14 px-8 bg-blue-600 hover:bg-blue-500 text-white rounded-2xl flex items-center justify-center font-bold shadow-xl shadow-blue-500/20 transition-all">
                        Request Access <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
                    </a>
                    <a href="{{ route('docs') }}" class="h-14 px-8 bg-slate-50 border border-slate-200 hover:bg-slate-100 text-slate-700 rounded-2xl flex items-center justify-center font-bold transition-all">
                        API Documentation
                    </a>
                </div>

                <!-- Small bullet feature items -->
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 pt-6 text-slate-600 max-w-lg mx-auto lg:mx-0 text-xs font-semibold">
                    <div class="flex items-center gap-2"><i class="fa-solid fa-check text-blue-500"></i> Instant IMPS, NEFT, RTGS</div>
                    <div class="flex items-center gap-2"><i class="fa-solid fa-check text-blue-500"></i> UPI & Bulk Payouts</div>
                    <div class="flex items-center gap-2"><i class="fa-solid fa-check text-blue-500"></i> Secure Wallet Infrastructure</div>
                </div>
            </div>

            <!-- Hero Image: Premium FinTech Dashboard mockup -->
            <div class="lg:col-span-5 relative">
                <!-- Decorative absolute background glow -->
                <div class="absolute -inset-4 bg-gradient-to-tr from-blue-500/10 to-indigo-500/10 rounded-[36px] blur-2xl"></div>

                <!-- Product Showcase -->
                <div class="relative bg-white border border-slate-200 rounded-[32px] p-6 shadow-2xl overflow-hidden space-y-6">
                    <!-- Dashboard Header -->
                    <div class="flex justify-between items-center border-b border-slate-100 pb-4">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-slate-200"></span>
                            <span class="w-3 h-3 rounded-full bg-slate-200"></span>
                            <span class="w-3 h-3 rounded-full bg-slate-200"></span>
                        </div>
                        <span class="px-2.5 py-1 text-[10px] font-bold uppercase rounded-md bg-green-50 text-green-700 border border-green-200">Active Node</span>
                    </div>

                    <!-- Wallet & Stats -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-slate-50 border border-slate-100 rounded-2xl p-4 space-y-1">
                            <span class="text-[9px] font-bold text-slate-400 uppercase">LEDGER BALANCE</span>
                            <span class="text-xl font-bold text-slate-900 font-mono block">₹12,45,670.00</span>
                        </div>
                        <div class="bg-slate-50 border border-slate-100 rounded-2xl p-4 space-y-1">
                            <span class="text-[9px] font-bold text-slate-400 uppercase">TRANSFER SUCCESS RATE</span>
                            <span class="text-xl font-bold text-green-600 font-mono block">99.98%</span>
                        </div>
                    </div>

                    <!-- Recent Transactions list -->
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-[10px] font-bold text-slate-400 uppercase">RECENT TRANSFER ENTRIES</span>
                            <span class="text-[10px] font-bold text-blue-600">LIVE FEED</span>
                        </div>

                        <div class="space-y-2">
                            <!-- Success transaction 1 -->
                            <div class="flex items-center justify-between p-3 border border-slate-100 rounded-xl bg-white shadow-sm">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-green-50 text-green-600 flex items-center justify-center text-xs"><i class="fa-solid fa-arrow-down"></i></div>
                                    <div>
                                        <div class="text-[11px] font-bold text-slate-800">Transfer Success</div>
                                        <div class="text-[9px] text-slate-400">IMPS Beneficiary: Rahul S.</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs font-bold text-slate-950">₹5,000.00</div>
                                    <div class="text-[8px] text-slate-400">UTR: 322409...</div>
                                </div>
                            </div>
                            <!-- Success transaction 2 -->
                            <div class="flex items-center justify-between p-3 border border-slate-100 rounded-xl bg-white shadow-sm">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-green-50 text-green-600 flex items-center justify-center text-xs"><i class="fa-solid fa-arrow-down"></i></div>
                                    <div>
                                        <div class="text-[11px] font-bold text-slate-800">Transfer Success</div>
                                        <div class="text-[9px] text-slate-400">UPI Payout: anil@okhdfc</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs font-bold text-slate-950">₹2,500.00</div>
                                    <div class="text-[8px] text-slate-400">UTR: 322410...</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- API Graph mockup -->
                    <div class="space-y-2">
                        <div class="flex justify-between items-center text-[10px] font-bold text-slate-400">
                            <span>API RESPONSES (LAST 24 HOURS)</span>
                            <span class="text-green-500">Normal</span>
                        </div>
                        <div class="h-16 flex items-end justify-between gap-1 pt-2">
                            <div class="w-full bg-slate-100 rounded-t h-12"></div>
                            <div class="w-full bg-slate-100 rounded-t h-8"></div>
                            <div class="w-full bg-slate-100 rounded-t h-10"></div>
                            <div class="w-full bg-slate-100 rounded-t h-14"></div>
                            <div class="w-full bg-blue-500 rounded-t h-16"></div>
                            <div class="w-full bg-blue-500 rounded-t h-12"></div>
                            <div class="w-full bg-blue-500 rounded-t h-14"></div>
                            <div class="w-full bg-blue-500 rounded-t h-16"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Trust Section -->
<section class="py-12 bg-slate-50 border-b border-slate-100 text-xs font-bold uppercase tracking-wider text-slate-400">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-6 gap-6 text-center">
            <div class="space-y-1">
                <i class="fa-solid fa-clock-rotate-left text-blue-500 text-lg block mb-1"></i>
                <span>99.99% Uptime</span>
            </div>
            <div class="space-y-1 border-l border-slate-200">
                <i class="fa-solid fa-shield-halved text-blue-500 text-lg block mb-1"></i>
                <span>256-bit Encryption</span>
            </div>
            <div class="space-y-1 border-l border-slate-200">
                <i class="fa-solid fa-cloud-bolt text-blue-500 text-lg block mb-1"></i>
                <span>API First Architecture</span>
            </div>
            <div class="space-y-1 border-l border-slate-200">
                <i class="fa-solid fa-bolt text-blue-500 text-lg block mb-1"></i>
                <span>Instant Transfers</span>
            </div>
            <div class="space-y-1 border-l border-slate-200">
                <i class="fa-solid fa-building-columns text-blue-500 text-lg block mb-1"></i>
                <span>Indian Rails (IMPS/UPI)</span>
            </div>
            <div class="space-y-1 border-l border-slate-200">
                <i class="fa-solid fa-circle-check text-blue-500 text-lg block mb-1"></i>
                <span>Enterprise Ready</span>
            </div>
        </div>
    </div>
</section>

<!-- Business Showcase Section (Real Unsplash image blended with FinTech visuals) -->
<section class="py-20 bg-white border-b border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-12 gap-12 items-center">
            <div class="lg:col-span-5 space-y-6">
                <span class="text-xs font-extrabold uppercase text-blue-600 tracking-wider">Scale Your Payments</span>
                <h2 class="text-3xl font-extrabold font-display text-slate-900 leading-tight">
                    Powering High-Volume Financial Logistics.
                </h2>
                <p class="text-slate-600 text-sm leading-relaxed">
                    Our platform is crafted for Indian businesses, startups, and enterprises executing frequent payouts. Whether you are settling merchant funds, processing user withdrawals, or paying vendors, Novexapay executes your orders within seconds.
                </p>
                <div class="space-y-3 text-sm font-semibold text-slate-700">
                    <div class="flex items-center gap-3"><i class="fa-solid fa-circle-check text-green-500"></i> Smart routing across multiple bank nodes</div>
                    <div class="flex items-center gap-3"><i class="fa-solid fa-circle-check text-green-500"></i> Full-featured wallet bookkeeping</div>
                    <div class="flex items-center gap-3"><i class="fa-solid fa-circle-check text-green-500"></i> Automated compliance and fraud monitoring</div>
                </div>
            </div>
            <!-- Indian Entrepreneur Unsplash image mockup -->
            <div class="lg:col-span-7 relative">
                <div class="absolute -inset-4 bg-gradient-to-tr from-indigo-500/5 to-blue-500/5 rounded-3xl blur-xl"></div>
                <div class="relative rounded-3xl overflow-hidden border border-slate-200 shadow-xl">
                    <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=1200&q=80" alt="Indian entrepreneur using Novexapay" class="w-full object-cover h-80 sm:h-96">
                    <div class="absolute bottom-6 left-6 right-6 bg-white/90 backdrop-blur-md border border-white/50 rounded-2xl p-4 shadow-lg flex items-center justify-between text-xs">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-lock text-blue-600 text-base"></i>
                            <div>
                                <div class="font-bold text-slate-900">Enterprise Security Active</div>
                                <div class="text-slate-500">All data channels fully encrypted & signed.</div>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 bg-blue-50 text-blue-700 rounded-md font-bold uppercase">SECURED</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Product Features Grid -->
<section class="py-20 bg-slate-50/50 border-b border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        <div class="text-center space-y-2">
            <span class="text-xs font-extrabold uppercase text-blue-600 tracking-wider">Features Suite</span>
            <h2 class="text-3xl font-extrabold font-display text-slate-900">Powerful Infrastructure. Simplified.</h2>
            <p class="text-slate-500 text-sm max-w-lg mx-auto">Everything you need to configure, monitor, and scale merchant money transfers in India.</p>
        </div>

        <div class="grid md:grid-cols-3 gap-6">
            <!-- Feature 1 -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-4">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center"><i class="fa-solid fa-bolt"></i></div>
                <h4 class="font-bold text-slate-950 text-lg">Instant Transfers</h4>
                <p class="text-xs text-slate-500 leading-relaxed">Direct connections to national rails (IMPS, UPI, NEFT, RTGS) ensure your transfers conclude instantly, 24/7.</p>
            </div>
            <!-- Feature 2 -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-4">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center"><i class="fa-solid fa-code"></i></div>
                <h4 class="font-bold text-slate-950 text-lg">Developer APIs</h4>
                <p class="text-xs text-slate-500 leading-relaxed">Robust REST APIs with calculated HMAC signature security headers. Integrate payouts directly into your product workflows.</p>
            </div>
            <!-- Feature 3 -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-4">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center"><i class="fa-solid fa-sliders"></i></div>
                <h4 class="font-bold text-slate-950 text-lg">Commission Slabs</h4>
                <p class="text-xs text-slate-500 leading-relaxed">Setup tier-based routing and commission engines. Calculate flat fees, percentages, and GST splits transparently.</p>
            </div>
            <!-- Feature 4 -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-4">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center"><i class="fa-solid fa-shield"></i></div>
                <h4 class="font-bold text-slate-950 text-lg">IP Whitelisting</h4>
                <p class="text-xs text-slate-500 leading-relaxed">Restrict API integrations to designated public production IP addresses to block unauthorized requests.</p>
            </div>
            <!-- Feature 5 -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-4">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center"><i class="fa-solid fa-receipt"></i></div>
                <h4 class="font-bold text-slate-950 text-lg">Double-Entry Ledger</h4>
                <p class="text-xs text-slate-500 leading-relaxed">Audit-proof accounting log where every payout request generates traceable credit/debit transaction keys.</p>
            </div>
            <!-- Feature 6 -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-4">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center"><i class="fa-solid fa-envelope-open-text"></i></div>
                <h4 class="font-bold text-slate-950 text-lg">Webhook Alerts</h4>
                <p class="text-xs text-slate-500 leading-relaxed">Get real-time JSON webhooks sent straight to your server endpoints on transaction status changes.</p>
            </div>
        </div>
    </div>
</section>

<!-- Developer API Section (Premium Layout matching public.docs) -->
<section class="py-20 bg-slate-950 text-slate-200 border-b border-slate-800" x-data="{ codeTab: 'php' }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        <div class="grid lg:grid-cols-12 gap-12 items-center">
            <!-- Left Info -->
            <div class="lg:col-span-5 space-y-6 text-center lg:text-left">
                <span class="text-xs font-extrabold uppercase text-blue-400 tracking-wider">Built for Developers</span>
                <h2 class="text-3xl font-extrabold font-display text-white leading-tight">
                    API-First Money Transfer Infrastructure
                </h2>
                <p class="text-slate-400 text-sm leading-relaxed">
                    Calculate HMAC signatures using your Secret Key, specify your API Key & Merchant ID headers, and initiate payouts immediately from your application.
                </p>
                <div class="flex justify-center lg:justify-start gap-4">
                    <a href="{{ route('docs') }}" class="px-6 h-11 bg-blue-600 hover:bg-blue-500 text-white rounded-lg flex items-center justify-center text-xs font-bold transition-all">
                        View API Documentation
                    </a>
                </div>
            </div>

            <!-- Right: Code Tabs Box -->
            <div class="lg:col-span-7 bg-slate-900 border border-slate-800 rounded-3xl p-6 space-y-4 overflow-hidden">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <div class="flex gap-2 text-xs">
                        <button @click="codeTab = 'php'" :class="codeTab === 'php' ? 'text-blue-400 font-bold border-b border-blue-400' : 'text-slate-400 hover:text-white'" class="pb-1">PHP</button>
                        <button @click="codeTab = 'node'" :class="codeTab === 'node' ? 'text-blue-400 font-bold border-b border-blue-400' : 'text-slate-400 hover:text-white'" class="pb-1 ml-3">Node.js</button>
                        <button @click="codeTab = 'python'" :class="codeTab === 'python' ? 'text-blue-400 font-bold border-b border-blue-400' : 'text-slate-400 hover:text-white'" class="pb-1 ml-3">Python</button>
                        <button @click="codeTab = 'java'" :class="codeTab === 'java' ? 'text-blue-400 font-bold border-b border-blue-400' : 'text-slate-400 hover:text-white'" class="pb-1 ml-3">Java</button>
                    </div>
                    <span class="text-[10px] font-bold text-slate-500">https://taskbazi.xyz/api/v1/payouts</span>
                </div>

                <!-- PHP snippet -->
                <div x-show="codeTab === 'php'" class="font-mono text-[12.5px] text-slate-300 leading-relaxed overflow-x-auto">
                    <pre>&lt;?php
$apiKey = 'nvx_pk_live_your_key';
$secretKey = 'nvx_sk_live_your_secret';
$merchantId = 'your_merchant_id';
$timestamp = time();
$nonce = bin2hex(random_bytes(16));

$body = json_encode([
    'client_reference_id' => 'ref_101',
    'amount' => 1500.00,
    'bank_name' => 'HDFC Bank',
    'bank_account_number' => '5010022334455',
    'bank_ifsc' => 'HDFC0001245',
    'bank_holder_name' => 'John Doe'
]);

$stringToSign = $timestamp . '.' . $nonce . '.' . $body;
$signature = hash_hmac('sha256', $stringToSign, $secretKey);
?&gt;</pre>
                </div>

                <!-- Node.js snippet -->
                <div x-show="codeTab === 'node'" class="font-mono text-[12.5px] text-slate-300 leading-relaxed overflow-x-auto">
                    <pre>const crypto = require('crypto');
const apiKey = 'nvx_pk_live_your_key';
const secretKey = 'nvx_sk_live_your_secret';
const timestamp = Math.floor(Date.now() / 1000).toString();
const nonce = crypto.randomUUID();

const payload = {
  client_reference_id: "ref_101",
  amount: 1500.00,
  bank_name: "HDFC Bank"
};

const signature = crypto.createHmac('sha256', secretKey)
  .update(`${timestamp}.${nonce}.${JSON.stringify(payload)}`)
  .digest('hex');</pre>
                </div>

                <!-- Python snippet -->
                <div x-show="codeTab === 'python'" class="font-mono text-[12.5px] text-slate-300 leading-relaxed overflow-x-auto">
                    <pre>import time, uuid, hmac, hashlib, json

api_key = 'nvx_pk_live_your_key'
secret_key = 'nvx_sk_live_your_secret'
timestamp = str(int(time.time()))
nonce = str(uuid.uuid4())

payload = {
    "client_reference_id": "ref_101",
    "amount": 1500.00,
    "bank_name": "HDFC Bank"
}

body_str = json.dumps(payload, separators=(',', ':'))
sig = hmac.new(secret_key.encode(), f"{timestamp}.{nonce}.{body_str}".encode(), hashlib.sha256).hexdigest()</pre>
                </div>

                <!-- Java snippet -->
                <div x-show="codeTab === 'java'" class="font-mono text-[12.5px] text-slate-300 leading-relaxed overflow-x-auto">
                    <pre>import javax.crypto.Mac;
import javax.crypto.spec.SecretKeySpec;

public class NovexaPay {
    public static String calculateHMAC(String data, String key) throws Exception {
        SecretKeySpec secretKeySpec = new SecretKeySpec(key.getBytes(), "HmacSHA256");
        Mac mac = Mac.getInstance("HmacSHA256");
        mac.init(secretKeySpec);
        return toHexString(mac.doFinal(data.getBytes()));
    }
}</pre>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call To Action (Large Premium Box) -->
<section class="py-24 bg-white relative overflow-hidden">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_120%,rgba(59,130,246,0.08),rgba(255,255,255,0))]"></div>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative text-center space-y-8">
        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold font-display text-slate-900 tracking-tight leading-tight max-w-3xl mx-auto">
            Ready to Build on India's Next Generation Money Transfer Infrastructure?
        </h2>
        <p class="text-slate-600 text-base max-w-xl mx-auto leading-relaxed">
            Create an account, complete your KYC profile, generate signature API credentials, and start routing automated payouts instantly.
        </p>
        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="{{ route('contact') }}" class="h-14 px-8 bg-blue-600 hover:bg-blue-500 text-white rounded-2xl flex items-center justify-center font-bold shadow-xl shadow-blue-500/20 transition-all">
                Request Invitation Access <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
            </a>
            <a href="{{ route('docs') }}" class="h-14 px-8 bg-slate-50 border border-slate-200 hover:bg-slate-100 text-slate-700 rounded-2xl flex items-center justify-center font-bold transition-all">
                Read API Reference
            </a>
        </div>
    </div>
</section>
@endsection
