@extends('layouts.admin')

@section('page-title', 'Users')

@section('content')
    <div class="space-y-5">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-slate-900">Users</h2>
                <p class="text-sm text-slate-500 mt-0.5">{{ $users->total() }} users</p>
            </div>
            <div class="flex gap-2">
                @can('roles.manage')
                    <a href="{{ route('admin.roles.index') }}"
                        class="rounded-lg border border-slate-300 text-sm font-medium px-4 py-2.5 text-slate-600 hover:bg-slate-50">Manage
                        Roles</a>
                @endcan
                @can('create', \App\Models\User::class)
                    <a href="{{ route('admin.users.create') }}"
                        class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 px-4 py-2.5 text-sm font-medium text-white shadow-sm">
                        <x-icon name="plus" class="w-4 h-4" /> Add User
                    </a>
                @endcan
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-slate-500">User</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-500">Role</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-500">Phone</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-500">Status</th>
                        <th class="px-4 py-3 text-right font-medium text-slate-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($users as $user)
                        <tr class="hover:bg-slate-50/75">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $user->avatarUrl() }}" class="w-8 h-8 rounded-full">
                                    <div>
                                        <p class="font-medium text-slate-900">{{ $user->name }}</p>
                                        <p class="text-xs text-slate-400">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <x-badge
                                    color="indigo">{{ ucwords(str_replace('_', ' ', $user->roles->first()?->name ?? 'No role')) }}</x-badge>
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $user->phone ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <x-badge :color="$user->is_active ? 'green' : 'slate'">{{ $user->is_active ? 'Active' : 'Inactive' }}</x-badge>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-1">
                                    @can('update', $user)
                                        <a href="{{ route('admin.users.edit', $user) }}"
                                            class="p-1.5 rounded-md text-slate-500 hover:bg-slate-100 hover:text-indigo-600">
                                            <x-icon name="pencil" class="w-4 h-4" />
                                        </a>
                                    @endcan
                                    @can('delete', $user)
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                            onsubmit="return confirm('Delete this user?');">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="p-1.5 rounded-md text-slate-500 hover:bg-red-50 hover:text-red-600">
                                                <x-icon name="trash" class="w-4 h-4" />
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-slate-500">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if ($users->hasPages())
                <div class="border-t border-slate-200 px-4 py-3">{{ $users->links() }}</div>
            @endif
        </div>
    </div>
@endsection
