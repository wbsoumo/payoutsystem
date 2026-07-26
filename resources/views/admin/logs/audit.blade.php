@extends('layouts.admin')
@section('title', 'Audit Logs')
@section('page_title', 'System Audit Trail')

@section('content')
<div class="space-y-6">

    <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-6">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Security Audit Logs</h1>
            <p class="text-xs text-slate-500">Every single administrator and merchant portal action is recorded in this immutable trail.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold">
                    <tr>
                        <th class="px-6 py-3">Log ID</th>
                        <th class="px-6 py-3">User Type</th>
                        <th class="px-6 py-3">User ID</th>
                        <th class="px-6 py-3">Action</th>
                        <th class="px-6 py-3">Description</th>
                        <th class="px-6 py-3">IP & Agent</th>
                        <th class="px-6 py-3">Timestamp</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-slate-700">
                    @forelse($logs as $log)
                        <tr>
                            <td class="px-6 py-4 font-mono font-bold text-slate-400">{{ $log->id }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase {{ $log->user_type === 'admin' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-green-50 text-green-700 border border-green-200' }}">
                                    {{ $log->user_type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-mono text-[10px]">{{ $log->user_id ?? 'System' }}</td>
                            <td class="px-6 py-4 font-bold text-slate-800">{{ $log->action }}</td>
                            <td class="px-6 py-4 font-semibold text-slate-600">{{ $log->description }}</td>
                            <td class="px-6 py-4">
                                <div class="font-bold">{{ $log->ip_address }}</div>
                                <div class="text-[9px] text-slate-400 truncate max-w-xs">{{ $log->user_agent }}</div>
                            </td>
                            <td class="px-6 py-4">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-slate-400 font-semibold">No audit logs found.</td>
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
