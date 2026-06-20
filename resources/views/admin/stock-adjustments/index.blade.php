@extends('layouts.admin')

@section('page-title', 'Stock Adjustments')

@section('content')
    <div class="space-y-5">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-slate-900">Stock Adjustments</h2>
                <p class="text-sm text-slate-500 mt-0.5">Manual corrections for damage, loss, theft, or recounts.</p>
            </div>
            @can('stock-adjustments.create')
                <a href="{{ route('admin.stock-adjustments.create') }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 px-4 py-2.5 text-sm font-medium text-white shadow-sm">
                    <x-icon name="plus" class="w-4 h-4" /> New Adjustment
                </a>
            @endcan
        </div>

        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-slate-500">Adjustment #</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-500">Warehouse</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-500">Reason</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-500">Created By</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-500">Status</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-500">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($adjustments as $adjustment)
                        <tr class="hover:bg-slate-50/75 cursor-pointer"
                            onclick="window.location='{{ route('admin.stock-adjustments.show', $adjustment) }}'">
                            <td class="px-4 py-3 font-mono-num font-medium text-indigo-600">
                                {{ $adjustment->adjustment_number }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $adjustment->warehouse->name }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ ucwords(str_replace('_', ' ', $adjustment->reason)) }}
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $adjustment->user->name }}</td>
                            <td class="px-4 py-3">
                                <x-badge
                                    :color="$adjustment->isApproved() ? 'green' : 'amber'">{{ $adjustment->isApproved() ? 'Approved' : 'Pending' }}</x-badge>
                            </td>
                            <td class="px-4 py-3 text-slate-500">{{ $adjustment->created_at->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-slate-500">No stock adjustments yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if ($adjustments->hasPages())
                <div class="border-t border-slate-200 px-4 py-3">{{ $adjustments->links() }}</div>
            @endif
        </div>
    </div>
@endsection
