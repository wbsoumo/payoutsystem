@extends('layouts.admin')
@section('title', 'Support Tickets')
@section('page_title', 'Merchant Support Desk')

@section('content')
<div class="space-y-6">

    <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-6">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Support Tickets</h1>
            <p class="text-xs text-slate-500">Provide support to compliance, verification, or settlement issues.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold">
                    <tr>
                        <th class="px-6 py-3">Merchant</th>
                        <th class="px-6 py-3">Subject</th>
                        <th class="px-6 py-3">Priority</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Last Updated</th>
                        <th class="px-6 py-3 text-right">Inspect</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-slate-700">
                    @forelse($tickets as $ticket)
                        <tr>
                            <td class="px-6 py-4 font-bold text-slate-900">{{ $ticket->merchant->company_name }}</td>
                            <td class="px-6 py-4 font-semibold text-slate-700">
                                <a href="{{ route('admin.tickets.view', $ticket->id) }}" class="hover:text-blue-600 transition-colors">{{ $ticket->subject }}</a>
                            </td>
                            <td class="px-6 py-4">
                                @if($ticket->priority === 'high')
                                    <span class="text-red-500 font-bold uppercase">High</span>
                                @elseif($ticket->priority === 'medium')
                                    <span class="text-yellow-600 font-bold uppercase">Medium</span>
                                @else
                                    <span class="text-slate-500 font-bold uppercase">Low</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($ticket->status === 'open')
                                    <span class="px-2.5 py-0.5 bg-blue-50 text-blue-700 border border-blue-200 rounded-md font-bold uppercase text-[9px]">Open</span>
                                @elseif($ticket->status === 'replied')
                                    <span class="px-2.5 py-0.5 bg-green-50 text-green-700 border border-green-200 rounded-md font-bold uppercase text-[9px]">Replied</span>
                                @else
                                    <span class="px-2.5 py-0.5 bg-slate-100 text-slate-500 border border-slate-200 rounded-md font-bold uppercase text-[9px]">Closed</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">{{ $ticket->updated_at->diffForHumans() }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.tickets.view', $ticket->id) }}" class="text-slate-400 hover:text-blue-600 text-sm">
                                    <i class="fa-solid fa-chevron-right"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-400 font-semibold">No support requests in database.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
