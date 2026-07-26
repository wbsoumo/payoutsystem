@extends('layouts.merchant')
@section('title', 'Profile Settings')
@section('page_title', 'Business Details & KYC')

@section('content')
<div class="grid lg:grid-cols-12 gap-8">

    <!-- Profile Settings Forms -->
    <div class="lg:col-span-8 space-y-6">
        
        <!-- Details form -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 md:p-8 shadow-sm space-y-6">
            <div>
                <h3 class="font-bold text-slate-900 text-lg">Business Entity details</h3>
                <p class="text-xs text-slate-500">Provide official identity numbers and address parameters to finalize KYC checks.</p>
            </div>

            <form action="{{ route('merchant.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Section: IDs -->
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="space-y-1">
                        <label for="gst" class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">GSTIN Number</label>
                        <input type="text" name="gst" id="gst" value="{{ old('gst', $profile->gst) }}" placeholder="e.g. 27AAAAA1111A1Z1"
                               class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50 uppercase font-mono">
                    </div>
                    <div class="space-y-1">
                        <label for="pan" class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">PAN Number</label>
                        <input type="text" name="pan" id="pan" value="{{ old('pan', $profile->pan) }}" placeholder="e.g. ABCDE1234F"
                               class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50 uppercase font-mono">
                    </div>
                </div>

                <!-- Section: Bank Account -->
                <div class="bg-slate-50/50 border border-slate-100 rounded-2xl p-6 space-y-4">
                    <span class="text-xs font-bold text-slate-700 uppercase tracking-wider block">Official Bank Details</span>
                    
                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label for="bank_name" class="text-[10px] font-bold text-slate-500 uppercase">Bank Name</label>
                            <input type="text" name="bank_name" id="bank_name" value="{{ old('bank_name', $profile->bank_name) }}" placeholder="e.g. State Bank of India"
                                   class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-white">
                        </div>
                        <div class="space-y-1">
                            <label for="bank_holder_name" class="text-[10px] font-bold text-slate-500 uppercase">Account Holder Name</label>
                            <input type="text" name="bank_holder_name" id="bank_holder_name" value="{{ old('bank_holder_name', $profile->bank_holder_name) }}"
                                   class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-white">
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label for="bank_account_number" class="text-[10px] font-bold text-slate-500 uppercase">Account Number</label>
                            <input type="text" name="bank_account_number" id="bank_account_number" value="{{ old('bank_account_number', $profile->bank_account_number) }}"
                                   class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-white font-mono">
                        </div>
                        <div class="space-y-1">
                            <label for="bank_ifsc" class="text-[10px] font-bold text-slate-500 uppercase">IFSC Code</label>
                            <input type="text" name="bank_ifsc" id="bank_ifsc" value="{{ old('bank_ifsc', $profile->bank_ifsc) }}" placeholder="e.g. SBIN0001234"
                                   class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-white uppercase font-mono">
                        </div>
                    </div>
                </div>

                <!-- Section: Address -->
                <div class="space-y-4">
                    <span class="text-xs font-bold text-slate-700 uppercase tracking-wider block">Physical Business Address</span>
                    
                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label for="address_line1" class="text-[10px] font-bold text-slate-500 uppercase">Address Line 1</label>
                            <input type="text" name="address_line1" id="address_line1" value="{{ old('address_line1', $profile->address_line1) }}"
                                   class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50">
                        </div>
                        <div class="space-y-1">
                            <label for="address_line2" class="text-[10px] font-bold text-slate-500 uppercase">Address Line 2 (optional)</label>
                            <input type="text" name="address_line2" id="address_line2" value="{{ old('address_line2', $profile->address_line2) }}"
                                   class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50">
                        </div>
                    </div>

                    <div class="grid md:grid-cols-3 gap-4">
                        <div class="space-y-1">
                            <label for="city" class="text-[10px] font-bold text-slate-500 uppercase">City</label>
                            <input type="text" name="city" id="city" value="{{ old('city', $profile->city) }}"
                                   class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50">
                        </div>
                        <div class="space-y-1">
                            <label for="state" class="text-[10px] font-bold text-slate-500 uppercase">State</label>
                            <input type="text" name="state" id="state" value="{{ old('state', $profile->state) }}"
                                   class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50">
                        </div>
                        <div class="space-y-1">
                            <label for="postal_code" class="text-[10px] font-bold text-slate-500 uppercase">Postal Code</label>
                            <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $profile->postal_code) }}"
                                   class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50">
                        </div>
                    </div>
                </div>

                <!-- Section: KYC PDF Document & Image Uploads -->
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="space-y-1">
                        <label for="kyc_document" class="text-[10px] font-bold text-slate-500 uppercase block">KYC Proof (Business Registration / Incorporation PDF)</label>
                        <input type="file" name="kyc_document" id="kyc_document"
                               class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        @if($profile->kyc_document_path)
                            <span class="text-[10px] text-slate-400 block mt-1"><i class="fa-solid fa-file-pdf text-red-500 mr-1"></i> Document Uploaded: <a href="{{ asset('storage/' . $profile->kyc_document_path) }}" target="_blank" class="text-blue-600 hover:underline font-bold">View Document</a></span>
                        @endif
                    </div>

                    <div class="space-y-1">
                        <label for="profile_image" class="text-[10px] font-bold text-slate-500 uppercase block">Logo / Profile Image</label>
                        <input type="file" name="profile_image" id="profile_image"
                               class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        @if($profile->profile_image_path)
                            <span class="text-[10px] text-slate-400 block mt-1"><i class="fa-solid fa-image text-blue-500 mr-1"></i> Profile Image Uploaded</span>
                        @endif
                    </div>
                </div>

                <button type="submit" class="w-full h-12 btn-gradient text-white text-xs font-bold rounded-xl shadow-lg shadow-brand-500/10">
                    Save Profile Changes & Submit KYC Documents
                </button>
            </form>
        </div>

    </div>

    <!-- Password / Security Column -->
    <div class="lg:col-span-4 space-y-6">
        
        <!-- Password Change -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-4">
            <h3 class="font-bold text-slate-900 text-lg">Change Password</h3>
            
            <form action="{{ route('merchant.profile.password') }}" method="POST" class="space-y-4">
                @csrf
                <div class="space-y-1">
                    <label for="current_password" class="text-[10px] font-bold text-slate-500 uppercase">Current Password</label>
                    <input type="password" name="current_password" id="current_password" required
                           class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50">
                </div>

                <div class="space-y-1">
                    <label for="new_password" class="text-[10px] font-bold text-slate-500 uppercase">New Password</label>
                    <input type="password" name="new_password" id="new_password" required
                           class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50">
                </div>

                <div class="space-y-1">
                    <label for="new_password_confirmation" class="text-[10px] font-bold text-slate-500 uppercase">Confirm New Password</label>
                    <input type="password" name="new_password_confirmation" id="new_password_confirmation" required
                           class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50">
                </div>

                <button type="submit" class="w-full h-11 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-lg transition-colors">
                    Update Portal Password
                </button>
            </form>
        </div>

    </div>

</div>
@endsection
