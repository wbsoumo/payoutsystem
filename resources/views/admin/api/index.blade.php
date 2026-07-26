@extends('layouts.admin')
@section('title', 'API Management & Monitor')
@section('page_title', 'API Management')

@section('content')
<div class="space-y-8">

    <!-- Active API Keys Directory -->
    <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h4 class="font-bold text-slate-800 text-sm">Active Merchant API Keys</h4>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider text-[10px]">
                        <th class="py-4 px-6">ID</th>
                        <th class="py-4 px-6">Merchant</th>
                        <th class="py-4 px-6">Key Name</th>
                        <th class="py-4 px-6">Public Key Preview</th>
                        <th class="py-4 px-6">Status</th>
                        <th class="py-4 px-6">Created At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @forelse($keys as $k)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-4 px-6 font-mono select-all text-slate-400">{{ substr($k->id, 0, 8) }}...</td>
                            <td class="py-4 px-6 font-bold text-slate-900">{{ $k->merchant->business_name }}</td>
                            <td class="py-4 px-6 text-slate-600 font-semibold">{{ $k->name }}</td>
                            <td class="py-4 px-6 font-mono text-slate-800">{{ $k->api_key_preview }}</td>
                            <td class="py-4 px-6">
                                @if($k->is_active)
                                    <span class="px-2 py-0.5 rounded text-[8px] font-bold bg-green-50 text-green-700 border border-green-200 uppercase">Active</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[8px] font-bold bg-slate-100 text-slate-500 uppercase">Revoked</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-slate-400">{{ $k->created_at->format('M d Y, H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 px-6 text-center text-slate-400 font-semibold">No API Keys initialized.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- IP Whitelists Directory -->
    <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h4 class="font-bold text-slate-800 text-sm">Whitelisted API IP Addresses</h4>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider text-[10px]">
                        <th class="py-4 px-6">Merchant</th>
                        <th class="py-4 px-6">IP Address</th>
                        <th class="py-4 px-6">Description</th>
                        <th class="py-4 px-6">Status</th>
                        <th class="py-4 px-6">Whitelisted On</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @forelse($ips as $ip)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-4 px-6 font-bold text-slate-900">{{ $ip->merchant->business_name }}</td>
                            <td class="py-4 px-6 font-mono text-blue-600 font-bold">{{ $ip->ip_address }}</td>
                            <td class="py-4 px-6 text-slate-500">{{ $ip->description ?? 'No description' }}</td>
                            <td class="py-4 px-6">
                                @if($ip->is_active)
                                    <span class="px-2 py-0.5 rounded text-[8px] font-bold bg-green-50 text-green-700 border border-green-200 uppercase">Active</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[8px] font-bold bg-slate-100 text-slate-500 uppercase">Suspended</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-slate-400">{{ $ip->created_at->format('M d Y, H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 px-6 text-center text-slate-400 font-semibold">No IPs whitelisted.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- API Gateway Logs -->
    <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h4 class="font-bold text-slate-800 text-sm">Real-time API Connection Logs</h4>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider text-[10px]">
                        <th class="py-4 px-6">Endpoint</th>
                        <th class="py-4 px-6">Merchant</th>
                        <th class="py-4 px-6">Status Code</th>
                        <th class="py-4 px-6">Latency</th>
                        <th class="py-4 px-6">Source IP</th>
                        <th class="py-4 px-6">Timestamp</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @forelse($apiLogs as $log)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-4 px-6 font-mono font-bold text-slate-900"><span class="px-2 py-0.5 rounded text-[9px] font-extrabold uppercase mr-2 bg-blue-50 text-blue-600 border border-blue-100">{{ $log->method }}</span>/{{ $log->endpoint }}</td>
                            <td class="py-4 px-6">{{ $log->merchant->business_name ?? 'N/A' }}</td>
                            <td class="py-4 px-6">
                                @if($log->status_code >= 200 && $log->status_code < 300)
                                    <span class="px-2 py-0.5 rounded text-[8px] font-bold bg-green-50 text-green-700 border border-green-200">{{ $log->status_code }}</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[8px] font-bold bg-red-50 text-red-700 border border-red-200">{{ $log->status_code }}</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-slate-500 font-bold">{{ $log->execution_time_ms }} ms</td>
                            <td class="py-4 px-6 font-mono text-slate-400">{{ $log->source_ip }}</td>
                            <td class="py-4 px-6 text-slate-400">{{ $log->created_at->format('M d H:i:s') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 px-6 text-center text-slate-400 font-semibold">No connection logs audited yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
