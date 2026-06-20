@extends('layouts.admin')

@section('page-title', 'POS Registers')

@section('content')
    <div class="space-y-5">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-slate-900">POS Registers</h2>
                <p class="text-sm text-slate-500 mt-0.5">Pair physical devices to warehouses for offline-capable checkout.
                </p>
            </div>
            <button type="button" data-modal-target="create-register"
                class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 px-4 py-2.5 text-sm font-medium text-white shadow-sm">
                <x-icon name="plus" class="w-4 h-4" /> Add Register
            </button>
        </div>

        @if (session('new_register_token'))
            <div class="rounded-lg bg-indigo-50 border border-indigo-200 px-4 py-3">
                <p class="text-sm font-medium text-indigo-900">Pairing token (shown once — copy it now):</p>
                <div class="mt-2 flex items-center gap-2">
                    <code id="new-token-display"
                        class="flex-1 bg-white border border-indigo-200 rounded-lg px-3 py-2 text-sm font-mono-num text-indigo-700 select-all">{{ session('new_register_token') }}</code>
                    <button type="button"
                        onclick="navigator.clipboard.writeText(document.getElementById('new-token-display').textContent)"
                        class="rounded-lg border border-indigo-300 text-indigo-700 hover:bg-indigo-100 text-sm font-medium px-3 py-2">Copy</button>
                </div>
                <p class="text-xs text-indigo-700 mt-2">Enter this token in the "Pair This Device" prompt the first time the
                    POS register screen loads on the target device.</p>
            </div>
        @endif

        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-slate-500">Register</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-500">Code</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-500">Warehouse</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-500">Last Seen</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-500">Status</th>
                        <th class="px-4 py-3 text-right font-medium text-slate-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($registers as $register)
                        <tr class="hover:bg-slate-50/75">
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $register->name }}</td>
                            <td class="px-4 py-3 font-mono-num text-slate-600">{{ $register->code }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $register->warehouse->name }}</td>
                            <td class="px-4 py-3 text-slate-500">
                                {{ $register->last_seen_at?->diffForHumans() ?? 'Never connected' }}
                            </td>
                            <td class="px-4 py-3">
                                <x-badge :color="$register->is_active ? 'green' : 'slate'">{{ $register->is_active ? 'Active' : 'Deactivated' }}</x-badge>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-2">
                                    <form method="POST" action="{{ route('admin.registers.regenerate-token', $register) }}"
                                        onsubmit="return confirm('This device will need to be re-paired with the new token. Continue?');">
                                        @csrf
                                        <button type="submit"
                                            class="text-xs font-medium text-indigo-600 hover:text-indigo-800">Regenerate
                                            Token</button>
                                    </form>
                                    @if ($register->is_active)
                                        <form method="POST" action="{{ route('admin.registers.deactivate', $register) }}">
                                            @csrf
                                            <button type="submit"
                                                class="text-xs font-medium text-red-600 hover:text-red-800">Deactivate</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-slate-500">No registers configured yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <x-modal id="create-register" title="Add Register">
        <form method="POST" action="{{ route('admin.registers.store') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Register Name</label>
                    <input type="text" name="name" placeholder="e.g. Front Counter" required
                        class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Code</label>
                    <input type="text" name="code" placeholder="e.g. WH01-REG01" required
                        class="w-full rounded-lg border-slate-300 text-sm font-mono-num focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Warehouse</label>
                    <select name="warehouse_id" required
                        class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @foreach (\App\Models\Warehouse::active()->orderBy('name')->get() as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mt-4 flex justify-end gap-2">
                <button type="button" data-modal-close="create-register"
                    class="rounded-lg border border-slate-300 text-sm font-medium px-4 py-2 text-slate-600 hover:bg-slate-50">Cancel</button>
                <button type="submit"
                    class="rounded-lg bg-indigo-600 hover:bg-indigo-500 text-sm font-medium px-4 py-2 text-white">Create</button>
            </div>
        </form>
    </x-modal>
@endsection
