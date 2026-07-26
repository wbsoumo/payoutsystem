@extends('layouts.public')
@section('title', 'Request Invite Access')

@section('content')
<div class="relative overflow-hidden bg-slate-50 py-16 lg:py-24">
    <div class="absolute inset-0 bg-[radial-gradient(ellipse_80%_80%_at_50%_-20%,rgba(67,97,238,0.08),rgba(255,255,255,0))]"></div>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="text-center space-y-4 mb-12">
            <h1 class="text-4xl font-extrabold font-display text-slate-900 tracking-tight">Request Access Invite</h1>
            <p class="text-slate-600 text-lg max-w-xl mx-auto">
                Fill out the secure application form below. Our corporate relations team will review your business credentials and volume metrics.
            </p>
        </div>

        @if(session('success'))
            <div class="mb-8 p-6 bg-green-50 border border-green-200 rounded-3xl text-green-800 flex items-start gap-4 shadow-sm">
                <i class="fa-solid fa-circle-check text-green-500 text-xl mt-0.5"></i>
                <div>
                    <h3 class="font-bold text-green-950">Application Submitted</h3>
                    <p class="text-sm mt-1">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <div class="bg-white border border-slate-200 rounded-3xl p-8 md:p-12 shadow-xl shadow-slate-100/50">
            <form action="{{ route('contact.store') }}" method="POST" class="space-y-6">
                @csrf
                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Company Name -->
                    <div class="space-y-1.5">
                        <label for="company_name" class="text-xs font-bold text-slate-700 uppercase tracking-wider">Company Registered Name</label>
                        <input type="text" name="company_name" id="company_name" required value="{{ old('company_name') }}"
                               class="w-full h-12 px-4 rounded-xl border border-slate-200 focus:outline-none focus:border-brand-500 transition-colors bg-slate-50/50">
                        @error('company_name') <span class="text-xs text-red-500 font-medium">{{ $message }}</span> @enderror
                    </div>

                    <!-- Business Name -->
                    <div class="space-y-1.5">
                        <label for="business_name" class="text-xs font-bold text-slate-700 uppercase tracking-wider">Brand / Business Name</label>
                        <input type="text" name="business_name" id="business_name" required value="{{ old('business_name') }}"
                               class="w-full h-12 px-4 rounded-xl border border-slate-200 focus:outline-none focus:border-brand-500 transition-colors bg-slate-50/50">
                        @error('business_name') <span class="text-xs text-red-500 font-medium">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Full Name -->
                    <div class="space-y-1.5">
                        <label for="full_name" class="text-xs font-bold text-slate-700 uppercase tracking-wider">Contact Person Full Name</label>
                        <input type="text" name="full_name" id="full_name" required value="{{ old('full_name') }}"
                               class="w-full h-12 px-4 rounded-xl border border-slate-200 focus:outline-none focus:border-brand-500 transition-colors bg-slate-50/50">
                        @error('full_name') <span class="text-xs text-red-500 font-medium">{{ $message }}</span> @enderror
                    </div>

                    <!-- Email -->
                    <div class="space-y-1.5">
                        <label for="email" class="text-xs font-bold text-slate-700 uppercase tracking-wider">Corporate Email Address</label>
                        <input type="email" name="email" id="email" required value="{{ old('email') }}"
                               class="w-full h-12 px-4 rounded-xl border border-slate-200 focus:outline-none focus:border-brand-500 transition-colors bg-slate-50/50">
                        @error('email') <span class="text-xs text-red-500 font-medium">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Phone -->
                    <div class="space-y-1.5">
                        <label for="phone" class="text-xs font-bold text-slate-700 uppercase tracking-wider">Phone / Mobile Number</label>
                        <input type="text" name="phone" id="phone" required value="{{ old('phone') }}"
                               class="w-full h-12 px-4 rounded-xl border border-slate-200 focus:outline-none focus:border-brand-500 transition-colors bg-slate-50/50">
                        @error('phone') <span class="text-xs text-red-500 font-medium">{{ $message }}</span> @enderror
                    </div>

                    <!-- Country -->
                    <div class="space-y-1.5">
                        <label for="country" class="text-xs font-bold text-slate-700 uppercase tracking-wider">Operating Country</label>
                        <input type="text" name="country" id="country" required value="{{ old('country') ?? 'India' }}"
                               class="w-full h-12 px-4 rounded-xl border border-slate-200 focus:outline-none focus:border-brand-500 transition-colors bg-slate-50/50">
                        @error('country') <span class="text-xs text-red-500 font-medium">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Monthly Volume -->
                    <div class="space-y-1.5">
                        <label for="monthly_volume" class="text-xs font-bold text-slate-700 uppercase tracking-wider">Estimated Monthly Volume</label>
                        <select name="monthly_volume" id="monthly_volume" required
                                class="w-full h-12 px-4 rounded-xl border border-slate-200 focus:outline-none focus:border-brand-500 transition-colors bg-slate-50/50">
                            <option value="">Select volume range</option>
                            <option value="under_10l" {{ old('monthly_volume') === 'under_10l' ? 'selected' : '' }}>Under ₹10 Lakhs</option>
                            <option value="10l_50l" {{ old('monthly_volume') === '10l_50l' ? 'selected' : '' }}>₹10 Lakhs - ₹50 Lakhs</option>
                            <option value="50l_2cr" {{ old('monthly_volume') === '50l_2cr' ? 'selected' : '' }}>₹50 Lakhs - ₹2 Crores</option>
                            <option value="above_2cr" {{ old('monthly_volume') === 'above_2cr' ? 'selected' : '' }}>Above ₹2 Crores</option>
                        </select>
                        @error('monthly_volume') <span class="text-xs text-red-500 font-medium">{{ $message }}</span> @enderror
                    </div>

                    <!-- Business Type -->
                    <div class="space-y-1.5">
                        <label for="business_type" class="text-xs font-bold text-slate-700 uppercase tracking-wider">Entity Type</label>
                        <select name="business_type" id="business_type" required
                                class="w-full h-12 px-4 rounded-xl border border-slate-200 focus:outline-none focus:border-brand-500 transition-colors bg-slate-50/50">
                            <option value="">Select entity type</option>
                            <option value="individual" {{ old('business_type') === 'individual' ? 'selected' : '' }}>Individual</option>
                            <option value="proprietorship" {{ old('business_type') === 'proprietorship' ? 'selected' : '' }}>Sole Proprietorship</option>
                            <option value="partnership" {{ old('business_type') === 'partnership' ? 'selected' : '' }}>Partnership</option>
                            <option value="pvt_ltd" {{ old('business_type') === 'pvt_ltd' ? 'selected' : '' }}>Private Limited Company</option>
                            <option value="public_ltd" {{ old('business_type') === 'public_ltd' ? 'selected' : '' }}>Public Limited Company</option>
                        </select>
                        @error('business_type') <span class="text-xs text-red-500 font-medium">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Website -->
                <div class="space-y-1.5">
                    <label for="website" class="text-xs font-bold text-slate-700 uppercase tracking-wider">Business Website URL</label>
                    <input type="url" name="website" id="website" value="{{ old('website') }}" placeholder="https://example.com"
                           class="w-full h-12 px-4 rounded-xl border border-slate-200 focus:outline-none focus:border-brand-500 transition-colors bg-slate-50/50">
                    @error('website') <span class="text-xs text-red-500 font-medium">{{ $message }}</span> @enderror
                </div>

                <!-- Message -->
                <div class="space-y-1.5">
                    <label for="message" class="text-xs font-bold text-slate-700 uppercase tracking-wider">Briefly Describe Your Business Case</label>
                    <textarea name="message" id="message" rows="4"
                              class="w-full p-4 rounded-xl border border-slate-200 focus:outline-none focus:border-brand-500 transition-colors bg-slate-50/50">{{ old('message') }}</textarea>
                    @error('message') <span class="text-xs text-red-500 font-medium">{{ $message }}</span> @enderror
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full h-14 btn-gradient text-white font-bold rounded-xl shadow-lg shadow-brand-500/20 text-center flex items-center justify-center gap-2">
                        Submit Application <i class="fa-solid fa-paper-plane text-sm"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
