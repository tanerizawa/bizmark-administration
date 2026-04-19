@extends('layouts.app')

@section('title', 'Audit Logs')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">Audit Logs</h1>
        <a href="{{ route('admin.security.webhook-metrics') }}" class="text-sm text-purple-600 hover:text-purple-800">Webhook Metrics</a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left">Waktu</th>
                        <th class="px-4 py-3 text-left">User</th>
                        <th class="px-4 py-3 text-left">Event</th>
                        <th class="px-4 py-3 text-left">Model</th>
                        <th class="px-4 py-3 text-left">ID</th>
                        <th class="px-4 py-3 text-left">Route</th>
                        <th class="px-4 py-3 text-left">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($logs as $log)
                        <tr>
                            <td class="px-4 py-3 text-gray-700">{{ $log->created_at }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $log->user_id }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $log->event }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ class_basename($log->auditable_type) }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $log->auditable_id }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $log->route }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $log->ip_address }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-gray-500">Belum ada audit log</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-4 py-4">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection

