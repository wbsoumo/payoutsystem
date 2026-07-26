@extends('layouts.merchant')
@section('title', 'Help & Support')
@section('page_title', 'Support Tickets')

@section('content')
<div class="grid lg:grid-cols-12 gap-8" x-data="{ showCreateForm: false }">

    <!-- Tickets List -->
    <div class="lg:col-span-8 space-y-6">
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-6">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-slate-900 text-lg">Support History</h3>
                    <p class="text-xs text-slate-500">Track and respond to official administrative queries.</p>
                </div>
                <button @click="showCreateForm = !showCreateForm" class="btn-gradient text-white px-4 h-10 rounded-xl text-xs font-bold shadow-lg shadow-blue-500/10">
                    <i class="fa-solid fa-plus mr-1"></i> Create Support Ticket
                </button>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($tickets as $ticket)
                    <div class="py-4 flex justify-between items-center text-xs">
                        <div class="space-y-1">
                            <a href="{{ route('merchant.tickets.view', $ticket->id) }}" class="font-bold text-slate-900 hover:text-blue-600 transition-colors text-sm">{{ $ticket->subject }}</a>
                            <div class="flex items-center gap-3 text-slate-400">
                                <span>Priority: 
                                    @if($ticket->priority === 'high')
                                        <span class="text-red-500 font-bold uppercase">High</span>
                                    @elseif($ticket->priority === 'medium')
                                        <span class="text-yellow-600 font-bold uppercase">Medium</span>
                                    @else
                                        <span class="text-slate-500 font-bold uppercase">Low</span>
                                    @endif
                                </span>
                                <span>Updated: {{ $ticket->updated_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-4">
                            @if($ticket->status === 'open')
                                <span class="px-2.5 py-0.5 bg-blue-50 text-blue-700 border border-blue-200 rounded-md font-bold uppercase text-[9px]">Open</span>
                            @elseif($ticket->status === 'replied')
                                <span class="px-2.5 py-0.5 bg-green-50 text-green-700 border border-green-200 rounded-md font-bold uppercase text-[9px]">Replied</span>
                            @else
                                <span class="px-2.5 py-0.5 bg-slate-100 text-slate-500 border border-slate-200 rounded-md font-bold uppercase text-[9px]">Closed</span>
                            @endif
                            
                            <a href="{{ route('merchant.tickets.view', $ticket->id) }}" class="text-slate-400 hover:text-blue-600 text-sm">
                                <i class="fa-solid fa-chevron-right"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-400 font-semibold">No support tickets generated. Create one to contact compliance staff.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Create Ticket Form -->
    <div class="lg:col-span-4 space-y-6" x-show="showCreateForm || {{ count($tickets) === 0 ? 'true' : 'false' }}">
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-4">
            <h3 class="font-bold text-slate-900 text-lg">Create Support Ticket</h3>
            <p class="text-xs text-slate-500 leading-relaxed">Submit technical queries, wallet balance issues, or KYC updates. Typical response SLA is 2 hours.</p>

            <form action="{{ route('merchant.tickets.create') }}" method="POST" class="space-y-4">
                @csrf
                <div class="space-y-1">
                    <label for="subject" class="text-[10px] font-bold text-slate-500 uppercase">Subject</label>
                    <input type="text" name="subject" id="subject" placeholder="e.g. KYC Approval Issue" required
                           class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50">
                </div>

                <div class="space-y-1">
                    <label for="priority" class="text-[10px] font-bold text-slate-500 uppercase">Priority</label>
                    <select name="priority" id="priority" required
                            class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50">
                        <option value="low">Low - General query</option>
                        <option value="medium" selected>Medium - Account issue</option>
                        <option value="high">High - Transaction block</option>
                    </select>
                </div>

                <div class="space-y-1">
                    <label for="message" class="text-[10px] font-bold text-slate-500 uppercase">Message</label>
                    <textarea name="message" id="message" rows="4" required placeholder="Describe your issue in details..."
                              class="w-full p-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50"></textarea>
                </div>

                <button type="submit" class="w-full h-11 btn-gradient text-white text-xs font-bold rounded-lg shadow-lg shadow-brand-500/10">
                    Submit Support Ticket
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
