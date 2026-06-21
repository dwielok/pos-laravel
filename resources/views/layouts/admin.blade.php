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
            background: rgba(255, 255, 255, 0.2);
            border-radius: 4px;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        /* Theme variables */
        :root {
            --primary-green: #0d7c4f;
            --primary-green-dark: #0a5f3c;
            --primary-green-light: #e8f5ef;
            --primary-green-lighter: #f0faf5;
            --accent-green: #10b981;
            --sidebar-bg-start: #0d7c4f;
            --sidebar-bg-end: #0a5f3c;
            --body-bg: #f0f7f4;
            --card-bg: #ffffff;
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --border-color: #e2e8f0;
        }

        .dark {
            --primary-green: #10b981;
            --primary-green-dark: #059669;
            --primary-green-light: #1a2e2a;
            --primary-green-lighter: #0f1f1b;
            --accent-green: #34d399;
            --sidebar-bg-start: #064e3b;
            --sidebar-bg-end: #022c22;
            --body-bg: #0f172a;
            --card-bg: #1e293b;
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --border-color: #334155;
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

        .bg-primary-green {
            background-color: var(--primary-green);
        }

        .bg-primary-green-dark {
            background-color: var(--primary-green-dark);
        }

        .bg-primary-green-light {
            background-color: var(--primary-green-light);
        }

        .text-primary-green {
            color: var(--primary-green);
        }

        .hover\:bg-primary-green-dark:hover {
            background-color: var(--primary-green-dark);
        }

        .border-primary-green {
            border-color: var(--primary-green);
        }

        /* Sidebar dark mode overrides */
        .sidebar-gradient {
            background: linear-gradient(135deg, var(--sidebar-bg-start), var(--sidebar-bg-end));
        }

        /* Mobile overlay */
        .sidebar-overlay {
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
        }

        .dark .sidebar-overlay {
            background: rgba(0, 0, 0, 0.7);
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

        /* Mobile menu slide animation */
        .mobile-sidebar {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>
    @stack('styles')
</head>

<body class="h-full overflow-hidden antialiased">
    <div class="flex h-full max-h-screen overflow-hidden">

        {{-- Mobile Overlay --}}
        <div id="mobile-overlay" class="lg:hidden fixed inset-0 sidebar-overlay z-30 hidden"
            onclick="closeMobileSidebar()"></div>

        {{-- Sidebar --}}
        <aside id="sidebar"
            class="sidebar-gradient text-white flex flex-col w-64 shrink-0 fixed lg:relative inset-y-0 left-0 z-40 mobile-sidebar -translate-x-full lg:translate-x-0">
            <!-- Brand -->
            <div class="flex items-center gap-2 px-6 h-16 border-b border-white/10 flex-shrink-0">
                <div
                    class="h-8 w-8 rounded-lg bg-white/20 backdrop-blur flex items-center justify-center font-mono-num font-semibold text-white text-sm">
                    PO
                </div>
                <span class="font-semibold text-white tracking-tight">{{ config('app.name', 'POS System') }}</span>
                <span
                    class="ml-auto text-[10px] font-medium text-white/60 bg-white/10 px-2 py-0.5 rounded-full hidden sm:inline">v2.0</span>
                <button onclick="closeMobileSidebar()" class="lg:hidden ml-2 text-white/70 hover:text-white">
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
                        <p class="px-3 text-[10px] font-semibold uppercase tracking-wider text-white/50 mb-1.5">Overview</p>
                        <x-nav-link href="{{ route('admin.dashboard') }}" icon="chart-bar"
                            :active="request()->routeIs('admin.dashboard')">Dashboard</x-nav-link>
                    </div>
                @endcan

                @canany(['products.view', 'categories.manage', 'units.manage'])
                    <div>
                        <p class="px-3 text-[10px] font-semibold uppercase tracking-wider text-white/50 mb-1.5">Catalog</p>
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
                        <p class="px-3 text-[10px] font-semibold uppercase tracking-wider text-white/50 mb-1.5">Inventory
                        </p>
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
                        <p class="px-3 text-[10px] font-semibold uppercase tracking-wider text-white/50 mb-1.5">Sales</p>

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
                                    class="flex w-full items-center justify-between px-3 py-2 text-white/70 hover:text-white transition rounded-md hover:bg-white/5">
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

                                <div x-show="open" class="mt-1 ml-4 pl-4 border-l border-white/10 space-y-1">
                                    @can('reports.sales')
                                        <a href="{{ route('admin.reports.sales') }}"
                                            class="block px-3 py-1.5 text-xs rounded-md {{ request()->routeIs('admin.reports.sales') ? 'text-white bg-white/10' : 'text-white/60 hover:text-white hover:bg-white/5' }} transition">Sales
                                            Report</a>
                                    @endcan
                                    @can('reports.profit')
                                        <a href="{{ route('admin.reports.profit') }}"
                                            class="block px-3 py-1.5 text-xs rounded-md {{ request()->routeIs('admin.reports.profit') ? 'text-white bg-white/10' : 'text-white/60 hover:text-white hover:bg-white/5' }} transition">Profit
                                            Report</a>
                                    @endcan
                                    @can('reports.inventory')
                                        <a href="{{ route('admin.reports.inventory') }}"
                                            class="block px-3 py-1.5 text-xs rounded-md {{ request()->routeIs('admin.reports.inventory') ? 'text-white bg-white/10' : 'text-white/60 hover:text-white hover:bg-white/5' }} transition">Inventory
                                            Report</a>
                                    @endcan
                                </div>
                            </div>
                        @endcanany
                    </div>
                @endcanany

                @canany(['users.view', 'roles.manage', 'activity-logs.view'])
                    <div>
                        <p class="px-3 text-[10px] font-semibold uppercase tracking-wider text-white/50 mb-1.5">System</p>
                        @role('admin')
                            <x-nav-link href="{{ route('admin.settings.edit') }}" icon="cog"
                                :active="request()->routeIs('admin.settings.*')">Settings</x-nav-link>
                            <x-nav-link href="{{ route('admin.registers.index') }}" icon="shield-check" :active="request()->routeIs('admin.registers.*')">POS
                                Registers</x-nav-link>
                        @endrole
                        @canany(['users.view', 'roles.manage'])
                            <x-nav-link href="{{ route('admin.users.index') }}" icon="shield-check" :active="request()->routeIs('admin.users.*') || request()->routeIs('admin.roles.*')">Users &
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
                <div class="border-t border-white/10 px-4 py-3 flex-shrink-0">
                    <a href="{{ route('pos.register') ?? '#' }}"
                        class="flex items-center justify-center gap-2 rounded-lg bg-white/20 backdrop-blur hover:bg-white/30 transition px-3 py-2.5 text-sm font-medium text-white">
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
                        class="lg:hidden p-2 -ml-2 text-secondary hover:text-primary-green transition">
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
                        class="p-2 text-secondary hover:text-primary-green transition rounded-lg hover:bg-primary-green-light theme-toggle"
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
                        class="hidden sm:flex p-2 text-secondary hover:text-primary-green transition rounded-lg hover:bg-primary-green-light relative">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>

                    @auth
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" @click.away="open = false"
                                class="flex items-center gap-2 text-sm focus:outline-none group">
                                <img src="{{ auth()->user()->avatarUrl() }}"
                                    class="w-8 h-8 rounded-full ring-2 ring-transparent group-hover:ring-emerald-200 transition"
                                    alt="">
                                <span
                                    class="hidden sm:block font-medium text-secondary group-hover:text-primary-green transition truncate max-w-[100px]">{{ auth()->user()->name }}</span>
                                <svg class="hidden sm:block w-4 h-4 text-secondary group-hover:text-primary-green transition"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="open" x-transition
                                class="absolute right-0 mt-2 w-48 bg-card rounded-lg shadow-lg ring-1 ring-theme py-1 text-sm">
                                <a href="{{ route('profile.edit') ?? '#' }}"
                                    class="block px-4 py-2 text-secondary hover:bg-primary-green-light hover:text-primary-green transition">Profile</a>
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
                        class="rounded-lg bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 px-4 py-3 text-sm flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div
                        class="rounded-lg bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 px-4 py-3 text-sm flex items-center gap-2">
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
