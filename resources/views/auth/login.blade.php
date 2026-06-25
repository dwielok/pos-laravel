<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'POS') }} - Login</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])

    <style>
        * {
            font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
        }

        .font-mono-num {
            font-family: 'JetBrains Mono', ui-monospace, monospace;
            font-variant-numeric: tabular-nums;
        }

        /* Sage theme colors */
        :root {
            --sage-50: #f6f8f4;
            --sage-100: #e8ede3;
            --sage-200: #d0dec9;
            --sage-300: #b3c9a8;
            --sage-400: #94b387;
            --sage-500: #779b68;
            --sage-600: #5e7e51;
            --sage-700: #47623d;
            --sage-800: #32482b;
            --sage-900: #1f2e1a;
        }

        body {
            font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
            background: linear-gradient(135deg, #f6f8f4 0%, #e8ede3 50%, #d0dec9 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            margin: 0;
        }

        /* Modern glass morphism card */
        .login-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(208, 222, 201, 0.4);
            box-shadow: 0 25px 50px -12px rgba(50, 72, 43, 0.15);
            border-radius: 24px;
            padding: 2.5rem;
            width: 100%;
            max-width: 420px;
            transition: all 0.3s ease;
        }

        .login-card:hover {
            box-shadow: 0 30px 60px -12px rgba(50, 72, 43, 0.2);
            transform: translateY(-2px);
        }

        /* Decorative elements */
        .bg-sage-gradient {
            background: linear-gradient(135deg, #f6f8f4 0%, #e8ede3 50%, #d0dec9 100%);
        }

        .input-sage {
            background: rgba(246, 248, 244, 0.6);
            border: 1px solid rgba(208, 222, 201, 0.5);
            transition: all 0.3s ease;
        }

        .input-sage:focus {
            background: rgba(255, 255, 255, 0.9);
            border-color: var(--sage-400);
            box-shadow: 0 0 0 3px rgba(119, 155, 104, 0.15);
            outline: none;
        }

        .input-sage:focus + .input-icon {
            color: var(--sage-600);
        }

        .input-icon {
            color: rgba(71, 98, 61, 0.4);
            transition: color 0.3s ease;
        }

        .btn-sage {
            background: var(--sage-600);
            color: white;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-sage:hover {
            background: var(--sage-700);
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(94, 126, 81, 0.3);
        }

        .btn-sage:active {
            transform: translateY(0);
        }

        /* Floating decoration */
        .decoration-circle {
            position: fixed;
            border-radius: 50%;
            background: rgba(208, 222, 201, 0.3);
            pointer-events: none;
            z-index: 0;
        }

        .decoration-circle-1 {
            width: 300px;
            height: 300px;
            top: -100px;
            right: -100px;
            animation: float 20s ease-in-out infinite;
        }

        .decoration-circle-2 {
            width: 200px;
            height: 200px;
            bottom: -50px;
            left: -50px;
            animation: float 25s ease-in-out infinite reverse;
        }

        .decoration-circle-3 {
            width: 150px;
            height: 150px;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            animation: pulse 15s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -30px) scale(1.05); }
            66% { transform: translate(-20px, 20px) scale(0.95); }
        }

        @keyframes pulse {
            0%, 100% { opacity: 0.3; transform: translate(-50%, -50%) scale(1); }
            50% { opacity: 0.5; transform: translate(-50%, -50%) scale(1.2); }
        }

        /* Checkbox styling */
        .checkbox-sage {
            appearance: none;
            width: 18px;
            height: 18px;
            border: 2px solid rgba(208, 222, 201, 0.6);
            border-radius: 6px;
            background: rgba(255, 255, 255, 0.5);
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
            flex-shrink: 0;
        }

        .checkbox-sage:checked {
            background: var(--sage-600);
            border-color: var(--sage-600);
        }

        .checkbox-sage:checked::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 12px;
            font-weight: 700;
        }

        .checkbox-sage:focus {
            box-shadow: 0 0 0 3px rgba(119, 155, 104, 0.2);
            outline: none;
        }

        /* Error styling */
        .input-error {
            color: #dc2626;
            font-size: 0.75rem;
            margin-top: 0.375rem;
            display: flex;
            align-items: center;
            gap: 0.375rem;
        }

        /* Responsive */
        @media (max-width: 640px) {
            .login-card {
                padding: 1.5rem;
                border-radius: 20px;
            }

            .decoration-circle-1,
            .decoration-circle-2,
            .decoration-circle-3 {
                display: none;
            }
        }
    </style>
</head>
<body>

    {{-- Decorative background elements --}}
    <div class="decoration-circle decoration-circle-1"></div>
    <div class="decoration-circle decoration-circle-2"></div>
    <div class="decoration-circle decoration-circle-3"></div>

    {{-- Main Card --}}
    <div class="login-card relative z-10">

        {{-- Brand --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-sage-100/80 text-sage-600 shadow-md mb-4">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-sage-800">{{ config('app.name', 'POS') }}</h2>
            <p class="mt-1 text-sm text-sage-600">Sign in to your account</p>
        </div>

        {{-- Session Status --}}
        @if (session('status'))
            <div class="mb-4 p-3 rounded-xl bg-sage-100/70 border border-sage-200 text-sage-700 text-sm flex items-center gap-2">
                <svg class="w-5 h-5 text-sage-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('status') }}
            </div>
        @endif

        {{-- Login Form --}}
        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-medium text-sage-700 mb-1.5">Email Address</label>
                <div class="relative">
                    <svg class="input-icon w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                        class="input-sage w-full rounded-xl pl-10 pr-4 py-3 text-sm text-sage-800 placeholder:text-sage-400/60"
                        placeholder="you@example.com">
                </div>
                @error('email')
                    <p class="input-error"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-sm font-medium text-sage-700">Password</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-xs font-medium text-sage-600 hover:text-sage-800 transition">
                            Forgot password?
                        </a>
                    @endif
                </div>
                <div class="relative">
                    <svg class="input-icon w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                        class="input-sage w-full rounded-xl pl-10 pr-4 py-3 text-sm text-sage-800 placeholder:text-sage-400/60"
                        placeholder="Enter your password">
                </div>
                @error('password')
                    <p class="input-error"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ $message }}</p>
                @enderror
            </div>

            {{-- Remember Me --}}
            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2.5 text-sm text-sage-600 cursor-pointer group">
                    <input type="checkbox" name="remember" class="checkbox-sage">
                    <span class="group-hover:text-sage-800 transition">Remember me</span>
                </label>
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn-sage w-full rounded-xl font-semibold py-3 text-sm flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                </svg>
                Sign In
            </button>
        </form>

        {{-- Footer --}}
        <p class="mt-6 text-center text-xs text-sage-500/70">
            &copy; {{ date('Y') }} {{ config('app.name', 'POS') }}. All rights reserved.
        </p>
    </div>

</body>
</html>
