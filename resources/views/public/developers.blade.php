@extends('layouts.public')
@section('title', 'Developers')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-16 text-center space-y-8">
    <h1 class="text-4xl font-extrabold font-display text-slate-900">Developer Portal</h1>
    <p class="text-slate-600 text-lg max-w-xl mx-auto">
        Integrate secure payment capabilities into your platforms with ease. Get client libraries, testing suites, and cryptographic helpers.
    </p>
    <div class="flex justify-center gap-4">
        <a href="{{ route('docs') }}" class="btn-gradient text-white px-6 h-12 rounded-xl flex items-center justify-center font-bold shadow-lg shadow-brand-500/10">View API Docs</a>
        <a href="{{ route('contact') }}" class="border border-slate-200 hover:border-slate-300 bg-white text-slate-700 px-6 h-12 rounded-xl flex items-center justify-center font-bold">Request Sandbox Keys</a>
    </div>
</div>
@endsection
