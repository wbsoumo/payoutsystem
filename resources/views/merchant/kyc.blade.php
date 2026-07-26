@extends('layouts.merchant')
@section('title', 'KYC Compliance')
@section('page_title', 'KYC Compliance Verification')

@section('content')
<div class="space-y-8" x-data="{ kycStatus: '{{ $merchant->kyc_status }}' }">
    <div class="bg-white border border-slate-200 rounded-3xl p-8 shadow-sm space-y-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div class="space-y-1">
                <span class="text-xs font-bold text-slate-400 uppercase block">Compliance Verification</span>
                <h2 class="text-2xl font-extrabold text-slate-900 font-display">KYC Portal</h2>
                <p class="text-xs text-slate-500">Submit identity credentials to activate full wallet withdrawals and higher transaction limits.</p>
            </div>
            
            <div class="flex items-center gap-2">
                <template x-if="kycStatus === 'pending'">
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200 uppercase">
                        Pending Documents
                    </span>
                </template>
                <template x-if="kycStatus === 'submitted'">
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200 uppercase animate-pulse">
                        In Review
                    </span>
                </template>
                <template x-if="kycStatus === 'approved'">
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-green-50 text-green-700 border border-green-200 uppercase">
                        Verified & Compliant
                    </span>
                </template>
                <template x-if="kycStatus === 'rejected'">
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-red-50 text-red-700 border border-red-200 uppercase">
                        Kyc Rejected
                    </span>
                </template>
            </div>
        </div>

        <div class="h-[1px] bg-slate-100"></div>

        @if(session('success'))
            <div class="p-4 bg-green-50 text-green-700 text-xs font-semibold rounded-xl border border-green-100">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 bg-red-50 text-red-700 text-xs font-semibold rounded-xl border border-red-100">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- Form for KYC submission -->
        <template x-if="kycStatus === 'pending' || kycStatus === 'rejected'">
            <form action="{{ route('merchant.kyc.submit') }}" method="POST" enctype="multipart/form-data" class="space-y-6 max-w-2xl">
                @csrf
                <div class="grid md:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label for="pan" class="text-[10px] font-bold text-slate-500 uppercase">Company / Individual PAN Card</label>
                        <input type="text" name="pan" id="pan" value="{{ $profile->pan ?? '' }}" placeholder="ABCDE1234F" required
                               class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50 font-mono uppercase">
                    </div>
                    <div class="space-y-1">
                        <label for="gst" class="text-[10px] font-bold text-slate-500 uppercase">GSTIN (Optional / Mandatory for firms)</label>
                        <input type="text" name="gst" id="gst" value="{{ $profile->gst ?? '' }}" placeholder="22AAAAA1111A1Z1" required
                               class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50 font-mono uppercase">
                    </div>
                </div>

                <div class="grid md:grid-cols-3 gap-4">
                    <div class="space-y-1">
                        <label for="bank_name" class="text-[10px] font-bold text-slate-500 uppercase">Bank Name</label>
                        <input type="text" name="bank_name" id="bank_name" value="{{ $profile->bank_name ?? '' }}" placeholder="HDFC Bank" required
                               class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50">
                    </div>
                    <div class="space-y-1">
                        <label for="bank_account_number" class="text-[10px] font-bold text-slate-500 uppercase">Account Number</label>
                        <input type="text" name="bank_account_number" id="bank_account_number" value="{{ $profile->bank_account_number ?? '' }}" placeholder="5010022..." required
                               class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50 font-mono">
                    </div>
                    <div class="space-y-1">
                        <label for="bank_ifsc" class="text-[10px] font-bold text-slate-500 uppercase">IFSC Code</label>
                        <input type="text" name="bank_ifsc" id="bank_ifsc" value="{{ $profile->bank_ifsc ?? '' }}" placeholder="HDFC0000001" required
                               class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50 font-mono uppercase">
                    </div>
                </div>

                <div class="space-y-1">
                    <label for="kyc_doc" class="text-[10px] font-bold text-slate-500 uppercase">KYC Document Upload (PAN/COI PDF, JPG or PNG)</label>
                    <input type="file" name="kyc_doc" id="kyc_doc" required
                           class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>

                <button type="submit" class="px-6 h-11 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-lg text-xs shadow-lg shadow-blue-500/10 transition-colors">
                    Submit Compliance Verification
                </button>
            </form>
        </template>

        <!-- Showing documents in review -->
        <template x-if="kycStatus === 'submitted'">
            <div class="p-6 bg-slate-50 rounded-2xl space-y-3 max-w-lg text-xs">
                <i class="fa-regular fa-clock text-4xl text-blue-600 block animate-pulse"></i>
                <h4 class="font-bold text-sm text-slate-800">Compliance Documents Under Review</h4>
                <p class="text-slate-500 leading-relaxed">Our compliance team is auditing your business credentials. Verification reviews usually resolve inside 2 business hours. You will receive an email confirmation once completed.</p>
            </div>
        </template>

        <!-- Showing documents verified -->
        <template x-if="kycStatus === 'approved'">
            <div class="p-6 bg-green-50/50 border border-green-100 rounded-2xl space-y-3 max-w-lg text-xs">
                <i class="fa-solid fa-circle-check text-4xl text-green-600 block"></i>
                <h4 class="font-bold text-sm text-green-800">Verification Active</h4>
                <p class="text-green-700/80 leading-relaxed">Your merchant profile is fully KYC compliant. Account limits and instant automatic bank settlements are enabled.</p>
            </div>
        </template>
    </div>
</div>
@endsection
