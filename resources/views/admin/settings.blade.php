@extends('layouts.admin')
@section('title', 'System Settings')
@section('page_title', 'Gateway & System Configurations')

@section('content')
<div class="max-w-2xl bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-6">
    <div>
        <h3 class="font-bold text-slate-900 text-lg">Upstream Provider Settings</h3>
        <p class="text-xs text-slate-500">Configure credentials for Jiopay V1.3.2 Production API integrations.</p>
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-50 text-green-700 text-xs font-semibold rounded-xl border border-green-100">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-4">
        @csrf
        <div class="space-y-1">
            <label for="default_gateway" class="text-[10px] font-bold text-slate-500 uppercase">Active Payout Gateway Route</label>
            <select name="default_gateway" id="default_gateway" required
                    class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50 font-bold">
                <option value="mock" {{ ($settings['default_gateway'] ?? 'mock') === 'mock' ? 'selected' : '' }}>Sandbox Mock Route (Testing)</option>
                <option value="jiopay" {{ ($settings['default_gateway'] ?? '') === 'jiopay' ? 'selected' : '' }}>Jiopay Production API (Live Payouts)</option>
            </select>
        </div>

        <div class="space-y-1">
            <label for="jiopay_mid" class="text-[10px] font-bold text-slate-500 uppercase">Jiopay Merchant ID (bharat_mid)</label>
            <input type="text" name="jiopay_mid" id="jiopay_mid" value="{{ $settings['jiopay_mid'] ?? '' }}" required
                   class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50 font-mono">
        </div>

        <div class="space-y-1">
            <label for="jiopay_key" class="text-[10px] font-bold text-slate-500 uppercase">Jiopay Merchant Key (bharat_key)</label>
            <input type="password" name="jiopay_key" id="jiopay_key" value="{{ $settings['jiopay_key'] ?? '' }}" required
                   class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50 font-mono">
        </div>

        <div class="space-y-1">
            <label for="jiopay_entity_id" class="text-[10px] font-bold text-slate-500 uppercase">Jiopay Entity ID (entityId)</label>
            <input type="text" name="jiopay_entity_id" id="jiopay_entity_id" value="{{ $settings['jiopay_entity_id'] ?? '' }}" required
                   class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50 font-mono">
        </div>

        <div class="space-y-1">
            <label for="jiopay_customer_id" class="text-[10px] font-bold text-slate-500 uppercase">Jiopay Customer ID (customerId)</label>
            <input type="text" name="jiopay_customer_id" id="jiopay_customer_id" value="{{ $settings['jiopay_customer_id'] ?? '' }}" required
                   class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50 font-mono">
        </div>

        <div class="space-y-1">
            <label for="logo_dev_api_key" class="text-[10px] font-bold text-slate-500 uppercase">Logo.dev API Key (token)</label>
            <input type="text" name="logo_dev_api_key" id="logo_dev_api_key" value="{{ $settings['logo_dev_api_key'] ?? '' }}"
                   class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50 font-mono">
        </div>

        <div class="space-y-1">
            <label for="deposit_upi_id" class="text-[10px] font-bold text-slate-500 uppercase">Default Deposit UPI ID</label>
            <input type="text" name="deposit_upi_id" id="deposit_upi_id" value="{{ $settings['deposit_upi_id'] ?? 'novexapay@yesbank' }}" required
                   class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50 font-mono">
        </div>

        <button type="submit" class="px-6 h-11 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-lg text-xs shadow-lg shadow-blue-500/10 transition-colors">
            Update Gateway Configurations
        </button>
    </form>
</div>
@endsection
