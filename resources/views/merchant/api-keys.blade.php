@extends('layouts.merchant')
@section('title', 'API Credentials')
@section('page_title', 'Developer Keys & IP Whitelisting')

@section('content')
<div class="grid lg:grid-cols-12 gap-8" x-data="{ showGenForm: false }">

    <!-- API Keys Column -->
    <div class="lg:col-span-7 space-y-6">
        
        <!-- Active Keys Info -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-6">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-slate-900 text-lg">Active API Credentials</h3>
                    <p class="text-xs text-slate-500">Secure REST API endpoint credentials. Re-generating keys immediately invalidates old ones.</p>
                </div>
                <button @click="showGenForm = !showGenForm" class="btn-gradient text-white px-4 h-10 rounded-xl text-xs font-bold shadow-lg shadow-blue-500/10">
                    <i class="fa-solid fa-arrows-rotate mr-1"></i> Re-generate Keys
                </button>
            </div>

            <!-- Newly Generated credentials output (if session flashes it) -->
            @if(session('api_key'))
                <div class="p-6 bg-blue-50 border border-blue-200 rounded-2xl space-y-4">
                    <div class="text-xs font-bold text-blue-700 uppercase tracking-wider"><i class="fa-solid fa-triangle-exclamation mr-1 text-amber-500"></i> COPY AND STORE SAFELY! THIS WILL NOT BE SHOWN AGAIN.</div>
                    
                    <div class="space-y-3 font-mono text-xs">
                        <div>
                            <span class="text-slate-400 font-bold block text-[10px]">API KEY</span>
                            <div class="flex items-center justify-between p-2 bg-white rounded-lg border border-slate-100 mt-1 select-all font-semibold">
                                <code>{{ session('api_key') }}</code>
                            </div>
                        </div>

                        <div>
                            <span class="text-slate-400 font-bold block text-[10px]">SECRET KEY</span>
                            <div class="flex items-center justify-between p-2 bg-white rounded-lg border border-slate-100 mt-1 select-all font-semibold">
                                <code>{{ session('secret_key') }}</code>
                            </div>
                        </div>

                        <div>
                            <span class="text-slate-400 font-bold block text-[10px]">WEBHOOK SECRET</span>
                            <div class="flex items-center justify-between p-2 bg-white rounded-lg border border-slate-100 mt-1 select-all font-semibold">
                                <code>{{ session('webhook_secret') }}</code>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- List Keys -->
            <div class="space-y-3">
                @forelse($keys as $key)
                    <div class="flex items-center justify-between p-4 bg-slate-50 border border-slate-100 rounded-2xl">
                        <div>
                            <div class="text-xs font-bold text-slate-800">{{ $key->name }}</div>
                            <div class="text-[10px] font-mono text-slate-400 mt-0.5">Key Preview: {{ $key->api_key_preview }}</div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-[10px] font-semibold text-slate-400">Created: {{ $key->created_at->format('Y-m-d') }}</span>
                            @if($key->is_active)
                                <span class="px-2 py-0.5 bg-green-50 text-green-700 border border-green-200 rounded text-[9px] font-bold uppercase">Active</span>
                            @else
                                <span class="px-2 py-0.5 bg-slate-100 text-slate-500 rounded text-[9px] font-bold uppercase">Revoked</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6 text-slate-400 font-semibold text-xs">No active keys. Re-generate to create production API credentials.</div>
                @endforelse
            </div>
        </div>

        <!-- Regenerate Modal/Form (inlined) -->
        <div x-show="showGenForm" class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-4">
            <h3 class="font-bold text-slate-900 text-lg">Confirm Key Re-generation</h3>
            <p class="text-xs text-slate-500 leading-relaxed">
                This requires OTP and Password verification. Regenerating credentials will deactivate all existing API connections immediately.
            </p>

            <form action="{{ route('merchant.api-keys.generate') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid md:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label for="key_name" class="text-[10px] font-bold text-slate-500 uppercase">Key Label</label>
                        <input type="text" name="key_name" id="key_name" value="Production Key" required
                               class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50">
                    </div>
                    <div class="space-y-1">
                        <label for="password" class="text-[10px] font-bold text-slate-500 uppercase">Confirm Login Password</label>
                        <input type="password" name="password" id="password" required placeholder="••••••••"
                               class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50">
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-4 items-end">
                    <div class="space-y-1">
                        <label for="otp_code" class="text-[10px] font-bold text-slate-500 uppercase">Verification OTP (Email OTP)</label>
                        <input type="text" name="otp_code" id="otp_code" placeholder="123456" required
                               class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs text-center font-mono font-bold focus:outline-none focus:border-blue-500 bg-slate-50/50">
                        <span class="text-[9px] text-slate-400 block mt-0.5">Use OTP code: <code class="font-bold">123456</code></span>
                    </div>
                    <button type="submit" class="w-full h-11 bg-red-600 hover:bg-red-500 text-white font-bold rounded-lg text-xs transition-colors shadow-lg shadow-red-500/10">
                        Deactivate Old & Re-generate Keys
                    </button>
                </div>
            </form>
        </div>

    </div>

    <!-- IP Whitelisting Column -->
    <div class="lg:col-span-5 space-y-6">
        
        <!-- Add IP Form -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-4">
            <h3 class="font-bold text-slate-900 text-lg">Whitelist IP Address</h3>
            <p class="text-xs text-slate-500 leading-relaxed">
                If whitelisted IPs are configured, requests originating from non-whitelisted IPs to REST API endpoints are immediately blocked (403 Forbidden).
            </p>

            <form action="{{ route('merchant.api-keys.ip.add') }}" method="POST" class="space-y-4">
                @csrf
                <div class="space-y-1">
                    <label for="ip_address" class="text-[10px] font-bold text-slate-500 uppercase">IP Address</label>
                    <input type="text" name="ip_address" id="ip_address" placeholder="e.g. 198.51.100.1" required
                           class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50 font-mono">
                </div>

                <div class="space-y-1">
                    <label for="description" class="text-[10px] font-bold text-slate-500 uppercase">Description (optional)</label>
                    <input type="text" name="description" id="description" placeholder="e.g. Office Server"
                           class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50">
                </div>

                <button type="submit" class="w-full h-11 btn-gradient text-white text-xs font-bold rounded-lg shadow-lg shadow-brand-500/10">
                    Whitelist IP Address
                </button>
            </form>
        </div>

        <!-- Whitelisted IPs List -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-4">
            <h3 class="font-bold text-slate-900 text-lg">IP Whitelist Rules</h3>
            
            <div class="space-y-3">
                @forelse($whitelists as $ip)
                    <div class="flex items-center justify-between p-3 border border-slate-100 rounded-2xl text-xs bg-slate-50/50">
                        <div>
                            <div class="font-mono font-bold text-slate-800">{{ $ip->ip_address }}</div>
                            @if($ip->description)
                                <div class="text-[10px] text-slate-400 mt-0.5">{{ $ip->description }}</div>
                            @endif
                        </div>
                        <div class="flex items-center gap-3">
                            <form action="{{ route('merchant.api-keys.ip.toggle', $ip->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-[10px] font-bold uppercase transition-colors {{ $ip->is_active ? 'text-green-600 hover:text-green-500' : 'text-slate-400 hover:text-slate-500' }}">
                                    {{ $ip->is_active ? 'Enabled' : 'Disabled' }}
                                </button>
                            </form>
                            
                            <form action="{{ route('merchant.api-keys.ip.delete', $ip->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this IP rule?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-400 text-sm">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6 text-slate-400 font-semibold text-xs">No IP whitelist rules configured. API gateway is open globally.</div>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection
