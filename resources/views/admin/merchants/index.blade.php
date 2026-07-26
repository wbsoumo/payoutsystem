@extends('layouts.admin')
@section('title', 'Merchants')
@section('page_title', 'Merchant Directory')

@section('content')
<div class="space-y-6">

    <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-6">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Merchants Accounts</h1>
            <p class="text-xs text-slate-500">Monitor and inspect active, pending, or suspended business entity profiles.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold">
                    <tr>
                        <th class="px-6 py-3">Company Name</th>
                        <th class="px-6 py-3">Contact Email</th>
                        <th class="px-6 py-3">Wallet Balance</th>
                        <th class="px-6 py-3">KYC Status</th>
                        <th class="px-6 py-3">Account Status</th>
                        <th class="px-6 py-3">Joined Date</th>
                        <th class="px-6 py-3 text-right">Inspect</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-slate-700">
                    @forelse($merchants as $merchant)
                        <tr>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.merchants.view', $merchant->id) }}" class="font-bold text-slate-900 hover:text-blue-600 transition-colors text-sm">{{ $merchant->company_name }}</a>
                                <div class="text-[10px] text-slate-400 mt-0.5">Brand: {{ $merchant->business_name }}</div>
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-700">{{ $merchant->email }}</td>
                            <td class="px-6 py-4 font-extrabold text-slate-950">₹{{ number_format($merchant->wallet->balance ?? 0.0, 2) }}</td>
                            <td class="px-6 py-4">
                                @if($merchant->kyc_status === 'approved')
                                    <span class="px-2 py-0.5 bg-green-50 text-green-700 border border-green-200 rounded text-[9px] font-bold uppercase">Approved</span>
                                @elseif($merchant->kyc_status === 'submitted')
                                    <span class="px-2 py-0.5 bg-yellow-50 text-yellow-700 border border-yellow-200 rounded text-[9px] font-bold uppercase">Submitted</span>
                                @else
                                    <span class="px-2 py-0.5 bg-red-50 text-red-700 border border-red-200 rounded text-[9px] font-bold uppercase">Pending Docs</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($merchant->status === 'active')
                                    <span class="px-2.5 py-0.5 bg-green-50 text-green-700 border border-green-200 rounded-md font-bold uppercase text-[9px]">Active</span>
                                @elseif($merchant->status === 'pending')
                                    <span class="px-2.5 py-0.5 bg-yellow-50 text-yellow-700 border border-yellow-200 rounded-md font-bold uppercase text-[9px]">Pending Setup</span>
                                @else
                                    <span class="px-2.5 py-0.5 bg-red-50 text-red-700 border border-red-200 rounded-md font-bold uppercase text-[9px]">Suspended</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">{{ $merchant->created_at->format('Y-m-d') }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.merchants.view', $merchant->id) }}" 
                                   class="px-3 py-1.5 bg-slate-100 hover:bg-blue-600 hover:text-white text-slate-700 font-bold rounded-lg transition-all text-[10px] uppercase shadow-sm inline-flex items-center gap-1">
                                    <i class="fa-solid fa-eye"></i> View Profile
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-slate-400 font-semibold">No merchants registered. Convert access enquiries or seeds first.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
