@extends('layouts.admin')
@section('title', 'Access Applications')
@section('page_title', 'Access Applications Queue')

@section('content')
<div class="space-y-6">

    <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-6">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Enquiries Queue</h1>
            <p class="text-xs text-slate-500">Businesses requesting invite logins. Review monthly volume stats and convert requests into merchant accounts.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold">
                    <tr>
                        <th class="px-6 py-3">Company Details</th>
                        <th class="px-6 py-3">Representative</th>
                        <th class="px-6 py-3">Contact Email</th>
                        <th class="px-6 py-3">Monthly Volume</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Applied Date</th>
                        <th class="px-6 py-3 text-right">Convert Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-slate-700">
                    @forelse($enquiries as $enquiry)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900 text-sm">{{ $enquiry->company_name }}</div>
                                <div class="text-[10px] text-slate-400 mt-0.5">Brand: {{ $enquiry->business_name }} | Type: {{ $enquiry->business_type }}</div>
                                @if($enquiry->website)
                                    <div class="text-[10px] text-blue-600 font-semibold mt-0.5"><a href="{{ $enquiry->website }}" target="_blank">{{ $enquiry->website }}</a></div>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-700">{{ $enquiry->full_name }}</td>
                            <td class="px-6 py-4 font-mono">
                                <div>{{ $enquiry->email }}</div>
                                <div class="text-[9px] text-slate-400 mt-0.5">{{ $enquiry->phone }}</div>
                            </td>
                            <td class="px-6 py-4 font-mono font-semibold uppercase text-slate-600">{{ $enquiry->monthly_volume }}</td>
                            <td class="px-6 py-4">
                                @if($enquiry->status === 'converted')
                                    <span class="px-2.5 py-0.5 bg-green-50 text-green-700 border border-green-200 rounded-md font-bold uppercase text-[9px]"><i class="fa-solid fa-circle-check mr-0.5"></i> Converted</span>
                                @elseif($enquiry->status === 'rejected')
                                    <span class="px-2.5 py-0.5 bg-red-50 text-red-700 border border-red-200 rounded-md font-bold uppercase text-[9px]">Rejected</span>
                                @else
                                    <span class="px-2.5 py-0.5 bg-yellow-50 text-yellow-700 border border-yellow-200 rounded-md font-bold uppercase text-[9px]">Pending</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">{{ $enquiry->created_at->format('Y-m-d') }}</td>
                            <td class="px-6 py-4 text-right">
                                @if($enquiry->status === 'pending')
                                    <form action="{{ route('admin.enquiries.convert', $enquiry->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-lg transition-colors text-[9px] uppercase shadow-sm">
                                            Convert
                                        </button>
                                    </form>
                                @else
                                    <span class="text-[10px] text-slate-400 font-semibold italic">Processed</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-slate-400 font-semibold">No invite requests found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
