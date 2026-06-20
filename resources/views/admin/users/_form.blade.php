@csrf
<div class="space-y-4">
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Name <span class="text-red-500">*</span></label>
        <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" required
            class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
        @error('name')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Email <span
                    class="text-red-500">*</span></label>
            <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" required
                class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            @error('email')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Phone</label>
            <input type="text" name="phone" value="{{ old('phone', $user->phone ?? '') }}"
                class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">
            Password @if (isset($user))
                <span class="text-slate-400 font-normal">(leave blank to keep current password)</span>
            @else
                <span class="text-red-500">*</span>
            @endif
        </label>
        <input type="password" name="password"
            class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
            {{ isset($user) ? '' : 'required' }}>
        @error('password')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Role <span class="text-red-500">*</span></label>
        <select name="role" required
            class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">Select role...</option>
            @php
                $selectedRole = old('role', isset($user) ? $user->roles->first()?->name : '');
            @endphp

            @foreach ($roles as $role)
                <option value="{{ $role->name }}" @selected($selectedRole === $role->name)>
                    {{ ucwords(str_replace('_', ' ', $role->name)) }}
                </option>
            @endforeach
        </select>
        @error('role')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>
    <label class="flex items-center gap-2 text-sm text-slate-700">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $user->is_active ?? true))
            class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
        Active (uncheck to immediately log this user out and block sign-in)
    </label>
</div>
