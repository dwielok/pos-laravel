<!DOCTYPE html>
<html lang="en" class="h-full bg-[#f0f7f4]">

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

    <style>
        body {
            font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
        }

        .font-mono-num {
            font-family: 'JetBrains Mono', ui-monospace, monospace;
            font-variant-numeric: tabular-nums;
        }

        /* Custom scrollbar for sidebar */
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

        /* Modern green theme variables */
        :root {
            --primary-green: #0d7c4f;
            --primary-green-dark: #0a5f3c;
            --primary-green-light: #e8f5ef;
            --primary-green-lighter: #f0faf5;
            --accent-green: #10b981;
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

        /* Smooth transitions */
        .transition-smooth {
            transition: all 0.2s ease-in-out;
        }
    </style>
    @stack('styles')
</head>

<body class="h-full overflow-hidden text-slate-900 antialiased bg-[#f0f7f4]">
    <div class="flex h-full max-h-screen overflow-hidden">

        {{-- Sidebar --}}
        <aside class="hidden lg:flex lg:flex-col w-64 shrink-0 bg-gradient-to-b from-[#0d7c4f] to-[#0a5f3c] text-white">
            <!-- Brand -->
            <div class="flex items-center gap-2 px-6 h-16 border-b border-white/10">
                <div
                    class="h-8 w-8 rounded-lg bg-white/20 backdrop-blur flex items-center justify-center font-mono-num font-semibold text-white text-sm">
                    PO</div>
                <span class="font-semibold text-white tracking-tight">{{ config('app.name', 'POS System') }}</span>
                <span
                    class="ml-auto text-[10px] font-medium text-white/60 bg-white/10 px-2 py-0.5 rounded-full">v1.0</span>
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
                <div class="border-t border-white/10 px-4 py-3">
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
                class="sticky top-0 z-20 flex items-center justify-between h-16 px-4 sm:px-6 bg-white border-b border-emerald-100/50 shadow-sm">
                <div class="flex items-center gap-3">
                    <button id="mobile-menu-btn"
                        class="lg:hidden p-2 -ml-2 text-slate-500 hover:text-primary-green transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <div class="flex items-center gap-2">
                        <h1 class="text-lg font-semibold text-slate-900">@yield('page-title', 'Dashboard')</h1>
                        <span class="hidden sm:inline text-xs text-slate-400 font-medium">/ @yield('breadcrumb', 'Overview')</span>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <!-- Quick actions -->
                    <button
                        class="hidden sm:flex p-2 text-slate-400 hover:text-primary-green transition rounded-lg hover:bg-emerald-50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </button>
                    <button
                        class="hidden sm:flex p-2 text-slate-400 hover:text-primary-green transition rounded-lg hover:bg-emerald-50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </button>

                    @auth
                        <div class="relative">
                            <button id="user-menu-btn" class="flex items-center gap-2 text-sm focus:outline-none group">
                                <img src="{{ auth()->user()->avatarUrl() }}"
                                    class="w-8 h-8 rounded-full ring-2 ring-transparent group-hover:ring-emerald-200 transition"
                                    alt="">
                                <span
                                    class="hidden sm:block font-medium text-slate-700 group-hover:text-primary-green transition">{{ auth()->user()->name }}</span>
                                <svg class="hidden sm:block w-4 h-4 text-slate-400 group-hover:text-primary-green transition"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div id="user-menu"
                                class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg ring-1 ring-slate-200 py-1 text-sm">
                                <a href="{{ route('profile.edit') ?? '#' }}"
                                    class="block px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-primary-green transition">Profile</a>
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
                        class="rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm flex items-center gap-2">
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
                        class="rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ session('error') }}
                    </div>
                @endif
            </div>

            <!-- Main content - flex-1 with overflow -->
            <main class="flex-1 px-4 sm:px-6 py-4 overflow-y-auto">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>

</html>
