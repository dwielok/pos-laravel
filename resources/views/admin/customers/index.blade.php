@extends('layouts.admin')

@section('page-title', 'Customers')
@section('breadcrumb', 'Customer Management')

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-sage-100 dark:bg-sage-800/30 text-sage-600 dark:text-sage-400 flex items-center justify-center">
                    <x-icon name="users" class="w-5 h-5" />
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-primary">Customers</h2>
                    <div class="flex items-center gap-2 text-sm text-secondary">
                        <span>{{ $customers->total() }} total customers</span>
                        <span class="w-1 h-1 rounded-full bg-sage-300 dark:bg-sage-600 opacity-30"></span>
                        <span class="flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-sage-500 dark:bg-sage-400"></span>
                            {{ $customers->filter(fn($c) => $c->sales()->count() > 0)->count() }} with purchases
                        </span>
                    </div>
                </div>
            </div>
            @can('customers.create')
                <button type="button" data-modal-target="create-customer"
                    class="inline-flex items-center gap-2 rounded-xl bg-sage-600 hover:bg-sage-700 dark:bg-sage-500 dark:hover:bg-sage-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm hover:shadow-md transition-all duration-200 group">
                    <x-icon name="plus" class="w-4 h-4 group-hover:rotate-90 transition-transform duration-300" />
                    Add Customer
                </button>
            @endcan
        </div>

        {{-- Quick Stats --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="bg-card rounded-xl border border-theme p-4 shadow-sm">
                <p class="text-xs font-medium text-secondary uppercase tracking-wider">Total Customers</p>
                <p class="text-lg font-bold text-primary mt-1">{{ $customers->total() }}</p>
            </div>
            <div class="bg-card rounded-xl border border-theme p-4 shadow-sm">
                <p class="text-xs font-medium text-secondary uppercase tracking-wider">Active Customers</p>
                <p class="text-lg font-bold text-sage-600 dark:text-sage-400 mt-1">
                    {{ $customers->filter(fn($c) => $c->sales()->count() > 0)->count() }}
                </p>
            </div>
            <div class="bg-card rounded-xl border border-theme p-4 shadow-sm">
                <p class="text-xs font-medium text-secondary uppercase tracking-wider">Total Revenue</p>
                <p class="text-lg font-bold text-primary mt-1 font-mono-num">
                    {{ \App\Support\Money::fromAmount($customers->sum(fn($c) => $c->totalSpent()->amount()))->formatted() }}
                </p>
            </div>
            <div class="bg-card rounded-xl border border-theme p-4 shadow-sm">
                <p class="text-xs font-medium text-secondary uppercase tracking-wider">Guest Customers</p>
                <p class="text-lg font-bold text-primary mt-1">
                    {{ $customers->filter(fn($c) => $c->is_guest)->count() }}
                </p>
            </div>
        </div>

        {{-- Search --}}
        <form method="GET" class="bg-card rounded-2xl border border-theme p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-secondary opacity-40">
                        <x-icon name="search" class="w-4 h-4" />
                    </div>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                        placeholder="Search by name, phone, or email..."
                        class="w-full rounded-xl border-theme pl-9 pr-4 py-2.5 bg-sage-50/50 dark:bg-sage-900/20 text-primary text-sm focus:ring-2 focus:ring-sage-400 dark:focus:ring-sage-500 focus:border-sage-400 dark:focus:border-sage-500 transition">
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-sage-600 hover:bg-sage-700 dark:bg-sage-500 dark:hover:bg-sage-600 text-white text-sm font-medium px-5 py-2.5 transition shadow-sm hover:shadow-md">
                        <x-icon name="search" class="w-4 h-4" />
                        Search
                    </button>
                    @if (request()->has('search'))
                        <a href="{{ route('admin.customers.index') }}"
                            class="inline-flex items-center gap-2 rounded-xl border border-theme text-sm font-medium px-5 py-2.5 text-secondary hover:bg-sage-50 dark:hover:bg-sage-900/20 hover:text-primary transition">
                            <x-icon name="x" class="w-4 h-4" />
                            Clear
                        </a>
                    @endif
                </div>
            </div>

            @if ($customers->total() > 0)
                <div class="mt-3 pt-3 border-t border-theme flex items-center justify-between">
                    <span class="text-xs text-secondary">
                        <span class="font-medium text-primary">{{ $customers->total() }}</span> customers found
                    </span>
                    <span class="text-xs text-secondary opacity-60">
                        Sorted by name
                    </span>
                </div>
            @endif
        </form>

        {{-- Customers Table --}}
        <div class="bg-card rounded-2xl border border-theme overflow-hidden shadow-sm hover:shadow-md transition-shadow">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-theme text-sm">
                    <thead class="bg-sage-50 dark:bg-sage-900/20">
                        <tr>
                            <th class="px-6 py-3.5 text-left font-medium text-xs uppercase tracking-wider text-secondary">
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="user" class="w-3.5 h-3.5" />
                                    Customer
                                </span>
                            </th>
                            <th class="px-6 py-3.5 text-left font-medium text-xs uppercase tracking-wider text-secondary">
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="phone" class="w-3.5 h-3.5" />
                                    Phone
                                </span>
                            </th>
                            <th class="px-6 py-3.5 text-left font-medium text-xs uppercase tracking-wider text-secondary">
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="mail" class="w-3.5 h-3.5" />
                                    Email
                                </span>
                            </th>
                            <th class="px-6 py-3.5 text-left font-medium text-xs uppercase tracking-wider text-secondary">
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="shopping-bag" class="w-3.5 h-3.5" />
                                    Orders
                                </span>
                            </th>
                            <th class="px-6 py-3.5 text-right font-medium text-xs uppercase tracking-wider text-secondary">
                                <span class="flex items-center justify-end gap-1.5">
                                    <x-icon name="cash" class="w-3.5 h-3.5" />
                                    Total Spent
                                </span>
                            </th>
                            <th class="px-6 py-3.5 text-left font-medium text-xs uppercase tracking-wider text-secondary">
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="clock" class="w-3.5 h-3.5" />
                                    Joined
                                </span>
                            </th>
                            <th class="px-6 py-3.5 text-right font-medium text-xs uppercase tracking-wider text-secondary">
                                <span class="flex items-center justify-end gap-1.5">
                                    <x-icon name="settings" class="w-3.5 h-3.5" />
                                    Actions
                                </span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-theme">
                        @forelse ($customers as $customer)
                            <tr class="hover:bg-sage-50/50 dark:hover:bg-sage-900/20 transition group cursor-pointer"
                                onclick="window.location='{{ route('admin.customers.show', $customer) }}'">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-sage-100/50 dark:bg-sage-800/30 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                                            <span class="text-sage-600 dark:text-sage-400 font-semibold text-sm">
                                                {{ $customer->is_guest ? 'G' : substr($customer->name, 0, 1) }}
                                            </span>
                                        </div>
                                        <div>
                                            <span class="font-medium text-primary">{{ $customer->name }}</span>
                                            @if ($customer->is_guest)
                                                <span class="text-xs text-secondary bg-sage-100/50 dark:bg-sage-800/30 px-2 py-0.5 rounded-full border border-sage-200 dark:border-sage-700 ml-1.5">Guest</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-secondary">
                                    {{ $customer->phone ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-secondary">
                                    @if ($customer->email)
                                        <a href="mailto:{{ $customer->email }}"
                                            class="hover:text-sage-600 dark:hover:text-sage-400 transition">
                                            {{ $customer->email }}
                                        </a>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @php $orderCount = $customer->sales()->count(); @endphp
                                    @if ($orderCount > 0)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-sage-100/50 dark:bg-sage-800/30 text-sm font-medium text-sage-700 dark:text-sage-300 border border-sage-200 dark:border-sage-700">
                                            <x-icon name="shopping-bag" class="w-3.5 h-3.5" />
                                            {{ $orderCount }}
                                        </span>
                                    @else
                                        <span class="text-secondary opacity-40 text-xs">No orders</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right font-mono-num font-semibold text-primary">
                                    {{ $customer->totalSpent()->formatted() }}
                                </td>
                                <td class="px-6 py-4 text-secondary text-xs">
                                    <div class="flex items-center gap-1.5">
                                        <x-icon name="clock" class="w-3 h-3 text-secondary opacity-40" />
                                        {{ $customer->created_at->diffForHumans() }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right" onclick="event.stopPropagation()">
                                    <div class="flex justify-end gap-1">
                                        <a href="{{ route('admin.customers.show', $customer) }}"
                                            class="p-1.5 rounded-lg text-secondary hover:bg-sage-100 dark:hover:bg-sage-800/30 hover:text-sage-700 dark:hover:text-sage-300 transition"
                                            title="View Details">
                                            <x-icon name="eye" class="w-4 h-4" />
                                        </a>
                                        @can('customers.update')
                                            <button type="button" data-modal-target="edit-customer-{{ $customer->id }}"
                                                class="p-1.5 rounded-lg text-secondary hover:bg-sage-100 dark:hover:bg-sage-800/30 hover:text-sage-700 dark:hover:text-sage-300 transition"
                                                title="Edit">
                                                <x-icon name="pencil" class="w-4 h-4" />
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>

                            {{-- Edit Modal --}}
                            <x-modal id="edit-customer-{{ $customer->id }}" title="Edit Customer"
                                description="Update customer information" icon="pencil">
                                <form method="POST" action="{{ route('admin.customers.update', $customer) }}">
                                    @csrf @method('PUT')
                                    @include('admin.customers._fields', ['customer' => $customer])
                                    <div class="mt-4 flex justify-end gap-2">
                                        <button type="button" data-modal-close="edit-customer-{{ $customer->id }}"
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
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-20 h-20 rounded-2xl bg-sage-100/30 dark:bg-sage-800/20 flex items-center justify-center mb-4">
                                            <x-icon name="users" class="w-10 h-10 text-secondary opacity-30" />
                                        </div>
                                        <p class="text-lg font-medium text-primary">No customers found</p>
                                        <p class="text-sm text-secondary mt-1">
                                            @if (request()->has('search'))
                                                Try adjusting your search filters
                                            @else
                                                Start by adding your first customer
                                            @endif
                                        </p>
                                        @can('customers.create')
                                            <button type="button" data-modal-target="create-customer"
                                                class="inline-flex items-center gap-2 mt-4 rounded-xl bg-sage-600 hover:bg-sage-700 dark:bg-sage-500 dark:hover:bg-sage-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm hover:shadow-md transition-all duration-200">
                                                <x-icon name="plus" class="w-4 h-4" />
                                                Add Customer
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($customers->hasPages())
                <div class="border-t border-theme px-6 py-4 flex items-center justify-between">
                    <div class="text-sm text-secondary">
                        Showing <span class="font-medium text-primary">{{ $customers->firstItem() ?? 0 }}</span>
                        to <span class="font-medium text-primary">{{ $customers->lastItem() ?? 0 }}</span>
                        of <span class="font-medium text-primary">{{ $customers->total() }}</span> customers
                    </div>
                    <div>
                        {{ $customers->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Create Modal --}}
    <x-modal id="create-customer" title="Add Customer" description="Create a new customer" icon="plus">
        <form method="POST" action="{{ route('admin.customers.store') }}">
            @csrf
            @include('admin.customers._fields')
            <div class="mt-4 flex justify-end gap-2">
                <button type="button" data-modal-close="create-customer"
                    class="rounded-xl border border-theme text-sm font-medium px-5 py-2 text-secondary hover:bg-sage-50 dark:hover:bg-sage-900/20 hover:text-primary transition">
                    Cancel
                </button>
                <button type="submit"
                    class="rounded-xl bg-sage-600 hover:bg-sage-700 dark:bg-sage-500 dark:hover:bg-sage-600 text-sm font-medium px-5 py-2 text-white shadow-sm hover:shadow-md transition flex items-center gap-2">
                    <x-icon name="plus" class="w-4 h-4" />
                    Create Customer
                </button>
            </div>
        </form>
    </x-modal>
@endsection
