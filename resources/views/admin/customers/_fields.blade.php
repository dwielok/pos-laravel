<div class="space-y-4">
    <div>
        <label class="block text-sm font-medium text-secondary mb-1.5">
            Name <span class="text-red-500">*</span>
        </label>
        <div class="relative">
            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-secondary opacity-40">
                <x-icon name="user" class="w-4 h-4" />
            </div>
            <input type="text" name="name" value="{{ old('name', $customer->name ?? '') }}" required
                placeholder="Enter customer name..."
                class="w-full rounded-xl border-theme pl-9 pr-4 py-2.5 bg-sage-50/50 dark:bg-sage-900/20 text-primary text-sm focus:ring-2 focus:ring-sage-400 dark:focus:ring-sage-500 focus:border-sage-400 dark:focus:border-sage-500 transition @error('name') border-red-500 ring-2 ring-red-500 @enderror">
        </div>
        @error('name')
            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                <x-icon name="alert-circle" class="w-3.5 h-3.5" />
                {{ $message }}
            </p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-secondary mb-1.5">Phone</label>
        <div class="relative">
            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-secondary opacity-40">
                <x-icon name="phone" class="w-4 h-4" />
            </div>
            <input type="text" name="phone" value="{{ old('phone', $customer->phone ?? '') }}"
                placeholder="e.g. 08123456789"
                class="w-full rounded-xl border-theme pl-9 pr-4 py-2.5 bg-sage-50/50 dark:bg-sage-900/20 text-primary text-sm focus:ring-2 focus:ring-sage-400 dark:focus:ring-sage-500 focus:border-sage-400 dark:focus:border-sage-500 transition @error('phone') border-red-500 ring-2 ring-red-500 @enderror">
        </div>
        @error('phone')
            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                <x-icon name="alert-circle" class="w-3.5 h-3.5" />
                {{ $message }}
            </p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-secondary mb-1.5">Email</label>
        <div class="relative">
            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-secondary opacity-40">
                <x-icon name="mail" class="w-4 h-4" />
            </div>
            <input type="email" name="email" value="{{ old('email', $customer->email ?? '') }}"
                placeholder="customer@example.com"
                class="w-full rounded-xl border-theme pl-9 pr-4 py-2.5 bg-sage-50/50 dark:bg-sage-900/20 text-primary text-sm focus:ring-2 focus:ring-sage-400 dark:focus:ring-sage-500 focus:border-sage-400 dark:focus:border-sage-500 transition @error('email') border-red-500 ring-2 ring-red-500 @enderror">
        </div>
        @error('email')
            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                <x-icon name="alert-circle" class="w-3.5 h-3.5" />
                {{ $message }}
            </p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-secondary mb-1.5">Address</label>
        <div class="relative">
            <div class="absolute left-3 top-3 text-secondary opacity-40">
                <x-icon name="home" class="w-4 h-4" />
            </div>
            <textarea name="address" rows="3" placeholder="Enter customer address..."
                class="w-full rounded-xl border-theme pl-9 pr-4 py-2.5 bg-sage-50/50 dark:bg-sage-900/20 text-primary text-sm focus:ring-2 focus:ring-sage-400 dark:focus:ring-sage-500 focus:border-sage-400 dark:focus:border-sage-500 transition resize-none @error('address') border-red-500 ring-2 ring-red-500 @enderror">{{ old('address', $customer->address ?? '') }}</textarea>
        </div>
        @error('address')
            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                <x-icon name="alert-circle" class="w-3.5 h-3.5" />
                {{ $message }}
            </p>
        @enderror
    </div>

    <label class="flex items-center gap-3 text-sm text-secondary cursor-pointer group">
        <input type="checkbox" name="is_guest" value="1"
            @checked(old('is_guest', $customer->is_guest ?? false))
            class="w-4 h-4 rounded border-theme text-sage-600 dark:text-sage-400 focus:ring-sage-400 dark:focus:ring-sage-500 focus:ring-2 transition">
        <span class="group-hover:text-primary transition">Guest Customer</span>
        <span class="text-xs text-secondary opacity-60">(Guest customers don't have accounts)</span>
    </label>
</div>
