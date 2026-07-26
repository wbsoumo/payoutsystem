@extends('layouts.admin')
@section('title', 'Create Merchant')
@section('page_title', 'Create Merchant Account')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Back to Directory link -->
    <div>
        <a href="{{ route('admin.merchants') }}" class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-slate-900 transition-colors font-semibold">
            <i class="fa-solid fa-arrow-left"></i> Back to Merchant Directory
        </a>
    </div>

    <!-- Main Creation Form Card -->
    <div class="bg-white border border-slate-200 rounded-3xl p-8 shadow-sm">
        <div class="border-b border-slate-100 pb-6 mb-8">
            <h1 class="text-xl font-bold text-slate-900">Provision Merchant Entity</h1>
            <p class="text-xs text-slate-500 mt-0.5">Register a brand new merchant corporate entity and configure their first administrator login user.</p>
        </div>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-800 text-xs p-4 rounded-xl mb-6 space-y-1">
                <div class="font-bold">Please correct the following errors:</div>
                <ul class="list-disc pl-4 space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.merchants.store') }}" method="POST" class="space-y-8">
            @csrf

            <!-- Section 1: Business Details -->
            <div class="space-y-4">
                <div class="flex items-center gap-2 border-b border-slate-100 pb-2">
                    <span class="w-5 h-5 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center text-[10px] font-bold">1</span>
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Business Details</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="company_name" class="block text-xs font-bold text-slate-700 mb-1.5">Registered Company Name <span class="text-red-500">*</span></label>
                        <input type="text" name="company_name" id="company_name" value="{{ old('company_name') }}" required
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600/10 focus:border-blue-600 outline-none transition-all placeholder:text-slate-400 font-semibold"
                               placeholder="e.g. Stark Industries Ltd">
                    </div>

                    <div>
                        <label for="business_name" class="block text-xs font-bold text-slate-700 mb-1.5">Brand / Trade Name <span class="text-red-500">*</span></label>
                        <input type="text" name="business_name" id="business_name" value="{{ old('business_name') }}" required
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600/10 focus:border-blue-600 outline-none transition-all placeholder:text-slate-400 font-semibold"
                               placeholder="e.g. Stark Payouts">
                    </div>

                    <div>
                        <label for="business_type" class="block text-xs font-bold text-slate-700 mb-1.5">Business Type <span class="text-red-500">*</span></label>
                        <select name="business_type" id="business_type" required
                                class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600/10 focus:border-blue-600 outline-none transition-all font-semibold">
                            <option value="sole_proprietorship" {{ old('business_type') === 'sole_proprietorship' ? 'selected' : '' }}>Sole Proprietorship</option>
                            <option value="partnership" {{ old('business_type') === 'partnership' ? 'selected' : '' }}>Partnership</option>
                            <option value="private_limited" {{ old('business_type') === 'private_limited' ? 'selected' : '' }}>Private Limited (Pvt Ltd)</option>
                            <option value="public_limited" {{ old('business_type') === 'public_limited' ? 'selected' : '' }}>Public Limited</option>
                            <option value="llp" {{ old('business_type') === 'llp' ? 'selected' : '' }}>Limited Liability Partnership (LLP)</option>
                            <option value="other" {{ old('business_type') === 'other' || !old('business_type') ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-700 mb-1.5">Corporate Email Address <span class="text-red-500">*</span></label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600/10 focus:border-blue-600 outline-none transition-all placeholder:text-slate-400 font-semibold"
                               placeholder="e.g. finance@stark.com">
                    </div>

                    <div>
                        <label for="phone" class="block text-xs font-bold text-slate-700 mb-1.5">Business Phone Number <span class="text-red-500">*</span></label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600/10 focus:border-blue-600 outline-none transition-all placeholder:text-slate-400 font-semibold"
                               placeholder="e.g. +91 9876543210">
                    </div>

                    <div>
                        <label for="website" class="block text-xs font-bold text-slate-700 mb-1.5">Business Website URL</label>
                        <input type="url" name="website" id="website" value="{{ old('website') }}"
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600/10 focus:border-blue-600 outline-none transition-all placeholder:text-slate-400 font-semibold"
                               placeholder="e.g. https://stark.com">
                    </div>
                </div>
            </div>

            <!-- Section 2: Administrator User Credentials -->
            <div class="space-y-4">
                <div class="flex items-center gap-2 border-b border-slate-100 pb-2">
                    <span class="w-5 h-5 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center text-[10px] font-bold">2</span>
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Primary Admin Credentials</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="user_name" class="block text-xs font-bold text-slate-700 mb-1.5">Administrator Full Name <span class="text-red-500">*</span></label>
                        <input type="text" name="user_name" id="user_name" value="{{ old('user_name') }}" required
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600/10 focus:border-blue-600 outline-none transition-all placeholder:text-slate-400 font-semibold"
                               placeholder="e.g. Tony Stark">
                    </div>

                    <div>
                        <label for="user_email" class="block text-xs font-bold text-slate-700 mb-1.5">Login Email Address <span class="text-red-500">*</span></label>
                        <input type="email" name="user_email" id="user_email" value="{{ old('user_email') }}" required
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600/10 focus:border-blue-600 outline-none transition-all placeholder:text-slate-400 font-semibold"
                               placeholder="e.g. tony@stark.com">
                    </div>

                    <div>
                        <label for="user_password" class="block text-xs font-bold text-slate-700 mb-1.5">Account Login Password <span class="text-red-500">*</span></label>
                        <input type="password" name="user_password" id="user_password" required
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600/10 focus:border-blue-600 outline-none transition-all placeholder:text-slate-400 font-semibold"
                               placeholder="Minimum 8 characters">
                    </div>
                </div>
            </div>

            <!-- Submission Area -->
            <div class="flex justify-end gap-3 pt-6 border-t border-slate-100">
                <a href="{{ route('admin.merchants') }}" 
                   class="px-5 py-2.5 border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold rounded-xl text-xs transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs transition-colors shadow-sm">
                    Provision Merchant
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
