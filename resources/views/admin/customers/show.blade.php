@extends('layouts.admin')

@section('page-title', $customer->name)
@section('breadcrumb', 'Customer Details')

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.customers.index') }}"
                    class="text-sm text-secondary hover:text-sage-600 dark:hover:text-sage-400 transition flex items-center gap-1.5 group">
                    <x-icon name="chevron-left" class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform" />
                    Back to Customers
                </a>
            </div>
            <div class="flex items-center gap-3">
                @can('customers.update')
                    <button type="button" data-modal-target="edit-customer"
                        class="inline-flex items-center gap-2 rounded-xl border border-theme hover:bg-sage-100 dark:hover:bg-sage-800/30 hover:text-sage-700 dark:hover:text-sage-300 px-4 py-2 text-sm font-medium text-secondary transition">
                        <x-icon name="pencil" class="w-4 h-4" />
                        Edit Customer
                    </button>
                @endcan
            </div>
        </div>

        {{-- Customer Summary Card --}}
        <div class="bg-card rounded-2xl border border-theme p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-xl bg-sage-100 dark:bg-sage-800/30 text-sage-600 dark:text-sage-400 flex items-center justify-center text-2xl font-bold">
                        {{ $customer->is_guest ? 'G' : substr($customer->name, 0, 1) }}
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-2xl font-bold text-primary">{{ $customer->name }}</h2>
                            @if ($customer->is_guest)
                                <span class="text-xs text-secondary bg-sage-100/50 dark:bg-sage-800/30 px-2.5 py-1 rounded-full border border-sage-200 dark:border-sage-700">Guest</span>
                            @endif
                        </div>
                        <div class="flex flex-wrap items-center gap-2 text-sm text-secondary mt-0.5">
                            @if ($customer->phone)
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="phone" class="w-3.5 h-3.5" />
                                    {{ $customer->phone }}
                                </span>
                            @endif
                            @if ($customer->email)
                                <span class="w-1 h-1 rounded-full bg-sage-300 dark:bg-sage-600 opacity-30"></span>
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="mail" class="w-3.5 h-3.5" />
                                    <a href="mailto:{{ $customer->email }}" class="hover:text-sage-600 dark:hover:text-sage-400 transition">
                                        {{ $customer->email }}
                                    </a>
                                </span>
                            @endif
                            @if (!$customer->phone && !$customer->email)
                                <span class="text-secondary opacity-60">No contact information</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-xs font-medium text-secondary uppercase tracking-wider">Total Spent</p>
                    <p class="text-3xl font-bold font-mono-num text-primary">{{ $totalSpent->formatted() }}</p>
                    <p class="text-xs text-secondary mt-0.5">{{ $sales->total() }} total orders</p>
                </div>
            </div>

            {{-- Address --}}
            @if ($customer->address)
                <div class="mt-4 p-4 bg-sage-50/50 dark:bg-sage-900/20 rounded-xl border border-theme">
                    <div class="flex items-start gap-2">
                        <x-icon name="home" class="w-4 h-4 text-secondary opacity-50 mt-0.5" />
                        <div>
                            <p class="text-xs font-medium text-secondary uppercase tracking-wider">Address</p>
                            <p class="text-sm text-primary mt-1">{{ $customer->address }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Stats Grid --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-4 pt-4 border-t border-theme">
                <div>
                    <p class="text-xs font-medium text-secondary uppercase tracking-wider">First Order</p>
                    <p class="text-sm font-medium text-primary mt-1">
                        {{ $sales->first()?->created_at->format('M d, Y') ?? '—' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs font-medium text-secondary uppercase tracking-wider">Last Order</p>
                    <p class="text-sm font-medium text-primary mt-1">
                        {{ $sales->last()?->created_at->format('M d, Y') ?? '—' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs font-medium text-secondary uppercase tracking-wider">Total Items</p>
                    <p class="text-sm font-medium text-primary mt-1 font-mono-num">
                        {{ $sales->sum(fn($s) => $s->items->sum('quantity')) }}
                    </p>
                </div>
                <div>
                    <p class="text-xs font-medium text-secondary uppercase tracking-wider">Avg. Order Value</p>
                    <p class="text-sm font-medium text-primary mt-1 font-mono-num">
                        {{ $sales->count() > 0 ? \App\Support\Money::fromAmount($sales->avg(fn($s) => $s->total()->amount()))->formatted() : '—' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Purchase History --}}
        <div class="bg-card rounded-2xl border border-theme overflow-hidden shadow-sm hover:shadow-md transition-shadow">
            <div class="px-6 py-4 border-b border-theme flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-sage-100 dark:bg-sage-800/30 text-sage-600 dark:text-sage-400 flex items-center justify-center">
                        <x-icon name="shopping-bag" class="w-4 h-4" />
                    </div>
                    <h3 class="font-semibold text-primary">Purchase History</h3>
                    <span class="text-xs text-secondary bg-sage-100/50 dark:bg-sage-800/30 px-2.5 py-1 rounded-full border border-sage-200 dark:border-sage-700">
                        {{ $sales->total() }} orders
                    </span>
                </div>
                <div class="flex items-center gap-4 text-xs text-secondary">
                    <span class="flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-sage-500 dark:bg-sage-400"></span>
                        Completed
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        Refunded
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-red-500"></span>
                        Cancelled
                    </span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-theme text-sm">
                    <thead class="bg-sage-50 dark:bg-sage-900/20">
                        <tr>
                            <th class="px-6 py-3.5 text-left font-medium text-xs uppercase tracking-wider text-secondary">
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="receipt" class="w-3.5 h-3.5" />
                                    Invoice
                                </span>
                            </th>
                            <th class="px-6 py-3.5 text-left font-medium text-xs uppercase tracking-wider text-secondary">
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="calendar" class="w-3.5 h-3.5" />
                                    Date
                                </span>
                            </th>
                            <th class="px-6 py-3.5 text-left font-medium text-xs uppercase tracking-wider text-secondary">
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="warehouse" class="w-3.5 h-3.5" />
                                    Warehouse
                                </span>
                            </th>
                            <th class="px-6 py-3.5 text-right font-medium text-xs uppercase tracking-wider text-secondary">
                                <span class="flex items-center justify-end gap-1.5">
                                    <x-icon name="cube" class="w-3.5 h-3.5" />
                                    Items
                                </span>
                            </th>
                            <th class="px-6 py-3.5 text-right font-medium text-xs uppercase tracking-wider text-secondary">
                                <span class="flex items-center justify-end gap-1.5">
                                    <x-icon name="cash" class="w-3.5 h-3.5" />
                                    Total
                                </span>
                            </th>
                            <th class="px-6 py-3.5 text-left font-medium text-xs uppercase tracking-wider text-secondary">
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="check-circle" class="w-3.5 h-3.5" />
                                    Status
                                </span>
                            </th>
                            <th class="px-6 py-3.5 text-center font-medium text-xs uppercase tracking-wider text-secondary">
                                <span class="flex items-center justify-center gap-1.5">
                                    <x-icon name="settings" class="w-3.5 h-3.5" />
                                    Actions
                                </span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-theme">
                        @forelse ($sales as $sale)
                            @php
                                $statusColors = [
                                    'completed' => 'success',
                                    'cancelled' => 'danger',
                                    'refunded' => 'warning',
                                    'partially_refunded' => 'warning',
                                ];
                            @endphp
                            <tr class="hover:bg-sage-50/50 dark:hover:bg-sage-900/20 transition group cursor-pointer"
                                onclick="window.location='{{ route('admin.sales.show', $sale) }}'">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-lg bg-sage-100/50 dark:bg-sage-800/30 flex items-center justify-center flex-shrink-0">
                                            <x-icon name="receipt" class="w-3.5 h-3.5 text-sage-600 dark:text-sage-400" />
                                        </div>
                                        <span class="font-mono-num font-semibold text-sage-600 dark:text-sage-400">
                                            {{ $sale->invoice_number }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-secondary text-sm">
                                    <div class="flex items-center gap-1.5">
                                        <x-icon name="calendar" class="w-3.5 h-3.5 text-secondary opacity-40" />
                                        {{ $sale->created_at->format('M d, Y') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-sage-100/50 dark:bg-sage-800/30 text-xs font-medium text-sage-700 dark:text-sage-300 border border-sage-200 dark:border-sage-700">
                                        <x-icon name="warehouse" class="w-3 h-3" />
                                        {{ $sale->warehouse->name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right font-mono-num text-secondary">
                                    {{ $sale->items->sum('quantity') }}
                                </td>
                                <td class="px-6 py-4 text-right font-mono-num font-semibold text-primary">
                                    {{ $sale->total()->formatted() }}
                                </td>
                                <td class="px-6 py-4">
                                    <x-badge :color="$statusColors[$sale->status->value]">
                                        <span class="flex items-center gap-1.5">
                                            @if ($sale->status->value === 'completed')
                                                <span class="w-1.5 h-1.5 rounded-full bg-sage-500 dark:bg-sage-400 animate-pulse"></span>
                                            @endif
                                            {{ $sale->status->label() }}
                                        </span>
                                    </x-badge>
                                </td>
                                <td class="px-6 py-4 text-center" onclick="event.stopPropagation()">
                                    <a href="{{ route('admin.sales.show', $sale) }}"
                                        class="p-1.5 rounded-lg text-secondary hover:bg-sage-100 dark:hover:bg-sage-800/30 hover:text-sage-700 dark:hover:text-sage-300 transition"
                                        title="View Order">
                                        <x-icon name="eye" class="w-4 h-4" />
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-20 h-20 rounded-2xl bg-sage-100/30 dark:bg-sage-800/20 flex items-center justify-center mb-4">
                                            <x-icon name="shopping-bag" class="w-10 h-10 text-secondary opacity-30" />
                                        </div>
                                        <p class="text-lg font-medium text-primary">No purchases yet</p>
                                        <p class="text-sm text-secondary mt-1">This customer hasn't made any purchases</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($sales->hasPages())
                <div class="border-t border-theme px-6 py-4 flex items-center justify-between">
                    <div class="text-sm text-secondary">
                        Showing <span class="font-medium text-primary">{{ $sales->firstItem() ?? 0 }}</span>
                        to <span class="font-medium text-primary">{{ $sales->lastItem() ?? 0 }}</span>
                        of <span class="font-medium text-primary">{{ $sales->total() }}</span> orders
                    </div>
                    <div>
                        {{ $sales->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Edit Modal --}}
    <x-modal id="edit-customer" title="Edit Customer" description="Update customer information" icon="pencil" maxWidth="lg">
        <form method="POST" action="{{ route('admin.customers.update', $customer) }}">
            @csrf @method('PUT')
            @include('admin.customers._fields', ['customer' => $customer])
            <div class="mt-4 flex justify-end gap-2">
                <button type="button" data-modal-close="edit-customer"
                    class="rounded-xl border border-theme text-sm font-medium px-5 py-2 text-secondary hover:bg-sage-50 dark:hover:bg-sage-900/20 hover:text-primary transition">
                    Cancel
                </button>
                <button type="submit"
                    class="rounded-xl bg-sage-600 hover:bg-sage-700 dark:bg-sage-500 dark:hover:bg-sage-600 text-sm font-medium px-5 py-2 text-white shadow-sm hover:shadow-md transition flex items-center gap-2">
                    <x-icon name="check" class="w-4 h-4" />
                    Save Changes
                </button>
            </div>
        </form>
    </x-modal>
@endsection
