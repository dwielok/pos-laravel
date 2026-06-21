@extends('layouts.admin')

@section('page-title', 'My Profile')
@section('breadcrumb', 'Account Settings')

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-primary-green-light text-primary-green flex items-center justify-center">
                <x-icon name="user" class="w-5 h-5" />
            </div>
            <div>
                <h2 class="text-xl font-semibold text-primary">My Profile</h2>
                <p class="text-sm text-secondary">Manage your account settings and preferences</p>
            </div>
        </div>

        {{-- Profile Information --}}
        <div class="bg-card rounded-2xl border border-theme p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-8 h-8 rounded-xl bg-primary-green-light text-primary-green flex items-center justify-center">
                    <x-icon name="user-circle" class="w-4 h-4" />
                </div>
                <h3 class="font-semibold text-primary">Profile Information</h3>
                <span class="ml-auto text-xs text-secondary bg-primary-green-light/20 px-2.5 py-1 rounded-full">Personal
                    details</span>
            </div>

            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf @method('PUT')

                {{-- Avatar --}}
                <div
                    class="flex flex-col sm:flex-row sm:items-center gap-4 p-4 bg-primary-green-light/10 rounded-xl border border-theme">
                    <div class="flex items-center gap-4">
                        <div class="relative group">
                            <img src="{{ $user->avatarUrl() }}"
                                class="w-20 h-20 rounded-full object-cover border-2 border-primary-green shadow-sm group-hover:shadow-md transition">
                            <div
                                class="absolute inset-0 rounded-full bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                <x-icon name="camera" class="w-6 h-6 text-white" />
                            </div>
                        </div>
                        <div>
                            <p class="font-medium text-primary">{{ $user->name }}</p>
                            <p class="text-sm text-secondary">{{ $user->email }}</p>
                            <p class="text-xs text-secondary opacity-60">
                                {{ ucfirst($user->roles->first()->name ?? 'User') }}</p>
                        </div>
                    </div>
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-xs font-medium text-secondary mb-1.5">Change Avatar</label>
                        <input type="file" name="avatar" accept="image/*"
                            class="w-full text-sm text-secondary file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-medium file:bg-primary-green-light file:text-primary-green hover:file:bg-primary-green-light/50 transition">
                        @error('avatar')
                            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                <x-icon name="alert-circle" class="w-3.5 h-3.5" />
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="mt-1 text-xs text-secondary opacity-60">PNG, JPG, or WEBP (max 2MB)</p>
                    </div>
                </div>

                {{-- Name & Email --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-secondary mb-1.5">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-secondary opacity-40">
                                <x-icon name="user" class="w-4 h-4" />
                            </div>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                placeholder="Your full name"
                                class="w-full rounded-xl border-theme pl-9 pr-4 py-2.5 bg-primary-green-light/10 text-sm focus:ring-2 focus:ring-primary-green focus:border-transparent transition @error('name') border-red-500 ring-2 ring-red-500 @enderror">
                        </div>
                        @error('name')
                            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                <x-icon name="alert-circle" class="w-3.5 h-3.5" />
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-secondary mb-1.5">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-secondary opacity-40">
                                <x-icon name="mail" class="w-4 h-4" />
                            </div>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                placeholder="you@example.com"
                                class="w-full rounded-xl border-theme pl-9 pr-4 py-2.5 bg-primary-green-light/10 text-sm focus:ring-2 focus:ring-primary-green focus:border-transparent transition @error('email') border-red-500 ring-2 ring-red-500 @enderror">
                        </div>
                        @error('email')
                            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                <x-icon name="alert-circle" class="w-3.5 h-3.5" />
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                {{-- Phone --}}
                <div>
                    <label class="block text-sm font-medium text-secondary mb-1.5">Phone Number</label>
                    <div class="relative">
                        <div class="absolute left-3 top-1/2 -translate-y-1/2 text-secondary opacity-40">
                            <x-icon name="phone" class="w-4 h-4" />
                        </div>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                            placeholder="+62 812 3456 7890"
                            class="w-full rounded-xl border-theme pl-9 pr-4 py-2.5 bg-primary-green-light/10 text-sm focus:ring-2 focus:ring-primary-green focus:border-transparent transition">
                    </div>
                </div>

                {{-- Save Button --}}
                <div class="pt-2 border-t border-theme flex justify-end">
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-primary-green hover:bg-primary-green-dark text-sm font-medium px-6 py-2.5 text-white shadow-sm hover:shadow-md transition group">
                        <x-icon name="check" class="w-4 h-4 group-hover:scale-110 transition-transform duration-300" />
                        Save Changes
                    </button>
                </div>
            </form>
        </div>

        {{-- Change Password --}}
        <div class="bg-card rounded-2xl border border-theme p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center gap-3 mb-5">
                <div
                    class="w-8 h-8 rounded-xl bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400 flex items-center justify-center">
                    <x-icon name="shield-check" class="w-4 h-4" />
                </div>
                <h3 class="font-semibold text-primary">Change Password</h3>
                <span
                    class="ml-auto text-xs text-secondary bg-amber-100/50 dark:bg-amber-900/20 px-2.5 py-1 rounded-full">Security</span>
            </div>

            <form method="POST" action="{{ route('profile.password.update') }}" class="space-y-4">
                @csrf @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-secondary mb-1.5">Current Password <span
                            class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute left-3 top-1/2 -translate-y-1/2 text-secondary opacity-40">
                            <x-icon name="lock" class="w-4 h-4" />
                        </div>
                        <input type="password" name="current_password" required placeholder="Enter your current password"
                            class="w-full rounded-xl border-theme pl-9 pr-4 py-2.5 bg-primary-green-light/10 text-sm focus:ring-2 focus:ring-primary-green focus:border-transparent transition @error('current_password') border-red-500 ring-2 ring-red-500 @enderror">
                    </div>
                    @error('current_password')
                        <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                            <x-icon name="alert-circle" class="w-3.5 h-3.5" />
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-secondary mb-1.5">New Password <span
                                class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-secondary opacity-40">
                                <x-icon name="lock" class="w-4 h-4" />
                            </div>
                            <input type="password" name="password" required placeholder="Enter new password"
                                class="w-full rounded-xl border-theme pl-9 pr-4 py-2.5 bg-primary-green-light/10 text-sm focus:ring-2 focus:ring-primary-green focus:border-transparent transition @error('password') border-red-500 ring-2 ring-red-500 @enderror">
                        </div>
                        @error('password')
                            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                <x-icon name="alert-circle" class="w-3.5 h-3.5" />
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="mt-1 text-xs text-secondary opacity-60">Minimum 8 characters</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-secondary mb-1.5">Confirm New Password <span
                                class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-secondary opacity-40">
                                <x-icon name="lock" class="w-4 h-4" />
                            </div>
                            <input type="password" name="password_confirmation" required
                                placeholder="Confirm new password"
                                class="w-full rounded-xl border-theme pl-9 pr-4 py-2.5 bg-primary-green-light/10 text-sm focus:ring-2 focus:ring-primary-green focus:border-transparent transition">
                        </div>
                    </div>
                </div>

                {{-- Password Strength Indicator --}}
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-medium text-secondary">Password strength:</span>
                        <span id="password-strength" class="text-xs font-medium text-secondary opacity-60">Enter a
                            password</span>
                    </div>
                    <div class="flex gap-1 h-1">
                        <div class="flex-1 rounded-full bg-slate-200 dark:bg-slate-700 transition" id="strength-1"></div>
                        <div class="flex-1 rounded-full bg-slate-200 dark:bg-slate-700 transition" id="strength-2"></div>
                        <div class="flex-1 rounded-full bg-slate-200 dark:bg-slate-700 transition" id="strength-3"></div>
                        <div class="flex-1 rounded-full bg-slate-200 dark:bg-slate-700 transition" id="strength-4"></div>
                    </div>
                </div>

                <div class="pt-2 border-t border-theme flex justify-end">
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-primary-green hover:bg-primary-green-dark text-sm font-medium px-6 py-2.5 text-white shadow-sm hover:shadow-md transition group">
                        <x-icon name="shield-check"
                            class="w-4 h-4 group-hover:scale-110 transition-transform duration-300" />
                        Update Password
                    </button>
                </div>
            </form>
        </div>

        {{-- Delete Account --}}
        <div
            class="bg-card rounded-2xl border-2 border-red-200/50 dark:border-red-800/30 p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-start gap-3">
                    <div
                        class="w-10 h-10 rounded-xl bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400 flex items-center justify-center flex-shrink-0">
                        <x-icon name="alert-triangle" class="w-5 h-5" />
                    </div>
                    <div>
                        <h3 class="font-semibold text-red-700 dark:text-red-400">Delete Account</h3>
                        <p class="text-sm text-secondary">This will deactivate your account. Your historical sales and
                            activity records are preserved.</p>
                    </div>
                </div>
                <button type="button" data-modal-target="delete-account"
                    class="inline-flex items-center gap-2 rounded-xl border border-red-300 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 text-sm font-medium px-5 py-2.5 transition flex-shrink-0">
                    <x-icon name="trash" class="w-4 h-4" />
                    Delete Account
                </button>
            </div>
        </div>
    </div>

    {{-- Delete Account Modal --}}
    <x-modal id="delete-account" title="Delete Account" description="This action cannot be undone" icon="danger">
        <form method="POST" action="{{ route('profile.destroy') }}">
            @csrf @method('DELETE')

            <div class="space-y-4">
                <div
                    class="flex items-start gap-4 p-4 bg-red-50/50 dark:bg-red-900/10 rounded-xl border border-red-200 dark:border-red-800/50">
                    <div
                        class="flex-shrink-0 w-10 h-10 rounded-xl bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 flex items-center justify-center">
                        <x-icon name="alert-triangle" class="w-5 h-5" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-red-800 dark:text-red-200">
                            You are about to delete your account
                        </p>
                        <p class="text-xs text-red-600/70 dark:text-red-300/70 mt-1">
                            This action cannot be undone. All your data will be permanently removed.
                        </p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-secondary mb-1.5">
                        Confirm Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute left-3 top-1/2 -translate-y-1/2 text-secondary opacity-40">
                            <x-icon name="lock" class="w-4 h-4" />
                        </div>
                        <input type="password" name="password" required placeholder="Enter your password to confirm"
                            class="w-full rounded-xl border-theme pl-9 pr-4 py-2.5 bg-primary-green-light/10 text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent transition @error('password') border-red-500 ring-2 ring-red-500 @enderror">
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                            <x-icon name="alert-circle" class="w-3.5 h-3.5" />
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="pt-2 border-t border-theme flex justify-end gap-2">
                    <button type="button" data-modal-close="delete-account"
                        class="rounded-xl border border-theme text-sm font-medium px-5 py-2 text-secondary hover:bg-primary-green-light hover:text-primary transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="rounded-xl bg-red-600 hover:bg-red-700 text-sm font-medium px-5 py-2 text-white shadow-sm hover:shadow-md transition flex items-center gap-2">
                        <x-icon name="trash" class="w-4 h-4" />
                        Delete Account
                    </button>
                </div>
            </div>
        </form>
    </x-modal>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password strength indicator
            const passwordInput = document.querySelector('input[name="password"]');
            const strengthText = document.getElementById('password-strength');
            const bars = [
                document.getElementById('strength-1'),
                document.getElementById('strength-2'),
                document.getElementById('strength-3'),
                document.getElementById('strength-4')
            ];

            if (passwordInput) {
                passwordInput.addEventListener('input', function() {
                    const password = this.value;
                    const strength = calculatePasswordStrength(password);

                    // Update bars
                    bars.forEach((bar, index) => {
                        if (index < strength.score) {
                            bar.classList.remove('bg-slate-200', 'dark:bg-slate-700');
                            bar.classList.add(strength.color);
                        } else {
                            bar.classList.remove('bg-emerald-500', 'bg-amber-500', 'bg-red-500');
                            bar.classList.add('bg-slate-200', 'dark:bg-slate-700');
                        }
                    });

                    // Update text
                    strengthText.textContent = strength.label;
                    strengthText.className = 'text-xs font-medium ' + strength.textColor;
                });
            }

            function calculatePasswordStrength(password) {
                let score = 0;
                const hasLower = /[a-z]/.test(password);
                const hasUpper = /[A-Z]/.test(password);
                const hasNumber = /\d/.test(password);
                const hasSpecial = /[^A-Za-z0-9]/.test(password);
                const length = password.length;

                if (length >= 8) score++;
                if (length >= 12) score++;
                if (hasLower && hasUpper) score++;
                if (hasNumber) score++;
                if (hasSpecial) score++;

                // Normalize to 0-4
                score = Math.min(4, Math.floor(score / 1.5));

                const strengths = [{
                        label: 'Very Weak',
                        color: 'bg-red-500',
                        textColor: 'text-red-500'
                    },
                    {
                        label: 'Weak',
                        color: 'bg-orange-500',
                        textColor: 'text-orange-500'
                    },
                    {
                        label: 'Fair',
                        color: 'bg-amber-500',
                        textColor: 'text-amber-500'
                    },
                    {
                        label: 'Good',
                        color: 'bg-emerald-500',
                        textColor: 'text-emerald-500'
                    },
                    {
                        label: 'Strong',
                        color: 'bg-emerald-600',
                        textColor: 'text-emerald-600'
                    }
                ];

                const result = strengths[score] || strengths[0];
                return {
                    score: score + 1,
                    label: password.length === 0 ? 'Enter a password' : result.label,
                    color: result.color,
                    textColor: password.length === 0 ? 'text-secondary opacity-60' : result.textColor
                };
            }
        });
    </script>
@endpush
