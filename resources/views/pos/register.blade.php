<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>POS Register</title>

    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#5e7e51">

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css'])

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://unpkg.com/dexie@3/dist/dexie.js"></script>

    <style>
        body {
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

        .bg-sage-50 {
            background-color: var(--sage-50);
        }

        .bg-sage-100 {
            background-color: var(--sage-100);
        }

        .bg-sage-200 {
            background-color: var(--sage-200);
        }

        .bg-sage-500 {
            background-color: var(--sage-500);
        }

        .bg-sage-600 {
            background-color: var(--sage-600);
        }

        .text-sage-600 {
            color: var(--sage-600);
        }

        .text-sage-700 {
            color: var(--sage-700);
        }

        .border-sage-200 {
            border-color: var(--sage-200);
        }

        .hover\:bg-sage-50:hover {
            background-color: var(--sage-50);
        }

        .hover\:bg-sage-100:hover {
            background-color: var(--sage-100);
        }

        .hover\:text-sage-700:hover {
            color: var(--sage-700);
        }

        .focus\:ring-sage-400:focus {
            --tw-ring-color: var(--sage-400);
        }

        .focus\:ring-sage-500:focus {
            --tw-ring-color: var(--sage-500);
        }

        .focus\:border-sage-400:focus {
            border-color: var(--sage-400);
        }

        .focus\:border-sage-500:focus {
            border-color: var(--sage-500);
        }
    </style>
</head>

<body class="h-full bg-sage-50 text-sage-900 antialiased overflow-hidden">

    <div id="pos-app" class="h-screen flex flex-col"
        data-warehouse-id="{{ $registers->first()->warehouse_id ?? '' }}">

        {{-- Topbar --}}
        <header class="flex items-center justify-between px-4 h-14 bg-sage-800 text-white shrink-0">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.dashboard') ?? '#' }}"
                    class="text-sage-300 hover:text-white text-sm transition">&larr;
                    Back</a>
                <span class="font-semibold">POS Register</span>
            </div>
            <div class="flex items-center gap-3">
                <span id="pending-sync-badge"
                    class="hidden inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full bg-amber-500 text-white text-xs font-semibold"></span>
                <span id="connection-status"
                    class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-medium bg-sage-100 text-sage-700">Online</span>
                <span class="text-sm text-sage-300">{{ auth()->user()->name }}</span>
            </div>
        </header>

        <div class="flex-1 flex overflow-hidden">

            {{-- Left: search + results --}}
            <div class="w-1/2 flex flex-col border-r border-sage-200 bg-white">
                <div class="p-4 border-b border-sage-100">
                    <div class="relative">
                        <x-icon name="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-sage-400" />
                        <input type="text" id="product-search-input" autofocus
                            placeholder="Search by name, SKU, or scan barcode..."
                            class="w-full rounded-xl border-sage-200 pl-9 text-sm focus:ring-2 focus:ring-sage-400 focus:border-sage-400">
                    </div>
                    <input type="text" id="barcode-input" class="hidden">
                </div>
                <div id="search-results"
                    class="flex-1 overflow-y-auto p-3 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 content-start">
                </div>
            </div>

            {{-- Right: cart --}}
            <div class="w-1/2 flex flex-col bg-sage-50">
                <div class="flex items-center justify-between px-4 py-3 border-b border-sage-200 bg-white">
                    <h2 class="font-semibold text-sage-800">Current Sale</h2>
                    <button id="clear-cart-btn" type="button"
                        class="text-xs text-sage-400 hover:text-red-600 transition">Clear
                        cart</button>
                </div>

                <div id="cart-items" class="flex-1 overflow-y-auto px-4 py-2 bg-white"></div>

                <div class="bg-white border-t border-sage-200 px-4 py-4 space-y-2">
                    <div class="flex justify-between text-sm text-sage-600">
                        <span>Subtotal</span>
                        <span id="cart-subtotal" class="font-mono-num">0.00</span>
                    </div>
                    <div class="flex justify-between text-sm text-sage-600">
                        <span>Discount</span>
                        <span id="cart-discount" class="font-mono-num">0.00</span>
                    </div>
                    <div class="flex justify-between text-sm text-sage-600">
                        <span>Tax</span>
                        <span id="cart-tax" class="font-mono-num">0.00</span>
                    </div>
                    <div class="flex justify-between text-lg font-semibold text-sage-800 pt-2 border-t border-sage-100">
                        <span>Total</span>
                        <span id="cart-total" class="font-mono-num">0.00</span>
                    </div>

                    <button id="checkout-btn" type="button" disabled
                        class="w-full mt-2 rounded-xl bg-sage-600 hover:bg-sage-700 disabled:bg-sage-300 disabled:cursor-not-allowed text-white font-semibold py-3 text-base transition">
                        Charge
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Payment modal --}}
    <x-modal-pos id="payment-modal" title="Take Payment" maxWidth="sm">
        <form id="payment-form">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-sage-700 mb-1">Payment Method</label>
                    <select id="payment-method-select"
                        class="w-full rounded-xl border-sage-200 text-sm focus:ring-2 focus:ring-sage-400 focus:border-sage-400">
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="qris">QRIS</option>
                        <option value="e_wallet">E-Wallet</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-sage-700 mb-1">Amount Received</label>
                    <input type="number" step="0.01" min="0" id="payment-amount-input" required
                        class="w-full rounded-xl border-sage-200 text-lg font-mono-num font-semibold focus:ring-2 focus:ring-sage-400 focus:border-sage-400">
                </div>
            </div>
            <div class="mt-5 flex justify-end gap-2">
                <button type="button" data-modal-close="payment-modal"
                    class="rounded-xl border border-sage-200 text-sm font-medium px-4 py-2 text-sage-600 hover:bg-sage-50 transition">
                    Cancel
                </button>
                <button type="submit"
                    class="rounded-xl bg-sage-600 hover:bg-sage-700 text-sm font-medium px-5 py-2 text-white shadow-sm hover:shadow-md transition">
                    Complete Sale
                </button>
            </div>
        </form>
    </x-modal-pos>

    {{-- Receipt confirmation modal --}}
    <x-modal-pos id="receipt-modal" title="Sale Complete" maxWidth="sm">
        <div class="text-center py-2">
            <div class="w-12 h-12 rounded-full bg-sage-100 text-sage-600 flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <p class="font-mono-num font-semibold text-lg text-sage-800" id="receipt-invoice-number">INV-0000</p>
            <p id="receipt-offline-note" class="hidden text-xs text-amber-600 mt-1">Saved offline — will sync
                automatically when back online.</p>

            <div class="mt-4 text-sm space-y-1">
                <div class="flex justify-between"><span class="text-sage-500">Total</span><span
                        class="font-mono-num font-medium text-sage-800" id="receipt-total">0.00</span></div>
                <div class="flex justify-between"><span class="text-sage-500">Change</span><span
                        class="font-mono-num font-medium text-sage-800" id="receipt-change">0.00</span></div>
            </div>
        </div>
        <div class="mt-5 flex justify-center gap-2">
            <button type="button" data-modal-close="receipt-modal"
                class="rounded-xl bg-sage-600 hover:bg-sage-700 text-sm font-medium px-5 py-2 text-white shadow-sm hover:shadow-md transition">
                New Sale
            </button>
        </div>
    </x-modal-pos>

    {{-- One-time register pairing modal --}}
    <div id="pairing-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-sage-900/60 p-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-sm w-full p-6 border border-sage-200">
            <h3 class="font-semibold text-sage-800 mb-1">Pair This Device</h3>
            <p class="text-sm text-sage-500 mb-4">Enter the register pairing token provided by your administrator.
                This only needs to be done once per device.</p>
            <form id="pairing-form">
                <input type="text" id="pairing-token-input" placeholder="Pairing token" required
                    class="w-full rounded-xl border-sage-200 text-sm font-mono-num focus:ring-2 focus:ring-sage-400 focus:border-sage-400">
                <button type="submit"
                    class="w-full mt-4 rounded-xl bg-sage-600 hover:bg-sage-700 text-white text-sm font-medium py-2.5 shadow-sm hover:shadow-md transition">
                    Pair Device
                </button>
            </form>
        </div>
    </div>

    <script src="{{ asset('js/pos/db.js') }}"></script>
    <script src="{{ asset('js/pos/cart.js') }}"></script>
    <script src="{{ asset('js/pos/sync-queue.js') }}"></script>
    <script src="{{ asset('js/pos/register.js') }}"></script>
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js', {
                scope: '/pos/'
            }).catch(console.error);
        }
    </script>
</body>

</html>
