@extends('layouts.admin')

@section('page-title', 'My Profile')

@section('content')
    <div class="space-y-5">

        {{-- Profile info --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <h3 class="font-semibold text-slate-900 mb-4">Profile Information</h3>
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf @method('PUT')

                <div class="flex items-center gap-4">
                    <img src="{{ $user->avatarUrl() }}" class="w-16 h-16 rounded-full object-cover border border-slate-200">
                    <div>
                        <input type="file" name="avatar" accept="image/*" class="text-sm">
                        @error('avatar')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                            class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('email')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                            class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit"
                        class="rounded-lg bg-indigo-600 hover:bg-indigo-500 text-sm font-medium px-5 py-2.5 text-white">Save
                        Changes</button>
                </div>
            </form>
        </div>

        {{-- Change password --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <h3 class="font-semibold text-slate-900 mb-4">Change Password</h3>
            <form method="POST" action="{{ route('profile.password.update') }}" class="space-y-4">
                @csrf @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Current Password</label>
                    <input type="password" name="current_password" required
                        class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('current_password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">New Password</label>
                        <input type="password" name="password" required
                            class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('password')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Confirm New Password</label>
                        <input type="password" name="password_confirmation" required
                            class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>
                <div class="pt-2">
                    <button type="submit"
                        class="rounded-lg bg-indigo-600 hover:bg-indigo-500 text-sm font-medium px-5 py-2.5 text-white">Update
                        Password</button>
                </div>
            </form>
        </div>

        {{-- Delete account --}}
        <div class="bg-white rounded-xl border border-red-200 p-5">
            <h3 class="font-semibold text-red-700 mb-1">Delete Account</h3>
            <p class="text-sm text-slate-500 mb-4">This will deactivate your account. Your historical sales and activity
                records are preserved.</p>
            <button type="button" data-modal-target="delete-account"
                class="rounded-lg border border-red-300 text-red-600 hover:bg-red-50 text-sm font-medium px-4 py-2">Delete
                Account</button>
        </div>
    </div>

    <x-modal id="delete-account" title="Delete Account">
        <form method="POST" action="{{ route('profile.destroy') }}">
            @csrf @method('DELETE')
            <p class="text-sm text-slate-600 mb-3">Enter your password to confirm.</p>
            <input type="password" name="password" required
                class="w-full rounded-lg border-slate-300 text-sm focus:border-red-500 focus:ring-red-500">
            @error('password')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
            <div class="mt-4 flex justify-end gap-2">
                <button type="button" data-modal-close="delete-account"
                    class="rounded-lg border border-slate-300 text-sm font-medium px-4 py-2 text-slate-600 hover:bg-slate-50">Cancel</button>
                <button type="submit"
                    class="rounded-lg bg-red-600 hover:bg-red-500 text-sm font-medium px-4 py-2 text-white">Delete
                    Account</button>
            </div>
        </form>
    </x-modal>
@endsection
