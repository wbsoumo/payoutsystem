@extends('layouts.public')
@section('title', 'Privacy Policy')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-16 space-y-8">
    <h1 class="text-4xl font-extrabold font-display text-slate-900 text-center">Privacy Policy</h1>
    <div class="bg-white border border-slate-200 rounded-3xl p-8 md:p-12 space-y-6 text-sm text-slate-600 leading-relaxed shadow-sm">
        <h3 class="font-bold text-slate-900 text-lg">1. Information Collection</h3>
        <p>We collect corporate details during access requests and require business identity documents (PAN, GST) for KYC verification upon merchant onboarding. Browser details and IP addresses are recorded during portal logins for geo-location tracking and audit logs.</p>
        
        <h3 class="font-bold text-slate-900 text-lg">2. Data Usage & Security</h3>
        <p>Your data is used solely to provide and secure our digital payout services. We implement strict encryption (AES-256) for database storage and secure session management. We do not sell corporate details to third-party marketing entities.</p>
    </div>
</div>
@endsection
