@extends('layouts.merchant')
@section('title', 'Virtual Accounts')
@section('page_title', 'Virtual Accounts Manager')

@section('content')
<div class="space-y-8">
    <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div class="space-y-1">
            <h2 class="text-xl font-bold text-slate-900 font-display">Dedicated Smart Virtual Accounts</h2>
            <p class="text-xs text-slate-500">Assign dedicated virtual bank accounts to your retail/wholesale clients for automated real-time reconciliation.</p>
        </div>
        <button class="px-4 h-10 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl text-xs transition-colors flex items-center gap-1.5 shadow-sm">
            <i class="fa-solid fa-plus"></i> Create Virtual Account
        </button>
    </div>

    <!-- Active accounts list -->
    <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-6">
        <div>
            <h3 class="font-bold text-slate-900 text-lg">Active Client Accounts</h3>
            <p class="text-xs text-slate-500">List of all active client virtual accounts powered by ICICI Bank CMS API.</p>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            @foreach($virtualAccounts as $va)
                <div class="p-6 border border-slate-100 rounded-2xl bg-slate-50/50 space-y-4">
                    <div class="flex justify-between items-start">
                        <div class="space-y-1">
                            <span class="text-sm font-bold text-slate-800 block">{{ $va['customer_name'] }}</span>
                            <span class="text-[10px] text-slate-400 font-bold block uppercase">Smart Virtual Account</span>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[8px] font-bold uppercase bg-green-50 text-green-700 border border-green-200">
                            {{ $va['status'] }}
                        </span>
                    </div>

                    <div class="h-[1px] bg-slate-200/60"></div>

                    <div class="grid grid-cols-2 gap-4 text-xs font-semibold">
                        <div>
                            <span class="text-slate-400 block text-[9px] uppercase font-bold">Account Number</span>
                            <span class="text-slate-800 font-mono tracking-wider">{{ $va['account_number'] }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[9px] uppercase font-bold">IFSC Code</span>
                            <span class="text-slate-800 font-mono">{{ $va['ifsc'] }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
