@extends('layouts.admin')

@section('page-title', 'Activity Log')

@section('content')
    <div class="space-y-5">
        <div>
            <h2 class="text-xl font-semibold text-slate-900">Activity Log</h2>
            <p class="text-sm text-slate-500 mt-0.5">System-wide audit trail of significant actions.</p>
        </div>

        <form method="GET" class="bg-white rounded-xl border border-slate-200 p-4 grid grid-cols-1 sm:grid-cols-5 gap-3">
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search description..."
                class="rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            <select name="log_name"
                class="rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">All categories</option>
                @foreach ($logNames as $logName)
                    <option value="{{ $logName }}" @selected(($filters['log_name'] ?? '') === $logName)>{{ ucfirst($logName) }}</option>
                @endforeach
            </select>
            <select name="causer_id"
                class="rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">All users</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" @selected(($filters['causer_id'] ?? null) == $user->id)>{{ $user->name }}</option>
                @endforeach
            </select>
            <input type="date" name="from" value="{{ $filters['from'] ?? '' }}"
                class="rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            <button type="submit"
                class="rounded-lg bg-slate-800 hover:bg-slate-700 text-white text-sm font-medium px-4 py-2">Filter</button>
        </form>

        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-slate-500">Date</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-500">User</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-500">Category</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-500">Description</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-500">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($activities as $activity)
                        <tr class="hover:bg-slate-50/75">
                            <td class="px-4 py-2.5 text-slate-500 whitespace-nowrap">
                                {{ $activity->created_at->format('M d, Y g:i A') }}</td>
                            <td class="px-4 py-2.5 text-slate-700">{{ $activity->causer->name ?? 'System' }}</td>
                            <td class="px-4 py-2.5"><x-badge color="slate">{{ ucfirst($activity->log_name) }}</x-badge>
                            </td>
                            <td class="px-4 py-2.5 text-slate-900">{{ $activity->description }}</td>
                            <td class="px-4 py-2.5 font-mono-num text-slate-400">{{ $activity->ip_address ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-slate-500">No activity recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if ($activities->hasPages())
                <div class="border-t border-slate-200 px-4 py-3">{{ $activities->links() }}</div>
            @endif
        </div>
    </div>
@endsection
