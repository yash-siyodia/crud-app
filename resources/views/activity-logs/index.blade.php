@extends('layouts.app_with_sidebar')

@section('title', 'Activity Logs')

@section('content')
<div class="w-full p-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold">Activity Logs</h2>
        <div class="flex gap-2">
            <button type="button" onclick="exportLogs('xlsx')" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                Export Excel
            </button>
            <button type="button" onclick="exportLogs('csv')" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                Export CSV
            </button>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white p-4 rounded shadow mb-4">
        <form method="GET" action="{{ route('activity-logs.index') }}" id="filterForm">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                <!-- User Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">User</label>
                    <select name="user_id" class="w-full border border-gray-300 rounded px-3 py-2">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Action Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Action</label>
                    <select name="action" class="w-full border border-gray-300 rounded px-3 py-2">
                        <option value="">All Actions</option>
                        @foreach($actions as $action)
                            <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                {{ ucfirst($action) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Model Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Model</label>
                    <select name="model" class="w-full border border-gray-300 rounded px-3 py-2">
                        <option value="">All Models</option>
                        @foreach($models as $model)
                            <option value="{{ $model }}" {{ request('model') == $model ? 'selected' : '' }}>
                                {{ $model }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Search Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Search..." 
                           class="w-full border border-gray-300 rounded px-3 py-2">
                </div>

                <!-- Date From -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" 
                           class="w-full border border-gray-300 rounded px-3 py-2">
                </div>

                <!-- Date To -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" 
                           class="w-full border border-gray-300 rounded px-3 py-2">
                </div>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Apply Filters
                </button>
                <a href="{{ route('activity-logs.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                    Clear Filters
                </a>
            </div>
        </form>
    </div>

    <!-- Logs Table -->
    <div class="bg-white rounded shadow overflow-x-auto">
        <table id="logsTable" class="min-w-full table-auto border-collapse">
            <thead class="bg-gray-200">
                <tr>
                    <th class="border px-4 py-2 text-left">ID</th>
                    <th class="border px-4 py-2 text-left">User</th>
                    <th class="border px-4 py-2 text-left">Action</th>
                    <th class="border px-4 py-2 text-left">Model</th>
                    <th class="border px-4 py-2 text-left">Model ID</th>
                    <th class="border px-4 py-2 text-left">IP Address</th>
                    <th class="border px-4 py-2 text-left">Created At</th>
                    <th class="border px-4 py-2 text-left">Details</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr class="odd:bg-white even:bg-gray-50">
                        <td class="border px-4 py-2">{{ $log->id }}</td>
                        <td class="border px-4 py-2">
                            @if($log->user)
                                <div class="text-sm">
                                    <div class="font-medium">{{ $log->user->name }}</div>
                                    <div class="text-gray-500 text-xs">{{ $log->user->email }}</div>
                                </div>
                            @else
                                <span class="text-gray-400">Guest</span>
                            @endif
                        </td>
                        <td class="border px-4 py-2">
                            <span class="px-2 py-1 rounded text-xs font-medium
                                @if($log->action == 'created') bg-green-100 text-green-800
                                @elseif($log->action == 'updated') bg-blue-100 text-blue-800
                                @elseif($log->action == 'deleted') bg-red-100 text-red-800
                                @elseif($log->action == 'login') bg-purple-100 text-purple-800
                                @elseif($log->action == 'logout') bg-gray-100 text-gray-800
                                @else bg-yellow-100 text-yellow-800
                                @endif">
                                {{ ucfirst($log->action) }}
                            </span>
                        </td>
                        <td class="border px-4 py-2">{{ $log->model ?? '—' }}</td>
                        <td class="border px-4 py-2">{{ $log->model_id ?? '—' }}</td>
                        <td class="border px-4 py-2 text-sm">{{ $log->ip_address ?? '—' }}</td>
                        <td class="border px-4 py-2 text-sm">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                        <td class="border px-4 py-2">
                            @if($log->details)
                                <button onclick="showDetails({{ $log->id }})" 
                                        class="text-blue-600 hover:underline text-sm">
                                    View Details
                                </button>
                                <div id="details-{{ $log->id }}" class="hidden mt-2 p-2 bg-gray-100 rounded text-xs">
                                    <pre class="whitespace-pre-wrap">{{ json_encode($log->details, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="border px-4 py-8 text-center text-gray-500">
                            No logs found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($logs->hasPages())
        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    @endif
</div>

<script>
    $(document).ready(function() {
        @if($logs->count() > 0)
        $('#logsTable').DataTable({
            order: [[0, 'desc']], // Sort by ID descending (newest first)
            pageLength: 50,
            searching: false, // Disable DataTables search since we have custom filters
            lengthChange: false,
            info: false
        });
        @endif
    });

    function showDetails(logId) {
        const detailsDiv = document.getElementById('details-' + logId);
        if (detailsDiv.classList.contains('hidden')) {
            detailsDiv.classList.remove('hidden');
        } else {
            detailsDiv.classList.add('hidden');
        }
    }

    function exportLogs(format) {
        // Get current filter parameters
        const form = document.getElementById('filterForm');
        const formData = new FormData(form);
        formData.append('format', format);

        // Build query string
        const params = new URLSearchParams(formData);
        
        // Redirect to export route with filters
        window.location.href = '{{ route("activity-logs.export") }}?' + params.toString();
    }
</script>
@endsection
