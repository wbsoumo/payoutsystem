@extends('layouts.admin')
@section('title', 'Admin Dashboard')
@section('page_title', 'System Management Control Panel')

@section('content')
<div class="space-y-8">

    <!-- Admin Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Merchants Count -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Registered Merchants</span>
                <span class="text-3xl font-extrabold text-slate-900 font-display">{{ $merchantsCount }}</span>
                <span class="text-xs text-slate-500 block"><a href="{{ route('admin.merchants') }}" class="text-blue-600 font-bold hover:underline">View merchant profiles</a></span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shadow-sm"><i class="fa-solid fa-users"></i></div>
        </div>

        <!-- Pending KYC -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">KYC Verification Queue</span>
                <span class="text-3xl font-extrabold text-slate-900 font-display">{{ $pendingKycCount }}</span>
                <span class="text-xs text-slate-500 block">Submitted documents pending review</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-yellow-50 text-yellow-600 flex items-center justify-center text-xl shadow-sm"><i class="fa-solid fa-circle-exclamation"></i></div>
        </div>

        <!-- Support Tickets Count -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Active Support Tickets</span>
                <span class="text-3xl font-extrabold text-slate-900 font-display">{{ $openTicketsCount }}</span>
                <span class="text-xs text-slate-500 block"><a href="{{ route('admin.tickets') }}" class="text-blue-600 font-bold hover:underline">View help requests</a></span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl shadow-sm"><i class="fa-solid fa-life-ring"></i></div>
        </div>
    </div>

    <!-- Contact Request / Access Application Queue -->
    <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-6">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="font-bold text-slate-900 text-lg">Access Applications Queue</h3>
                <p class="text-xs text-slate-500">Businesses requesting invite access. Review volume projections and convert into active merchant profiles.</p>
            </div>
            <a href="{{ route('admin.enquiries') }}" class="text-xs font-bold text-blue-600 hover:underline">View All Enquiries <i class="fa-solid fa-arrow-right ml-1"></i></a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold">
                    <tr>
                        <th class="px-6 py-3">Company Name</th>
                        <th class="px-6 py-3">Contact Person</th>
                        <th class="px-6 py-3">Email & Phone</th>
                        <th class="px-6 py-3">Monthly Volume</th>
                        <th class="px-6 py-3">Business Entity</th>
                        <th class="px-6 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-slate-700">
                    @forelse($pendingEnquiries as $enquiry)
                        <tr>
                            <td class="px-6 py-4 font-bold text-slate-800">{{ $enquiry->company_name }}</td>
                            <td class="px-6 py-4 font-semibold text-slate-700">{{ $enquiry->full_name }}</td>
                            <td class="px-6 py-4">
                                <div class="font-bold">{{ $enquiry->email }}</div>
                                <div class="text-[10px] text-slate-400 mt-0.5">{{ $enquiry->phone }}</div>
                            </td>
                            <td class="px-6 py-4 font-mono font-semibold uppercase text-slate-600">{{ $enquiry->monthly_volume }}</td>
                            <td class="px-6 py-4 font-semibold text-slate-500">{{ ucfirst($enquiry->business_type) }}</td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('admin.enquiries.convert', $enquiry->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-lg transition-colors text-[10px] uppercase shadow-sm">
                                        Convert to Merchant
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-400 font-semibold">No pending access requests in queue.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
