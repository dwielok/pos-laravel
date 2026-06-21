@extends('layouts.admin')

@section('page-title', 'Dashboard')
@section('breadcrumb', 'Analytics Overview')

@section('content')
    <div class="space-y-6">

        {{-- Warehouse Filter --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-sm font-medium text-secondary">Welcome back, {{ auth()->user()->name }} 👋</h2>
                <p class="text-xs text-secondary opacity-70">Here's what's happening with your business today</p>
            </div>
            <form method="GET" class="w-full sm:w-auto">
                <div class="relative">
                    <select name="warehouse_id" onchange="this.form.submit()"
                        class="w-full sm:w-auto pl-9 pr-4 py-2 bg-card border border-theme rounded-lg text-sm focus:ring-2 focus:ring-primary-green focus:border-transparent transition appearance-none cursor-pointer">
                        <option value="">All Warehouses</option>
                        @foreach ($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" @selected($warehouseId == $warehouse->id)>{{ $warehouse->name }}
                            </option>
                        @endforeach
                    </select>
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-secondary" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
            </form>
        </div>

        {{-- Stat Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <x-stat-card label="Today's Revenue" :value="$summary['today_revenue']->formatted()" :change="$summary['today_revenue_change_percent']" icon="cash" />
            <x-stat-card label="Today's Transactions" :value="$summary['today_transaction_count']" change="+12" icon="receipt" />
            <x-stat-card label="This Month's Revenue" :value="$summary['month_revenue']->formatted()" :change="$summary['month_revenue_change_percent']" icon="chart-bar" />
            <x-stat-card label="Low Stock Items" :value="$lowStockCount" change="-{{ $lowStockCount > 0 ? $lowStockCount : 0 }}"
                icon="exclamation-circle" :warning="$lowStockCount > 0" />
        </div>

        {{-- Charts Row --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Revenue Chart --}}
            <div
                class="lg:col-span-2 bg-card rounded-2xl border border-theme p-6 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="font-semibold text-primary">Revenue & Profit</h3>
                        <p class="text-xs text-secondary opacity-70">Last 14 days performance</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="flex items-center gap-1.5 text-xs">
                            <span class="w-3 h-0.5 rounded-full bg-indigo-500"></span>
                            <span class="text-secondary">Revenue</span>
                        </span>
                        <span class="flex items-center gap-1.5 text-xs">
                            <span class="w-3 h-0.5 rounded-full bg-emerald-500"></span>
                            <span class="text-secondary">Profit</span>
                        </span>
                    </div>
                </div>
                <canvas id="revenue-chart" height="100"></canvas>
            </div>

            {{-- Payment Methods --}}
            <div class="bg-card rounded-2xl border border-theme p-6 shadow-sm hover:shadow-md transition-shadow">
                <div class="mb-4">
                    <h3 class="font-semibold text-primary">Payment Methods</h3>
                    <p class="text-xs text-secondary opacity-70">This month's breakdown</p>
                </div>
                @if (empty($paymentBreakdown))
                    <div class="flex flex-col items-center justify-center py-8 text-center">
                        <svg class="w-12 h-12 text-secondary opacity-30 mb-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v1m0 4v-1m0 1v1" />
                        </svg>
                        <p class="text-sm text-secondary">No sales recorded yet.</p>
                        <p class="text-xs text-secondary opacity-60">Sales data will appear here</p>
                    </div>
                @else
                    <canvas id="payment-chart" class="max-h-[220px]"></canvas>
                @endif
            </div>
        </div>

        {{-- Bottom Row --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Low Stock Alerts --}}
            <div
                class="bg-card rounded-2xl border border-theme overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                <div class="px-6 py-4 border-b border-theme flex items-center justify-between">
                    <div>
                        <h3 class="font-semibold text-primary flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                            Low Stock Alerts
                        </h3>
                        <p class="text-xs text-secondary opacity-70">Products needing attention</p>
                    </div>
                    <a href="{{ route('admin.reports.inventory') }}"
                        class="text-xs text-primary-green hover:text-primary-green-dark font-medium transition flex items-center gap-1">
                        View all
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
                <div class="divide-y divide-theme max-h-[280px] overflow-y-auto">
                    @forelse ($lowStockItems as $stockLevel)
                        <div class="px-6 py-3.5 flex items-center justify-between hover:bg-primary-green-light transition">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full bg-amber-500 flex-shrink-0"></div>
                                    <p class="font-medium text-sm text-primary truncate">{{ $stockLevel->product->name }}
                                    </p>
                                </div>
                                <p class="text-xs text-secondary ml-4">{{ $stockLevel->warehouse->name }}</p>
                            </div>
                            <x-badge color="warning" class="flex-shrink-0 ml-2">
                                {{ $stockLevel->quantity }} units left
                            </x-badge>
                        </div>
                    @empty
                        <div class="px-6 py-10 text-center">
                            <svg class="w-10 h-10 text-emerald-500 mx-auto mb-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-sm text-secondary font-medium">All products are sufficiently stocked</p>
                            <p class="text-xs text-secondary opacity-60">No low stock items to report</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Best Sellers --}}
            <div
                class="bg-card rounded-2xl border border-theme overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                <div class="px-6 py-4 border-b border-theme">
                    <h3 class="font-semibold text-primary">Best-Selling Products</h3>
                    <p class="text-xs text-secondary opacity-70">Top performers in the last 30 days</p>
                </div>
                <div class="divide-y divide-theme max-h-[280px] overflow-y-auto">
                    @forelse ($bestSelling as $index => $product)
                        <div class="px-6 py-3.5 flex items-center justify-between hover:bg-primary-green-light transition">
                            <div class="flex items-center gap-3 min-w-0">
                                <div
                                    class="w-7 h-7 rounded-full {{ $index === 0 ? 'bg-amber-500/20 text-amber-600 dark:bg-amber-500/30 dark:text-amber-400' : 'bg-primary-green-light text-primary-green' }} flex items-center justify-center text-xs font-bold flex-shrink-0">
                                    #{{ $index + 1 }}
                                </div>
                                <div class="min-w-0">
                                    <p class="font-medium text-sm text-primary truncate">{{ $product->name }}</p>
                                    <div class="flex items-center gap-2 text-xs text-secondary">
                                        <span class="font-mono-num">{{ $product->sku }}</span>
                                        <span class="w-1 h-1 rounded-full bg-secondary opacity-30"></span>
                                        {{-- <span>{{ $product->category->name ?? 'Uncategorized' }}</span> --}}
                                    </div>
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0 ml-2">
                                <p class="font-mono-num font-semibold text-sm text-primary">
                                    {{ $product->total_quantity_sold }} sold</p>
                                <p class="text-xs text-secondary font-mono-num">
                                    {{ \App\Support\Money::fromAmount($product->total_revenue_cents)->formatted() }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-10 text-center">
                            <svg class="w-10 h-10 text-secondary opacity-30 mx-auto mb-2" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            <p class="text-sm text-secondary font-medium">No sales recorded</p>
                            <p class="text-xs text-secondary opacity-60">Data will appear once sales are made</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Recent Transactions --}}
        <div class="bg-card rounded-2xl border border-theme overflow-hidden shadow-sm hover:shadow-md transition-shadow">
            <div class="px-6 py-4 border-b border-theme flex items-center justify-between">
                <div>
                    <h3 class="font-semibold text-primary">Recent Transactions</h3>
                    <p class="text-xs text-secondary opacity-70">Latest sales activity</p>
                </div>
                <a href="{{ route('admin.sales.index') }}"
                    class="text-xs text-primary-green hover:text-primary-green-dark font-medium transition flex items-center gap-1">
                    View all
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-theme text-sm">
                    <thead class="bg-primary-green-light/30">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-xs uppercase tracking-wider text-secondary">
                                Invoice</th>
                            <th class="px-4 py-3 text-left font-medium text-xs uppercase tracking-wider text-secondary">
                                Customer</th>
                            <th class="px-4 py-3 text-left font-medium text-xs uppercase tracking-wider text-secondary">
                                Cashier</th>
                            <th class="px-4 py-3 text-right font-medium text-xs uppercase tracking-wider text-secondary">
                                Total</th>
                            <th class="px-4 py-3 text-left font-medium text-xs uppercase tracking-wider text-secondary">
                                Time</th>
                            <th class="px-4 py-3 text-center font-medium text-xs uppercase tracking-wider text-secondary">
                                Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-theme">
                        @forelse ($recentTransactions as $sale)
                            <tr class="hover:bg-primary-green-light/20 transition cursor-pointer"
                                onclick="window.location='{{ route('admin.sales.show', $sale) }}'">
                                <td class="px-4 py-3 font-mono-num font-medium text-primary-green">
                                    {{ $sale->invoice_number }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-6 h-6 rounded-full bg-primary-green-light text-primary-green flex items-center justify-center text-xs font-medium flex-shrink-0">
                                            {{ $sale->customer->is_guest ? 'W' : substr($sale->customer->name, 0, 1) }}
                                        </div>
                                        <span
                                            class="text-secondary">{{ $sale->customer->is_guest ? 'Walk-in' : $sale->customer->name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-secondary">{{ $sale->cashier->name }}</td>
                                <td class="px-4 py-3 text-right font-mono-num font-semibold text-primary">
                                    {{ $sale->total()->formatted() }}</td>
                                <td class="px-4 py-3 text-secondary text-xs">
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-3 h-3 text-secondary opacity-50" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $sale->created_at->diffForHumans() }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <x-badge color="success" class="flex-shrink-0 ml-2">
                                        Completed
                                    </x-badge>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center">
                                    <svg class="w-12 h-12 text-secondary opacity-30 mx-auto mb-3" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                    <p class="text-sm text-secondary font-medium">No transactions yet</p>
                                    <p class="text-xs text-secondary opacity-60">Start making sales to see them here</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
    <script>
        $(function() {
            // Revenue Chart
            const revenueData = @json($revenueSeries);
            const ctx = document.getElementById('revenue-chart').getContext('2d');

            const gradient1 = ctx.createLinearGradient(0, 0, 0, 300);
            gradient1.addColorStop(0, 'rgba(99, 102, 241, 0.15)');
            gradient1.addColorStop(1, 'rgba(99, 102, 241, 0)');

            const gradient2 = ctx.createLinearGradient(0, 0, 0, 300);
            gradient2.addColorStop(0, 'rgba(16, 185, 129, 0.15)');
            gradient2.addColorStop(1, 'rgba(16, 185, 129, 0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: revenueData.map(d => new Date(d.date).toLocaleDateString(undefined, {
                        month: 'short',
                        day: 'numeric'
                    })),
                    datasets: [{
                        label: 'Revenue',
                        data: revenueData.map(d => d.revenue),
                        borderColor: '#6366F1',
                        backgroundColor: gradient1,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#6366F1',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 3,
                        pointHoverRadius: 6,
                    }, {
                        label: 'Profit',
                        data: revenueData.map(d => d.profit),
                        borderColor: '#10B981',
                        backgroundColor: gradient2,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#10B981',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 3,
                        pointHoverRadius: 6,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: false,
                        },
                        tooltip: {
                            backgroundColor: 'rgba(255,255,255,0.95)',
                            titleColor: '#0f172a',
                            bodyColor: '#475569',
                            borderColor: '#e2e8f0',
                            borderWidth: 1,
                            cornerRadius: 8,
                            padding: 12,
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    let value = context.raw || 0;
                                    return label + ': Rp ' + new Intl.NumberFormat('id-ID').format(
                                        value);
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0,0,0,0.05)',
                                drawBorder: false,
                            },
                            ticks: {
                                callback: function(value) {
                                    if (value >= 1000) {
                                        return 'Rp ' + (value / 1000).toFixed(1) + 'K';
                                    }
                                    return 'Rp ' + value;
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false,
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index',
                    },
                },
            });

            // Payment Chart
            @if (!empty($paymentBreakdown))
                const paymentData = @json(collect($paymentBreakdown)->map(fn($p) => ['method' => $p['method'], 'total' => $p['total']->amount()]));

                const colors = ['#6366F1', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#6B7280'];
                const darkColors = ['#818CF8', '#34D399', '#FBBF24', '#F87171', '#A78BFA', '#9CA3AF'];

                new Chart(document.getElementById('payment-chart'), {
                    type: 'doughnut',
                    data: {
                        labels: paymentData.map(p => p.method.replace('_', ' ').replace(/\b\w/g, c => c
                            .toUpperCase())),
                        datasets: [{
                            data: paymentData.map(p => p.total),
                            backgroundColor: document.documentElement.classList.contains('dark') ?
                                darkColors : colors,
                            borderColor: document.documentElement.classList.contains('dark') ?
                                '#1e293b' : '#ffffff',
                            borderWidth: 3,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        cutout: '70%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 12,
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    font: {
                                        size: 11,
                                        weight: '500'
                                    },
                                    color: document.documentElement.classList.contains('dark') ? '#f1f5f9' :
                                        '#0f172a'
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(255,255,255,0.95)',
                                titleColor: '#0f172a',
                                bodyColor: '#475569',
                                borderColor: '#e2e8f0',
                                borderWidth: 1,
                                cornerRadius: 8,
                                padding: 12,
                                callbacks: {
                                    label: function(context) {
                                        let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        let percentage = ((context.parsed / total) * 100).toFixed(1);
                                        return context.label + ': Rp ' + new Intl.NumberFormat('id-ID')
                                            .format(context
                                                .parsed) + ' (' + percentage + '%)';
                                    }
                                }
                            }
                        },
                    },
                });
            @endif
        });
    </script>
@endpush
