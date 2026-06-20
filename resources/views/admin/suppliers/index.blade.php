@extends('layouts.admin')

@section('page-title', 'Suppliers')

@section('content')
    <div class="space-y-5">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-slate-900">Suppliers</h2>
                <p class="text-sm text-slate-500 mt-0.5">{{ $suppliers->total() }} suppliers</p>
            </div>
            @can('suppliers.create')
                <button type="button" data-modal-target="create-supplier"
                    class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 px-4 py-2.5 text-sm font-medium text-white shadow-sm">
                    <x-icon name="plus" class="w-4 h-4" /> Add Supplier
                </button>
            @endcan
        </div>

        <form method="GET" class="bg-white rounded-xl border border-slate-200 p-4 flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <x-icon name="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                    placeholder="Search suppliers..."
                    class="w-full rounded-lg border-slate-300 pl-9 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <button type="submit"
                class="rounded-lg bg-slate-800 hover:bg-slate-700 text-white text-sm font-medium px-4 py-2">Search</button>
        </form>

        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-slate-500">Supplier</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-500">Contact</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-500">Phone / Email</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-500">Status</th>
                        <th class="px-4 py-3 text-right font-medium text-slate-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($suppliers as $supplier)
                        <tr class="hover:bg-slate-50/75">
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $supplier->name }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $supplier->contact_person ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-600">
                                <div>{{ $supplier->phone ?? '—' }}</div>
                                <div class="text-xs text-slate-400">{{ $supplier->email ?? '' }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <x-badge :color="$supplier->is_active ? 'green' : 'slate'">{{ $supplier->is_active ? 'Active' : 'Inactive' }}</x-badge>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-1">
                                    <button type="button" data-modal-target="edit-supplier-{{ $supplier->id }}"
                                        class="p-1.5 rounded-md text-slate-500 hover:bg-slate-100 hover:text-indigo-600">
                                        <x-icon name="pencil" class="w-4 h-4" />
                                    </button>
                                    <button type="button" data-modal-target="delete-supplier-{{ $supplier->id }}"
                                        class="p-1.5 rounded-md text-slate-500 hover:bg-red-50 hover:text-red-600">
                                        <x-icon name="trash" class="w-4 h-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <x-modal id="edit-supplier-{{ $supplier->id }}" title="Edit Supplier" maxWidth="lg">
                            <form method="POST" action="{{ route('admin.suppliers.update', $supplier) }}">
                                @csrf @method('PUT')
                                @include('admin.suppliers._fields', ['supplier' => $supplier])
                                <div class="mt-4 flex justify-end gap-2">
                                    <button type="button" data-modal-close="edit-supplier-{{ $supplier->id }}"
                                        class="rounded-lg border border-slate-300 text-sm font-medium px-4 py-2 text-slate-600 hover:bg-slate-50">Cancel</button>
                                    <button type="submit"
                                        class="rounded-lg bg-indigo-600 hover:bg-indigo-500 text-sm font-medium px-4 py-2 text-white">Save</button>
                                </div>
                            </form>
                        </x-modal>

                        <x-modal id="delete-supplier-{{ $supplier->id }}" title="Delete Supplier">
                            <p class="text-sm text-slate-600">Delete <strong>{{ $supplier->name }}</strong>?</p>
                            <form method="POST" action="{{ route('admin.suppliers.destroy', $supplier) }}"
                                class="mt-4 flex justify-end gap-2">
                                @csrf @method('DELETE')
                                <button type="button" data-modal-close="delete-supplier-{{ $supplier->id }}"
                                    class="rounded-lg border border-slate-300 text-sm font-medium px-4 py-2 text-slate-600 hover:bg-slate-50">Cancel</button>
                                <button type="submit"
                                    class="rounded-lg bg-red-600 hover:bg-red-500 text-sm font-medium px-4 py-2 text-white">Delete</button>
                            </form>
                        </x-modal>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-slate-500">No suppliers yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if ($suppliers->hasPages())
                <div class="border-t border-slate-200 px-4 py-3">{{ $suppliers->links() }}</div>
            @endif
        </div>
    </div>

    <x-modal id="create-supplier" title="Add Supplier" maxWidth="lg">
        <form method="POST" action="{{ route('admin.suppliers.store') }}">
            @csrf
            @include('admin.suppliers._fields')
            <div class="mt-4 flex justify-end gap-2">
                <button type="button" data-modal-close="create-supplier"
                    class="rounded-lg border border-slate-300 text-sm font-medium px-4 py-2 text-slate-600 hover:bg-slate-50">Cancel</button>
                <button type="submit"
                    class="rounded-lg bg-indigo-600 hover:bg-indigo-500 text-sm font-medium px-4 py-2 text-white">Create</button>
            </div>
        </form>
    </x-modal>
@endsection
