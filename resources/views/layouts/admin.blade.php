<!DOCTYPE html>
<html lang="en" class="h-full" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" :class="darkMode ? 'dark' : ''">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') · {{ config('app.name', 'POS') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        * {
            font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
        }

        .font-mono-num {
            font-family: 'JetBrains Mono', ui-monospace, monospace;
            font-variant-numeric: tabular-nums;
        }

        /* Custom scrollbar */
        .sidebar-scroll::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: var(--sage-300);
            border-radius: 4px;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb:hover {
            background: var(--sage-400);
        }

        /* ===== SAGE GREEN THEME VARIABLES ===== */
        :root {
            /* Sage color scale */
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

            /* Theme aliases */
            --primary: var(--sage-600);
            --primary-dark: var(--sage-700);
            --primary-light: var(--sage-100);
            --primary-lighter: var(--sage-50);
            --accent: var(--sage-500);

            /* Sidebar - clean white with sage tint */
            --sidebar-bg-start: #ffffff;
            --sidebar-bg-end: #f6f8f4;
            --sidebar-border: var(--sage-200);
            --sidebar-text: var(--sage-800);
            --sidebar-text-muted: var(--sage-500);
            --sidebar-hover-bg: var(--sage-50);
            --sidebar-active-bg: var(--sage-100);
            --sidebar-active-text: var(--sage-700);

            /* Body */
            --body-bg: #fafcfa;
            --card-bg: #ffffff;
            --text-primary: #1a1f18;
            --text-secondary: #4a5a42;
            --border-color: var(--sage-200);
        }

        .dark {
            /* Dark mode - deeper sage */
            --sage-50: #1a1f18;
            --sage-100: #242c20;
            --sage-200: #3a4a33;
            --sage-300: #5a7050;
            --sage-400: #7a9670;
            --sage-500: #9ab890;
            --sage-600: #b8d0ae;
            --sage-700: #d4e6cc;
            --sage-800: #eaf3e5;
            --sage-900: #f6faf4;

            --primary: var(--sage-500);
            --primary-dark: var(--sage-600);
            --primary-light: var(--sage-200);
            --primary-lighter: var(--sage-100);

            --sidebar-bg-start: #141c12;
            --sidebar-bg-end: #1a2418;
            --sidebar-border: var(--sage-200);
            --sidebar-text: var(--sage-700);
            --sidebar-text-muted: var(--sage-400);
            --sidebar-hover-bg: var(--sage-100);
            --sidebar-active-bg: var(--sage-100);
            --sidebar-active-text: var(--sage-600);

            --body-bg: #0d120c;
            --card-bg: #1a2218;
            --text-primary: #eaf3e5;
            --text-secondary: #b8d0ae;
            --border-color: var(--sage-200);
        }

        /* Apply theme variables */
        body {
            background-color: var(--body-bg);
            color: var(--text-primary);
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .bg-card {
            background-color: var(--card-bg);
        }

        .text-secondary {
            color: var(--text-secondary);
        }

        .border-theme {
            border-color: var(--border-color);
        }

        /* Sage utility classes */
        .bg-sage-50 {
            background-color: var(--sage-50);
        }
        .bg-sage-100 {
            background-color: var(--sage-100);
        }
        .bg-sage-200 {
            background-color: var(--sage-200);
        }
        .bg-sage-300 {
            background-color: var(--sage-300);
        }
        .bg-sage-400 {
            background-color: var(--sage-400);
        }
        .bg-sage-500 {
            background-color: var(--sage-500);
        }
        .bg-sage-600 {
            background-color: var(--sage-600);
        }
        .bg-sage-700 {
            background-color: var(--sage-700);
        }

        .text-sage-50 {
            color: var(--sage-50);
        }
        .text-sage-100 {
            color: var(--sage-100);
        }
        .text-sage-200 {
            color: var(--sage-200);
        }
        .text-sage-300 {
            color: var(--sage-300);
        }
        .text-sage-400 {
            color: var(--sage-400);
        }
        .text-sage-500 {
            color: var(--sage-500);
        }
        .text-sage-600 {
            color: var(--sage-600);
        }
        .text-sage-700 {
            color: var(--sage-700);
        }
        .text-sage-800 {
            color: var(--sage-800);
        }
        .text-sage-900 {
            color: var(--sage-900);
        }

        .border-sage-200 {
            border-color: var(--sage-200);
        }
        .border-sage-300 {
            border-color: var(--sage-300);
        }

        .hover\:bg-sage-50:hover {
            background-color: var(--sage-50);
        }
        .hover\:bg-sage-100:hover {
            background-color: var(--sage-100);
        }
        .hover\:text-sage-600:hover {
            color: var(--sage-600);
        }
        .hover\:text-sage-700:hover {
            color: var(--sage-700);
        }

        /* Sidebar - White/Sage */
        .sidebar-gradient {
            background: linear-gradient(145deg, var(--sidebar-bg-start), var(--sidebar-bg-end));
            border-right: 1px solid var(--sidebar-border);
        }

        .sidebar-gradient .text-white {
            color: var(--sidebar-text) !important;
        }

        .sidebar-gradient .text-white\/70 {
            color: var(--sidebar-text-muted) !important;
        }

        .sidebar-gradient .text-white\/50 {
            color: var(--sidebar-text-muted) !important;
        }

        .sidebar-gradient .border-white\/10 {
            border-color: var(--sidebar-border) !important;
        }

        .sidebar-gradient .bg-white\/20 {
            background-color: var(--sage-100) !important;
            color: var(--sage-700) !important;
        }

        .sidebar-gradient .bg-white\/10 {
            background-color: var(--sage-100) !important;
        }

        .sidebar-gradient .bg-white\/15 {
            background-color: var(--sidebar-active-bg) !important;
            color: var(--sidebar-active-text) !important;
        }

        .sidebar-gradient .hover\:bg-white\/10:hover {
            background-color: var(--sidebar-hover-bg) !important;
        }

        .sidebar-gradient .hover\:bg-white\/30:hover {
            background-color: var(--sage-200) !important;
        }

        .sidebar-gradient .hover\:text-white:hover {
            color: var(--sidebar-text) !important;
        }

        .sidebar-gradient .text-white\/60 {
            color: var(--sidebar-text-muted) !important;
        }

        /* Brand in sidebar */
        .sidebar-gradient .brand-box {
            background-color: var(--sage-100) !important;
            color: var(--sage-700) !important;
        }

        .sidebar-gradient .brand-text {
            color: var(--sidebar-text) !important;
        }

        /* POS button */
        .sidebar-gradient .pos-button {
            background-color: var(--sage-100) !important;
            color: var(--sage-700) !important;
        }

        .sidebar-gradient .pos-button:hover {
            background-color: var(--sage-200) !important;
        }

        /* Mobile overlay */
        .sidebar-overlay {
            background: rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(4px);
        }

        .dark .sidebar-overlay {
            background: rgba(0, 0, 0, 0.6);
        }

        /* Smooth transitions */
        .transition-smooth {
            transition: all 0.2s ease-in-out;
        }

        /* Theme toggle animation */
        .theme-toggle {
            transition: transform 0.5s ease;
        }

        .theme-toggle.rotated {
            transform: rotate(360deg);
        }

        /* Mobile sidebar slide */
        .mobile-sidebar {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Nav link active state for sidebar */
        .nav-link-active {
            background-color: var(--sage-100) !important;
            color: var(--sage-700) !important;
        }

        .nav-link-active .active-dot {
            background-color: var(--sage-500) !important;
        }

        /* Report link active state */
        .report-link-active {
            background-color: var(--sage-100) !important;
            color: var(--sage-700) !important;
        }
    </style>
    @stack('styles')
</head>

<body class="h-full overflow-hidden antialiased">
    <div class="flex h-full max-h-screen overflow-hidden">

        {{-- Mobile Overlay --}}
        <div id="mobile-overlay" class="lg:hidden fixed inset-0 sidebar-overlay z-30 hidden"
            onclick="closeMobileSidebar()"></div>

        {{-- Sidebar - White & Sage --}}
        <aside id="sidebar"
            class="sidebar-gradient flex flex-col w-64 shrink-0 fixed lg:relative inset-y-0 left-0 z-40 mobile-sidebar -translate-x-full lg:translate-x-0">

            <!-- Brand -->
            <div class="flex items-center gap-2 px-6 h-16 border-b border-sage-200 flex-shrink-0">
                <div
                    class="h-8 w-8 rounded-lg brand-box flex items-center justify-center font-mono-num font-semibold text-sm">
                    PO
                </div>
                <span class="font-semibold brand-text tracking-tight">{{ config('app.name', 'POS System') }}</span>
                <span
                    class="ml-auto text-[10px] font-medium text-sage-400 bg-sage-100 px-2 py-0.5 rounded-full hidden sm:inline">v2.0</span>
                <button onclick="closeMobileSidebar()" class="lg:hidden ml-2 text-sage-400 hover:text-sage-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto sidebar-scroll px-3 py-4 space-y-4 text-sm">
                @can('dashboard.view')
                    <div>
                        <p class="px-3 text-[10px] font-semibold uppercase tracking-wider text-sage-400 mb-1.5">Overview</p>
                        <x-nav-link href="{{ route('admin.dashboard') }}" icon="chart-bar"
                            :active="request()->routeIs('admin.dashboard')">Dashboard</x-nav-link>
                    </div>
                @endcan

                @canany(['products.view', 'categories.manage', 'units.manage'])
                    <div>
                        <p class="px-3 text-[10px] font-semibold uppercase tracking-wider text-sage-400 mb-1.5">Catalog</p>
                        @can('products.view')
                            <x-nav-link href="{{ route('admin.products.index') }}" icon="cube"
                                :active="request()->routeIs('admin.products.*')">Products</x-nav-link>
                        @endcan
                        @can('categories.manage')
                            <x-nav-link href="{{ route('admin.categories.index') }}" icon="tag"
                                :active="request()->routeIs('admin.categories.*')">Categories</x-nav-link>
                        @endcan
                        @can('units.manage')
                            <x-nav-link href="{{ route('admin.units.index') }}" icon="scale"
                                :active="request()->routeIs('admin.units.*')">Units</x-nav-link>
                        @endcan
                    </div>
                @endcanany

                @canany(['suppliers.view', 'purchases.view', 'stock-adjustments.view', 'stock-movements.view'])
                    <div>
                        <p class="px-3 text-[10px] font-semibold uppercase tracking-wider text-sage-400 mb-1.5">Inventory</p>
                        @can('suppliers.view')
                            <x-nav-link href="{{ route('admin.suppliers.index') }}" icon="truck"
                                :active="request()->routeIs('admin.suppliers.*')">Suppliers</x-nav-link>
                        @endcan
                        @can('purchases.view')
                            <x-nav-link href="{{ route('admin.purchases.index') }}" icon="clipboard-list"
                                :active="request()->routeIs('admin.purchases.*')">Purchases</x-nav-link>
                        @endcan
                        @can('stock-adjustments.view')
                            <x-nav-link href="{{ route('admin.stock-adjustments.index') }}" icon="adjustments"
                                :active="request()->routeIs('admin.stock-adjustments.*')">Stock Adjustments</x-nav-link>
                        @endcan
                        @can('stock-movements.view')
                            <x-nav-link href="{{ route('admin.stock-movements.index') }}" icon="clock"
                                :active="request()->routeIs('admin.stock-movements.index')">Stock Movements</x-nav-link>
                        @endcan
                    </div>
                @endcanany

                @canany(['customers.view', 'sales.view', 'sales.view-all', 'reports.sales', 'reports.profit',
                    'reports.inventory'])
                    <div>
                        <p class="px-3 text-[10px] font-semibold uppercase tracking-wider text-sage-400 mb-1.5">Sales</p>

                        @can('customers.view')
                            <x-nav-link href="{{ route('admin.customers.index') }}" icon="users"
                                :active="request()->routeIs('admin.customers.*')">Customers</x-nav-link>
                        @endcan

                        @canany(['sales.view', 'sales.view-all'])
                            <x-nav-link href="{{ route('admin.sales.index') }}" icon="receipt"
                                :active="request()->routeIs('admin.sales.*')">Transactions</x-nav-link>
                        @endcanany

                        @canany(['reports.sales', 'reports.profit', 'reports.inventory'])
                            <div x-data="{ open: {{ request()->routeIs('admin.reports.*') ? 'true' : 'false' }} }">
                                <button @click="open = !open"
                                    class="flex w-full items-center justify-between px-3 py-2 text-sage-500 hover:text-sage-700 transition rounded-md hover:bg-sage-50">
                                    <span class="flex items-center gap-3">
                                        <x-icon name="clock" class="w-4 h-4 shrink-0" />
                                        Reports
                                    </span>
                                    <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <div x-show="open" class="mt-1 ml-4 pl-4 border-l border-sage-200 space-y-1">
                                    @can('reports.sales')
                                        <a href="{{ route('admin.reports.sales') }}"
                                            class="block px-3 py-1.5 text-xs rounded-md {{ request()->routeIs('admin.reports.sales') ? 'bg-sage-100 text-sage-700' : 'text-sage-500 hover:text-sage-700 hover:bg-sage-50' }} transition">Sales
                                            Report</a>
                                    @endcan
                                    @can('reports.profit')
                                        <a href="{{ route('admin.reports.profit') }}"
                                            class="block px-3 py-1.5 text-xs rounded-md {{ request()->routeIs('admin.reports.profit') ? 'bg-sage-100 text-sage-700' : 'text-sage-500 hover:text-sage-700 hover:bg-sage-50' }} transition">Profit
                                            Report</a>
                                    @endcan
                                    @can('reports.inventory')
                                        <a href="{{ route('admin.reports.inventory') }}"
                                            class="block px-3 py-1.5 text-xs rounded-md {{ request()->routeIs('admin.reports.inventory') ? 'bg-sage-100 text-sage-700' : 'text-sage-500 hover:text-sage-700 hover:bg-sage-50' }} transition">Inventory
                                            Report</a>
                                    @endcan
                                </div>
                            </div>
                        @endcanany
                    </div>
                @endcanany

                @canany(['users.view', 'roles.manage', 'activity-logs.view'])
                    <div>
                        <p class="px-3 text-[10px] font-semibold uppercase tracking-wider text-sage-400 mb-1.5">System</p>
                        @role('admin')
                            <x-nav-link href="{{ route('admin.settings.edit') }}" icon="cog"
                                :active="request()->routeIs('admin.settings.*')">Settings</x-nav-link>
                            <x-nav-link href="{{ route('admin.registers.index') }}" icon="shield-check"
                                :active="request()->routeIs('admin.registers.*')">POS Registers</x-nav-link>
                        @endrole
                        @canany(['users.view', 'roles.manage'])
                            <x-nav-link href="{{ route('admin.users.index') }}" icon="shield-check"
                                :active="request()->routeIs('admin.users.*') || request()->routeIs('admin.roles.*')">Users &
                                Roles</x-nav-link>
                        @endcanany
                        @can('activity-logs.view')
                            <x-nav-link href="{{ route('admin.activity-log.index') }}" icon="clock"
                                :active="request()->routeIs('admin.activity-log.*')">Activity Log</x-nav-link>
                        @endcan
                    </div>
                @endcanany
            </nav>

            <!-- POS Button -->
            @can('pos.access')
                <div class="border-t border-sage-200 px-4 py-3 flex-shrink-0">
                    <a href="{{ route('pos.register') ?? '#' }}"
                        class="flex items-center justify-center gap-2 rounded-lg pos-button hover:bg-sage-200 transition px-3 py-2.5 text-sm font-medium text-sage-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M5 6h14M7 4h10M4 20h16a1 1 0 001-1V9a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1z" />
                        </svg>
                        Open POS Register
                    </a>
                </div>
            @endcan
        </aside>

        {{-- Main column --}}
        <div class="flex-1 flex flex-col min-w-0 h-full max-h-screen overflow-hidden">
            {{-- Topbar --}}
            <header
                class="sticky top-0 z-20 flex items-center justify-between h-16 px-4 sm:px-6 bg-card border-b border-theme shadow-sm flex-shrink-0">
                <div class="flex items-center gap-3 min-w-0">
                    <button onclick="toggleMobileSidebar()"
                        class="lg:hidden p-2 -ml-2 text-secondary hover:text-sage-600 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <div class="flex items-center gap-2 min-w-0">
                        <h1 class="text-lg font-semibold truncate">@yield('page-title', 'Dashboard')</h1>
                        <span class="hidden sm:inline text-xs text-secondary font-medium truncate">/
                            @yield('breadcrumb', 'Overview')</span>
                    </div>
                </div>

                <div class="flex items-center gap-2 sm:gap-3 flex-shrink-0">
                    <!-- Dark/Light Mode Toggle -->
                    <button @click="darkMode = !darkMode"
                        class="p-2 text-secondary hover:text-sage-600 transition rounded-lg hover:bg-sage-50 theme-toggle"
                        :class="darkMode ? 'rotated' : ''">
                        <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                        <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </button>

                    <!-- Quick actions -->
                    <button
                        class="hidden sm:flex p-2 text-secondary hover:text-sage-600 transition rounded-lg hover:bg-sage-50 relative">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span class="absolute top-1 right-1 w-2 h-2 bg-sage-500 rounded-full"></span>
                    </button>

                    @auth
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" @click.away="open = false"
                                class="flex items-center gap-2 text-sm focus:outline-none group">
                                <img src="{{ auth()->user()->avatarUrl() }}"
                                    class="w-8 h-8 rounded-full ring-2 ring-transparent group-hover:ring-sage-300 transition"
                                    alt="">
                                <span
                                    class="hidden sm:block font-medium text-secondary group-hover:text-sage-600 transition truncate max-w-[100px]">{{ auth()->user()->name }}</span>
                                <svg class="hidden sm:block w-4 h-4 text-secondary group-hover:text-sage-600 transition"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="open" x-transition
                                class="absolute right-0 mt-2 w-48 bg-card rounded-lg shadow-lg ring-1 ring-sage-200 py-1 text-sm">
                                <a href="{{ route('profile.edit') ?? '#' }}"
                                    class="block px-4 py-2 text-secondary hover:bg-sage-50 hover:text-sage-700 transition">Profile</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="w-full text-left px-4 py-2 text-red-600 hover:bg-red-50 transition">Log
                                        out</button>
                                </form>
                            </div>
                        </div>
                    @endauth
                </div>
            </header>

            <!-- Flash messages -->
            <div class="px-4 sm:px-6 pt-4 flex-shrink-0">
                @if (session('success'))
                    <div
                        class="rounded-lg bg-sage-50 border border-sage-200 text-sage-800 px-4 py-3 text-sm flex items-center gap-2">
                        <svg class="w-5 h-5 text-sage-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div
                        class="rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ session('error') }}
                    </div>
                @endif
            </div>

            <!-- Main content -->
            <main class="flex-1 px-4 sm:px-6 py-4 overflow-y-auto">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        function toggleMobileSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobile-overlay');

            const isOpen = sidebar.classList.contains('translate-x-0');

            if (isOpen) {
                sidebar.classList.remove('translate-x-0');
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                document.body.style.overflow = '';
            } else {
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('translate-x-0');
                overlay.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeMobileSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobile-overlay');

            sidebar.classList.remove('translate-x-0');
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
            document.body.style.overflow = '';
        }

        // Close sidebar on resize to desktop
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1024) {
                const overlay = document.getElementById('mobile-overlay');
                overlay.classList.add('hidden');
                document.body.style.overflow = '';
            }
        });

        // Close sidebar on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeMobileSidebar();
            }
        });
    </script>

    @stack('scripts')
</body>

</html>
