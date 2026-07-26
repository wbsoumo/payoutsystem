@extends('layouts.admin')
@section('title', 'Support Ticket Details')
@section('page_title', 'Support Desk Console')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Ticket Card Header -->
    <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm flex items-start justify-between">
        <div class="space-y-2">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">SUPPORT TICKET ID: {{ $ticket->id }}</span>
            <h1 class="text-xl font-bold text-slate-900">{{ $ticket->subject }}</h1>
            <p class="text-slate-500 text-sm">Merchant: <span class="font-bold text-slate-800">{{ $ticket->merchant->company_name }}</span></p>
            <div class="flex items-center gap-3 text-xs text-slate-400">
                <span>Priority: <span class="font-bold text-slate-700 uppercase">{{ $ticket->priority }}</span></span>
                <span>•</span>
                <span>Submitted: {{ $ticket->created_at->format('Y-m-d H:i') }}</span>
            </div>
        </div>
        <div>
            @if($ticket->status === 'open')
                <span class="px-3 py-1 bg-blue-50 text-blue-700 border border-blue-200 rounded-xl font-bold uppercase text-xs">Open</span>
            @elseif($ticket->status === 'replied')
                <span class="px-3 py-1 bg-green-50 text-green-700 border border-green-200 rounded-xl font-bold uppercase text-xs">Replied</span>
            @else
                <span class="px-3 py-1 bg-slate-100 text-slate-500 border border-slate-200 rounded-xl font-bold uppercase text-xs">Closed</span>
            @endif
        </div>
    </div>

    <!-- Message Thread -->
    <div class="space-y-4">
        <!-- Original message -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-3">
            <div class="flex justify-between text-xs border-b border-slate-100 pb-2">
                <span class="font-bold text-slate-900">{{ $ticket->merchant->company_name }} (Merchant)</span>
                <span class="text-slate-400">{{ $ticket->created_at->format('Y-m-d H:i') }}</span>
            </div>
            <p class="text-xs text-slate-700 leading-relaxed whitespace-pre-line">{{ $ticket->message }}</p>
        </div>

        <!-- Replies list -->
        @if($ticket->replies)
            @foreach($ticket->replies as $reply)
                @php
                    $isAdmin = ($reply['user_type'] === 'admin');
                @endphp
                <div class="rounded-3xl p-6 shadow-sm border space-y-3 {{ $isAdmin ? 'bg-blue-50/50 border-blue-100 ml-12' : 'bg-white border-slate-200 mr-12' }}">
                    <div class="flex justify-between text-xs border-b pb-2 {{ $isAdmin ? 'border-blue-100 text-blue-800' : 'border-slate-100 text-slate-900' }}">
                        <span class="font-bold">{{ $reply['user_name'] }} {!! $isAdmin ? '<span class="ml-1 px-1.5 py-0.2 bg-blue-600 text-white rounded text-[8px] font-bold">STAFF</span>' : '' !!}</span>
                        <span class="text-slate-400">{{ $reply['created_at'] }}</span>
                    </div>
                    <p class="text-xs text-slate-700 leading-relaxed whitespace-pre-line">{{ $reply['message'] }}</p>
                </div>
            @endforeach
        @endif
    </div>

    <!-- Reply Form -->
    <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-4">
        <h3 class="font-bold text-slate-900 text-sm">Post a Response</h3>
        <form action="{{ route('admin.tickets.reply', $ticket->id) }}" method="POST" class="space-y-4">
            @csrf
            <div class="space-y-1">
                <textarea name="message" rows="4" required placeholder="Type your reply to the merchant here..."
                          class="w-full p-4 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50"></textarea>
            </div>
            <button type="submit" class="btn-gradient text-white px-6 h-11 rounded-xl text-xs font-bold shadow-lg shadow-blue-500/10">
                Post Response
            </button>
        </form>
    </div>

</div>
@endsection
