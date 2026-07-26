@extends('layouts.merchant')
@section('title', 'Webhooks')
@section('page_title', 'Webhook Deliveries')

@section('content')
<div class="space-y-8">
    <div class="grid lg:grid-cols-12 gap-8">
        <!-- Configuration Card -->
        <div class="lg:col-span-5 bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-6">
            <div>
                <h3 class="font-bold text-slate-900 text-lg">Configure Webhook Endpoint</h3>
                <p class="text-xs text-slate-500">Register your server endpoint to receive real-time JSON payment alerts.</p>
            </div>

            @if(session('success'))
                <div class="p-4 bg-green-50 text-green-700 text-xs font-semibold rounded-xl border border-green-100">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('merchant.webhooks.update') }}" method="POST" class="space-y-4">
                @csrf
                <div class="space-y-1">
                    <label for="url" class="text-[10px] font-bold text-slate-500 uppercase">Webhook URL Endpoint</label>
                    <input type="url" name="url" id="url" value="{{ $webhook->url ?? '' }}" placeholder="https://yourdomain.com/webhook/payout" required
                           class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50 font-mono">
                </div>

                @if($webhook)
                    <div class="p-4 bg-slate-50 rounded-xl space-y-1 text-xs">
                        <span class="font-bold text-slate-700 block">Signing Secret:</span>
                        <code class="text-indigo-600 font-mono select-all">{{ $webhook->secret_key }}</code>
                    </div>
                @endif

                <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-700">
                    <input type="checkbox" name="is_active" {{ !$webhook || $webhook->is_active ? 'checked' : '' }} class="rounded text-blue-600 focus:ring-blue-500">
                    <span>Activate Webhook Endpoint Listener</span>
                </label>

                <button type="submit" class="w-full h-11 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-lg text-xs shadow-lg shadow-blue-500/10 transition-colors">
                    Save Webhook Endpoint
                </button>
            </form>
        </div>

        <!-- Deliveries Log -->
        <div class="lg:col-span-7 bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-6">
            <div>
                <h3 class="font-bold text-slate-900 text-lg">Recent Deliveries</h3>
                <p class="text-xs text-slate-500">Log of outgoing webhook POST notifications.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-bold">
                        <tr>
                            <th class="px-4 py-3">Event/URI</th>
                            <th class="px-4 py-3 text-center">Response Code</th>
                            <th class="px-4 py-3 text-center">Executed Date</th>
                            <th class="px-4 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-slate-700">
                        @forelse($logs as $log)
                            <tr>
                                <td class="px-4 py-4 font-mono font-bold text-slate-500">
                                    <div class="text-slate-800 text-[11px]">{{ $log->endpoint }}</div>
                                    <div class="text-[9px] text-slate-400">Method: POST</div>
                                </td>
                                <td class="px-4 py-4 text-center font-mono font-bold text-slate-900">{{ $log->response_status ?? 200 }}</td>
                                <td class="px-4 py-4 text-center text-slate-500">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                <td class="px-4 py-4 text-center">
                                    <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-green-50 text-green-700 border border-green-200">
                                        Delivered
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-slate-400 font-semibold">No webhook payloads triggered yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
