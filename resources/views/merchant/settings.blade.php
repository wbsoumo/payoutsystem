@extends('layouts.merchant')
@section('title', 'General Settings')
@section('page_title', 'Account Settings')

@section('content')
<div class="space-y-8">
    <div class="max-w-2xl bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-6">
        <div>
            <h3 class="font-bold text-slate-900 text-lg">Merchant Entity Settings</h3>
            <p class="text-xs text-slate-500">Configure your business brand name and legal descriptors.</p>
        </div>

        @if(session('success'))
            <div class="p-4 bg-green-50 text-green-700 text-xs font-semibold rounded-xl border border-green-100">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('merchant.settings.update') }}" method="POST" class="space-y-4">
            @csrf
            <div class="space-y-1">
                <label for="business_name" class="text-[10px] font-bold text-slate-500 uppercase">Brand / Business Name</label>
                <input type="text" name="business_name" id="business_name" value="{{ $merchant->business_name }}" required
                       class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50">
            </div>

            <div class="space-y-1">
                <label for="business_type" class="text-[10px] font-bold text-slate-500 uppercase">Business Entity Type</label>
                <select name="business_type" id="business_type" required
                        class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50 font-bold">
                    <option value="sole_proprietorship" {{ $merchant->business_type === 'sole_proprietorship' ? 'selected' : '' }}>Sole Proprietorship</option>
                    <option value="partnership" {{ $merchant->business_type === 'partnership' ? 'selected' : '' }}>Partnership Firm</option>
                    <option value="private_limited" {{ $merchant->business_type === 'private_limited' ? 'selected' : '' }}>Private Limited Company</option>
                    <option value="individual" {{ $merchant->business_type === 'individual' ? 'selected' : '' }}>Individual / Freelancer</option>
                </select>
            </div>

            <button type="submit" class="px-6 h-11 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-lg text-xs shadow-lg shadow-blue-500/10 transition-colors">
                Save Entity Configuration
            </button>
        </form>
    </div>
</div>
@endsection
