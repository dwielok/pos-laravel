<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>POS Register</title>

    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#4F46E5">

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
    </style>
</head>

<body class="h-full bg-slate-100 text-slate-900 antialiased overflow-hidden">

    <div id="pos-app" class="h-screen flex flex-col"
        data-warehouse-id="{{ $registers->first()->warehouse_id ?? '' }}">

        {{-- Topbar --}}
        <header class="flex items-center justify-between px-4 h-14 bg-[#11132B] text-white shrink-0">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.dashboard') ?? '#' }}" class="text-slate-400 hover:text-white text-sm">&larr;
                    Back</a>
                <span class="font-semibold">POS Register</span>
            </div>
            <div class="flex items-center gap-3">
                <span id="pending-sync-badge"
                    class="hidden inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full bg-amber-500 text-white text-xs font-semibold"></span>
                <span id="connection-status"
                    class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-medium bg-emerald-100 text-emerald-700">Online</span>
                <span class="text-sm text-slate-300">{{ auth()->user()->name }}</span>
            </div>
        </header>

        <div class="flex-1 flex overflow-hidden">

            {{-- Left: search + results --}}
            <div class="w-1/2 flex flex-col border-r border-slate-200 bg-white">
                <div class="p-4 border-b border-slate-100">
                    <div class="relative">
                        <x-icon name="search"
                            class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                        <input type="text" id="product-search-input" autofocus
                            placeholder="Search by name, SKU, or scan barcode..."
                            class="w-full rounded-lg border-slate-300 pl-9 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <input type="text" id="barcode-input" class="hidden">
                </div>
                <div id="search-results"
                    class="flex-1 overflow-y-auto p-3 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 content-start">
                </div>
            </div>

            {{-- Right: cart --}}
            <div class="w-1/2 flex flex-col bg-slate-50">
                <div class="flex items-center justify-between px-4 py-3 border-b border-slate-200 bg-white">
                    <h2 class="font-semibold text-slate-900">Current Sale</h2>
                    <button id="clear-cart-btn" type="button" class="text-xs text-slate-400 hover:text-red-600">Clear
                        cart</button>
                </div>

                <div id="cart-items" class="flex-1 overflow-y-auto px-4 py-2 bg-white"></div>

                <div class="bg-white border-t border-slate-200 px-4 py-4 space-y-2">
                    <div class="flex justify-between text-sm text-slate-600">
                        <span>Subtotal</span>
                        <span id="cart-subtotal" class="font-mono-num">0.00</span>
                    </div>
                    <div class="flex justify-between text-sm text-slate-600">
                        <span>Discount</span>
                        <span id="cart-discount" class="font-mono-num">0.00</span>
                    </div>
                    <div class="flex justify-between text-sm text-slate-600">
                        <span>Tax</span>
                        <span id="cart-tax" class="font-mono-num">0.00</span>
                    </div>
                    <div
                        class="flex justify-between text-lg font-semibold text-slate-900 pt-2 border-t border-slate-100">
                        <span>Total</span>
                        <span id="cart-total" class="font-mono-num">0.00</span>
                    </div>

                    <button id="checkout-btn" type="button" disabled
                        class="w-full mt-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 disabled:bg-slate-300 disabled:cursor-not-allowed text-white font-semibold py-3 text-base transition">
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
                    <label class="block text-sm font-medium text-slate-700 mb-1">Payment Method</label>
                    <select id="payment-method-select"
                        class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="qris">QRIS</option>
                        <option value="e_wallet">E-Wallet</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Amount Received</label>
                    <input type="number" step="0.01" min="0" id="payment-amount-input" required
                        class="w-full rounded-lg border-slate-300 text-lg font-mono-num font-semibold focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>
            <div class="mt-5 flex justify-end gap-2">
                <button type="button" data-modal-close="payment-modal"
                    class="rounded-lg border border-slate-300 text-sm font-medium px-4 py-2 text-slate-600 hover:bg-slate-50">Cancel</button>
                <button type="submit"
                    class="rounded-lg bg-emerald-600 hover:bg-emerald-500 text-sm font-medium px-5 py-2 text-white">Complete
                    Sale</button>
            </div>
        </form>
    </x-modal-pos>

    {{-- Receipt confirmation modal --}}
    <x-modal-pos id="receipt-modal" title="Sale Complete" maxWidth="sm">
        <div class="text-center py-2">
            <div
                class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <p class="font-mono-num font-semibold text-lg" id="receipt-invoice-number">INV-0000</p>
            <p id="receipt-offline-note" class="hidden text-xs text-amber-600 mt-1">Saved offline — will sync
                automatically when back online.</p>

            <div class="mt-4 text-sm space-y-1">
                <div class="flex justify-between"><span class="text-slate-500">Total</span><span
                        class="font-mono-num font-medium" id="receipt-total">0.00</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Change</span><span
                        class="font-mono-num font-medium" id="receipt-change">0.00</span></div>
            </div>
        </div>
        <div class="mt-5 flex justify-center gap-2">
            <button type="button" data-modal-close="receipt-modal"
                class="rounded-lg bg-indigo-600 hover:bg-indigo-500 text-sm font-medium px-5 py-2 text-white">New
                Sale</button>
        </div>
    </x-modal-pos>

    {{-- One-time register pairing modal --}}
    <div id="pairing-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-sm w-full p-6">
            <h3 class="font-semibold text-slate-900 mb-1">Pair This Device</h3>
            <p class="text-sm text-slate-500 mb-4">Enter the register pairing token provided by your administrator.
                This only needs to be done once per device.</p>
            <form id="pairing-form">
                <input type="text" id="pairing-token-input" placeholder="Pairing token" required
                    class="w-full rounded-lg border-slate-300 text-sm font-mono-num focus:border-indigo-500 focus:ring-indigo-500">
                <button type="submit"
                    class="w-full mt-4 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium py-2.5">Pair
                    Device</button>
            </form>
        </div>
    </div>

    <script src="{{ asset('js/pos/db.js') }}"></script>
    <script src="{{ asset('js/pos/cart.js') }}"></script>
    <script src="{{ asset('js/pos/sync-queue.js') }}"></script>
    <script src="{{ asset('js/pos/register.js') }}"></script>
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js').catch(console.error);
        }
    </script>
</body>

</html>
