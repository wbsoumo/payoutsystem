@extends('layouts.public')
@section('title', 'Help & Support')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-16 text-center space-y-8">
    <h1 class="text-4xl font-extrabold font-display text-slate-900">Help & Corporate Support</h1>
    <p class="text-slate-600 text-lg max-w-xl mx-auto">
        Have questions? Registered merchants can create support tickets directly inside the dashboard panel.
    </p>
    <div class="bg-white border border-slate-200 rounded-3xl p-8 max-w-md mx-auto space-y-4">
        <h3 class="font-bold text-slate-900 text-lg">Contact Support</h3>
        <p class="text-slate-500 text-sm">Our typical corporate SLA for merchant queries is under 2 hours.</p>
        <div class="text-brand-500 font-bold font-mono text-sm">support@novexapay.com</div>
        <div class="h-[1px] bg-slate-100"></div>
        <a href="{{ route('contact') }}" class="w-full h-12 btn-gradient text-white rounded-xl flex items-center justify-center font-bold shadow-lg shadow-brand-500/10">Submit Request Access Form</a>
    </div>
</div>
@endsection
