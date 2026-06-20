@extends('layouts.admin')

@section('page-title', 'Customers')

@section('content')
    <div class="space-y-5">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-slate-900">Customers</h2>
                <p class="text-sm text-slate-500 mt-0.5">{{ $customers->total() }} customers</p>
            </div>
            @can('customers.create')
                <button type="button" data-modal-target="create-customer"
                    class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 px-4 py-2.5 text-sm font-medium text-white shadow-sm">
                    <x-icon name="plus" class="w-4 h-4" /> Add Customer
                </button>
            @endcan
        </div>

        <form method="GET" class="bg-white rounded-xl border border-slate-200 p-4 flex gap-3">
            <div class="relative flex-1">
                <x-icon name="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                    placeholder="Search by name, phone, or email..."
                    class="w-full rounded-lg border-slate-300 pl-9 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <button type="submit"
                class="rounded-lg bg-slate-800 hover:bg-slate-700 text-white text-sm font-medium px-4 py-2">Search</button>
        </form>

        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-slate-500">Name</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-500">Phone</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-500">Email</th>
                        <th class="px-4 py-3 text-right font-medium text-slate-500">Total Spent</th>
                        <th class="px-4 py-3 text-right font-medium text-slate-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($customers as $customer)
                        <tr class="hover:bg-slate-50/75 cursor-pointer"
                            onclick="window.location='{{ route('admin.customers.show', $customer) }}'">
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $customer->name }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $customer->phone ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $customer->email ?? '—' }}</td>
                            <td class="px-4 py-3 text-right font-mono-num font-medium text-slate-900">
                                {{ $customer->totalSpent()->formatted() }}</td>
                            <td class="px-4 py-3 text-right" onclick="event.stopPropagation()">
                                <div class="flex justify-end gap-1">
                                    <button type="button" data-modal-target="edit-customer-{{ $customer->id }}"
                                        class="p-1.5 rounded-md text-slate-500 hover:bg-slate-100 hover:text-indigo-600">
                                        <x-icon name="pencil" class="w-4 h-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <x-modal id="edit-customer-{{ $customer->id }}" title="Edit Customer">
                            <form method="POST" action="{{ route('admin.customers.update', $customer) }}">
                                @csrf @method('PUT')
                                @include('admin.customers._fields', ['customer' => $customer])
                                <div class="mt-4 flex justify-end gap-2">
                                    <button type="button" data-modal-close="edit-customer-{{ $customer->id }}"
                                        class="rounded-lg border border-slate-300 text-sm font-medium px-4 py-2 text-slate-600 hover:bg-slate-50">Cancel</button>
                                    <button type="submit"
                                        class="rounded-lg bg-indigo-600 hover:bg-indigo-500 text-sm font-medium px-4 py-2 text-white">Save</button>
                                </div>
                            </form>
                        </x-modal>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-slate-500">No customers yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if ($customers->hasPages())
                <div class="border-t border-slate-200 px-4 py-3">{{ $customers->links() }}</div>
            @endif
        </div>
    </div>

    <x-modal id="create-customer" title="Add Customer">
        <form method="POST" action="{{ route('admin.customers.store') }}">
            @csrf
            @include('admin.customers._fields')
            <div class="mt-4 flex justify-end gap-2">
                <button type="button" data-modal-close="create-customer"
                    class="rounded-lg border border-slate-300 text-sm font-medium px-4 py-2 text-slate-600 hover:bg-slate-50">Cancel</button>
                <button type="submit"
                    class="rounded-lg bg-indigo-600 hover:bg-indigo-500 text-sm font-medium px-4 py-2 text-white">Create</button>
            </div>
        </form>
    </x-modal>
@endsection
