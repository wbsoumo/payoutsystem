@extends('layouts.admin')
@section('title', 'API Gateway Logs')
@section('page_title', 'REST API Gateway Logs')

@section('content')
<div class="space-y-6">

    <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-6">
        <div>
            <h1 class="text-xl font-bold text-slate-900">API Gateway Logs</h1>
            <p class="text-xs text-slate-500">Monitor API calls passing through backend layers. Verifies signatures, nonces, and response performance.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold">
                    <tr>
                        <th class="px-6 py-3">Log ID</th>
                        <th class="px-6 py-3">Merchant</th>
                        <th class="px-6 py-3">Endpoint & Method</th>
                        <th class="px-6 py-3">Source IP</th>
                        <th class="px-6 py-3">Execution Time</th>
                        <th class="px-6 py-3">Security Checks</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Timestamp</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-slate-700">
                    @forelse($logs as $log)
                        <tr>
                            <td class="px-6 py-4 font-mono font-bold text-slate-400">{{ $log->id }}</td>
                            <td class="px-6 py-4 font-bold text-slate-800">{{ $log->merchant->company_name ?? 'Invalid key / unknown' }}</td>
                            <td class="px-6 py-4">
                                <span class="px-1.5 py-0.5 rounded font-extrabold uppercase text-[9px] {{ $log->method === 'POST' ? 'bg-green-50 text-green-700' : 'bg-blue-50 text-blue-700' }} mr-1">{{ $log->method }}</span>
                                <code class="font-mono text-xs font-semibold text-slate-700">{{ $log->endpoint }}</code>
                            </td>
                            <td class="px-6 py-4 font-mono">{{ $log->source_ip }}</td>
                            <td class="px-6 py-4 font-mono font-bold">{{ $log->execution_time_ms }} ms</td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1 text-[9px]">
                                    <span class="px-1.5 py-0.2 rounded font-bold {{ $log->signature_result ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200' }}">SIG</span>
                                    <span class="px-1.5 py-0.2 rounded font-bold {{ $log->timestamp_validation ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200' }}">TIME</span>
                                    <span class="px-1.5 py-0.2 rounded font-bold {{ $log->nonce_validation ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200' }}">NONCE</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase {{ $log->status_code >= 200 && $log->status_code < 300 ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200' }}">{{ $log->status_code }}</span>
                            </td>
                            <td class="px-6 py-4">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-slate-400 font-semibold">No API traffic recorded.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-4 border-t border-slate-100">
            {{ $logs->links() }}
        </div>
    </div>

</div>
@endsection
