@extends('layouts.admin')

@section('page-title', 'Roles & Permissions')

@section('content')
    <div class="space-y-5">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-slate-900">Roles & Permissions</h2>
                <p class="text-sm text-slate-500 mt-0.5">Control what each role can access throughout the system.</p>
            </div>
            <button type="button" data-modal-target="create-role"
                class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 px-4 py-2.5 text-sm font-medium text-white shadow-sm">
                <x-icon name="plus" class="w-4 h-4" /> Add Role
            </button>
        </div>

        <div class="flex flex-wrap gap-2" id="role-tabs">
            @foreach ($roles as $role)
                <button type="button" data-role-tab="{{ $role->id }}"
                    class="role-tab-btn rounded-lg px-4 py-2 text-sm font-medium border {{ $loop->first ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-slate-600 border-slate-300 hover:bg-slate-50' }}">
                    {{ ucwords(str_replace('_', ' ', $role->name)) }}
                    <span class="ml-1 text-xs opacity-70">({{ $role->users_count }})</span>
                </button>
            @endforeach
        </div>

        @foreach ($roles as $role)
            <div data-role-panel="{{ $role->id }}"
                class="role-panel {{ $loop->first ? '' : 'hidden' }} bg-white rounded-xl border border-slate-200 p-5">
                @if ($role->name === 'admin')
                    <div
                        class="flex items-start gap-2 rounded-lg bg-indigo-50 border border-indigo-200 px-4 py-3 text-sm text-indigo-800 mb-4">
                        <x-icon name="shield-check" class="w-4 h-4 mt-0.5 shrink-0" />
                        <span>The <strong>admin</strong> role always has full access to every permission and cannot be
                            modified.</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.roles.permissions.update', $role) }}">
                    @csrf @method('PUT')
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-5">
                        @foreach ($permissions as $module => $modulePermissions)
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">
                                    {{ str_replace('-', ' ', $module) }}</p>
                                <div class="space-y-1.5">
                                    @foreach ($modulePermissions as $permission)
                                        <label class="flex items-center gap-2 text-sm text-slate-700">
                                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                                {{ $role->name === 'admin' ? 'checked disabled' : '' }}
                                                @checked($role->hasPermissionTo($permission))
                                                class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                            {{ str_replace([$module . '.', '-'], ['', ' '], $permission->name) }}
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if ($role->name !== 'admin')
                        <div class="mt-6 pt-4 border-t border-slate-100 flex justify-between items-center">
                            @if (!in_array($role->name, ['admin', 'manager', 'cashier', 'stock_clerk']))
                                <form method="POST" action="{{ route('admin.roles.destroy', $role) }}"
                                    onsubmit="return confirm('Delete this role?');">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="text-sm font-medium text-red-600 hover:text-red-800">Delete Role</button>
                                </form>
                            @else
                                <span></span>
                            @endif
                            <button type="submit"
                                class="rounded-lg bg-indigo-600 hover:bg-indigo-500 text-sm font-medium px-5 py-2.5 text-white">Save
                                Permissions</button>
                        </div>
                    @endif
                </form>
            </div>
        @endforeach
    </div>

    <x-modal id="create-role" title="Add Role">
        <form method="POST" action="{{ route('admin.roles.store') }}">
            @csrf
            <label class="block text-sm font-medium text-slate-700 mb-1">Role Name (lowercase, underscores only)</label>
            <input type="text" name="name" placeholder="e.g. delivery_driver" pattern="[a-z_]+" required
                class="w-full rounded-lg border-slate-300 text-sm font-mono-num focus:border-indigo-500 focus:ring-indigo-500">
            <div class="mt-4 flex justify-end gap-2">
                <button type="button" data-modal-close="create-role"
                    class="rounded-lg border border-slate-300 text-sm font-medium px-4 py-2 text-slate-600 hover:bg-slate-50">Cancel</button>
                <button type="submit"
                    class="rounded-lg bg-indigo-600 hover:bg-indigo-500 text-sm font-medium px-4 py-2 text-white">Create</button>
            </div>
        </form>
    </x-modal>
@endsection

@push('scripts')
    <script>
        $(function() {
            $('.role-tab-btn').on('click', function() {
                const roleId = $(this).data('role-tab');
                $('.role-tab-btn').removeClass('bg-indigo-600 text-white border-indigo-600').addClass(
                    'bg-white text-slate-600 border-slate-300');
                $(this).removeClass('bg-white text-slate-600 border-slate-300').addClass(
                    'bg-indigo-600 text-white border-indigo-600');
                $('.role-panel').addClass('hidden');
                $(`[data-role-panel="${roleId}"]`).removeClass('hidden');
            });
        });
    </script>
@endpush
