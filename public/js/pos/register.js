/**
 * Main controller for the POS register screen. Wires together: db.js
 * (IndexedDB), cart.js (pricing), sync-queue.js (connectivity/sync), and
 * the jQuery UI in register.blade.php.
 *
 * Checkout flow (the core of the offline-first design):
 *   1. Cashier completes a sale -> build the payload from Cart.computeTotals()
 *   2. Generate client_uuid HERE, in the browser, BEFORE any network call.
 *   3. Try the online checkout endpoint first.
 *      - Success -> done, show receipt, clear cart.
 *      - Network failure (not a validation error) -> queue in IndexedDB
 *        as a pending_sale and show receipt from LOCAL data immediately --
 *        the cashier and customer should never be blocked by connectivity.
 *   4. The SyncQueue drains pending_sales whenever connectivity returns.
 */
$(function () {
    const $app = $('#pos-app');

    $("#pairing-form").on('submit', function (e) {
        // e.preventDefault();
        const token = $('#pairing-token-input').val().trim();
        if (!token) return;
        localStorage.setItem('pos_register_token', token);
        window.location.reload();
    });

    // One physical device is paired to exactly one register, so storage is
    // keyed by a single fixed name -- not by a "register code" we can't
    // actually know before pairing happens (pairing IS what tells us which
    // register/warehouse this device belongs to).
    let registerToken = localStorage.getItem('pos_register_token');
    let warehouseId = localStorage.getItem('pos_warehouse_id');

    if (!registerToken) {
        showPairingPrompt();
        return;
    }

    // IndexedDB is namespaced by token rather than a human-readable code --
    // tokens are unique per register by construction, which a guessed code
    // is not.
    const db = new PosDatabase(registerToken.slice(0, 12));
    const cart = new Cart();

    const syncQueue = new SyncQueue(db, registerToken, {
        onStatusChange: updateConnectionStatus,
        onSaleSynced: (sale, result) => {
            console.info(`Synced offline sale ${sale.client_uuid} -> invoice ${result.invoice_number}`);
            renderPendingBadge();
        },
        onSaleFailed: (sale, message) => {
            console.warn(`Failed to sync sale ${sale.client_uuid}: ${message}`);
            renderPendingBadge();
        },
    });

    // --- Boot sequence ------------------------------------------------------
    init();

    async function init() {
        // Resolve (and persist) this device's warehouse_id from the server
        // on first online boot, since pairing only gives us a token -- the
        // warehouse it belongs to is authoritative server-side.
        if (!warehouseId && navigator.onLine) {
            try {
                const response = await fetch('/api/v1/ping', { headers: syncQueue.headers() });
                if (response.ok) {
                    const data = await response.json();
                    warehouseId = data.warehouse_id;
                    localStorage.setItem('pos_warehouse_id', warehouseId);
                }
            } catch (e) { /* will retry on next online event */ }
        }
        $app.attr('data-warehouse-id', warehouseId || '');

        await syncQueue.refreshCatalog().catch(() => { });
        await syncQueue.syncPendingSales();
        renderPendingBadge();
        updateConnectionStatus(navigator.onLine ? 'online' : 'offline');

        // Periodic background sync + catalog refresh while online -- catches
        // sales that failed transiently and keeps prices/stock reasonably
        // fresh without requiring a manual page reload.
        setInterval(() => syncQueue.syncPendingSales(), 30000);
        setInterval(() => { if (navigator.onLine) syncQueue.refreshCatalog(); }, 5 * 60000);

        registerEventHandlers();
    }

    function showPairingPrompt() {
        $('#pairing-modal').removeClass('hidden');
    }

    // --- Connection status indicator -----------------------------------------
    function updateConnectionStatus(status, meta = {}) {
        const $indicator = $('#connection-status');
        const states = {
            online: { text: 'Online', class: 'bg-emerald-100 text-emerald-700' },
            offline: { text: 'Offline — sales will sync later', class: 'bg-amber-100 text-amber-800' },
            syncing: { text: `Syncing ${meta.count || ''} sale(s)...`, class: 'bg-indigo-100 text-indigo-700' },
            synced: { text: 'Online', class: 'bg-emerald-100 text-emerald-700' },
            partial: { text: `${meta.remaining} sale(s) pending sync`, class: 'bg-amber-100 text-amber-800' },
        };
        const s = states[status] || states.offline;
        $indicator.attr('class', `inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-medium ${s.class}`).text(s.text);
    }

    async function renderPendingBadge() {
        const count = await db.countPending();
        $('#pending-sync-badge').toggleClass('hidden', count === 0).text(count);
    }

    // --- Product search & barcode scanning -----------------------------------
    let searchDebounce;
    $('#product-search-input').on('input', function () {
        clearTimeout(searchDebounce);
        const term = $(this).val().trim();
        // if (term.length < 1) {
        //     $('#search-results').empty();
        //     return;
        // }
        searchDebounce = setTimeout(() => runSearch(term), 200);
    });

    runSearch("")

    async function runSearch(term) {
        let results;

        if (navigator.onLine) {
            try {
                const response = await fetch(`/api/v1/products/search?q=${encodeURIComponent(term)}`, {
                    headers: syncQueue.headers(),
                });
                if (response.ok) {
                    results = (await response.json()).data;
                }
            } catch (e) { /* fall through to local search */ }
        }

        if (!results) {
            results = await db.searchProducts(term);
        }

        renderSearchResults(results);
    }

    function renderSearchResults(products) {
        const $results = $('#search-results').empty();

        if (products.length === 0) {
            $results.append(`
            <div class="col-span-full text-center text-slate-400 py-8">
                No products found.
            </div>
        `);
            return;
        }

        products.forEach(p => {
            const price = (p.selling_price_cents).toFixed(0);
            const lowStock = p.track_stock && p.stock_quantity <= 0;

            $results.append(`
            <button
                type="button"
                class="product-result flex flex-col bg-white border border-slate-200 rounded-xl overflow-hidden hover:border-indigo-500 hover:shadow-md transition-all text-left"
                data-product-id="${p.id}"
            >
                <div class="w-full aspect-[4/3] bg-slate-100 flex items-center justify-center shrink-0">
                    ${p.image_url
                    ? `
                            <img
                                src="${p.image_url}"
                                alt="${escapeHtml(p.name)}"
                                class="w-full h-full object-cover"
                                loading="lazy"
                                onerror="this.parentElement.innerHTML='<div class=&quot;flex items-center justify-center w-full h-full text-slate-300&quot;><svg class=&quot;w-8 h-8&quot; fill=&quot;none&quot; stroke=&quot;currentColor&quot; viewBox=&quot;0 0 24 24&quot;><path stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot; stroke-width=&quot;1.5&quot; d=&quot;M4 16l4-4a3 3 0 014.243 0L20 20M14 14l1-1a3 3 0 014.243 0L20 14M4 8h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z&quot;/></svg></div>';"
                            >
                        `
                    : `
                            <div class="flex items-center justify-center w-full h-full text-slate-300">
                                <svg class="w-30 h-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M4 16l4-4a3 3 0 014.243 0L20 20M14 14l1-1a3 3 0 014.243 0L20 14M4 8h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        `
                }
                </div>

                <div class="flex flex-col flex-1 p-3 w-full">
                    <div class="mb-2">
                        <h3 class="font-medium text-sm text-slate-900 line-clamp-2 leading-tight">
                            ${escapeHtml(p.name)}
                        </h3>
                        <p class="text-xs text-slate-500 mt-1 truncate">
                            ${escapeHtml(p.sku)}
                        </p>
                    </div>

                    <div class="mt-auto">
                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-indigo-600 font-mono-num text-sm md:text-base">
                                Rp ${price}
                            </span>
                        </div>
                    </div>
                </div>
            </button>
        `);
        });

        $results.find('.product-result').on('click', async function () {
            const productId = parseInt($(this).data('product-id'));
            const product = await db.getProduct(productId) || products.find(p => p.id === productId);
            addToCart(product);
        });
    }

    // Barcode scanners typically act as a fast keyboard: a stream of
    // digit keystrokes ending in Enter. We listen globally (not just on
    // the search input) so a cashier can scan without clicking into a
    // field first, but ignore the stream if focus is in a text input
    // that isn't the dedicated barcode field, to avoid hijacking normal typing.
    let barcodeBuffer = '';
    let barcodeTimer;
    $(document).on('keydown', function (e) {
        const tag = document.activeElement.tagName;
        const isTypingField = tag === 'INPUT' || tag === 'TEXTAREA';
        if (isTypingField && document.activeElement.id !== 'barcode-input') return;

        if (e.key === 'Enter' && barcodeBuffer.length >= 4) {
            handleBarcodeScanned(barcodeBuffer);
            barcodeBuffer = '';
            return;
        }

        if (/^[0-9]$/.test(e.key)) {
            barcodeBuffer += e.key;
            clearTimeout(barcodeTimer);
            barcodeTimer = setTimeout(() => { barcodeBuffer = ''; }, 300); // scanner keystrokes arrive fast; reset if gap is human-typing-speed
        }
    });

    async function handleBarcodeScanned(barcode) {
        let product = await db.findByBarcode(barcode);

        if (!product && navigator.onLine) {
            try {
                const response = await fetch(`/api/v1/products/barcode/${encodeURIComponent(barcode)}`, {
                    headers: syncQueue.headers(),
                });
                if (response.ok) product = (await response.json()).data;
            } catch (e) { /* ignore, handled below */ }
        }

        if (!product) {
            flashMessage(`Barcode ${barcode} not found.`, 'error');
            return;
        }

        addToCart(product);
        flashMessage(`Added ${product.name}`, 'success');
    }

    // --- Cart rendering --------------------------------------------------------
    function addToCart(product) {
        if (!product) return;
        cart.addItem(product, 1);
        renderCart();
    }

    function renderCart() {
        const $cartItems = $('#cart-items').empty();
        const totals = cart.computeTotals();

        if (cart.items.length === 0) {
            $cartItems.append('<p class="text-sm text-slate-400 text-center py-8">Cart is empty</p>');
        }

        cart.items.forEach(item => {
            const lineTotal = ((item.product.selling_price_cents * item.quantity)).toFixed(0);
            $cartItems.append(`
                <div class="flex items-center gap-2 py-2 border-b border-slate-100" data-product-id="${item.product.id}">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-900 truncate">${escapeHtml(item.product.name)}</p>
                        <p class="text-xs text-slate-400 font-mono-num">${(item.product.selling_price_cents).toFixed(0)} each</p>
                    </div>
                    <div class="flex items-center gap-1">
                        <button class="qty-decrease w-7 h-7 rounded-md border border-slate-200 text-slate-500 hover:bg-slate-50">−</button>
                        <input type="number" class="qty-input w-12 text-center text-sm rounded-md border-slate-200 font-mono-num" value="${item.quantity}" min="1">
                        <button class="qty-increase w-7 h-7 rounded-md border border-slate-200 text-slate-500 hover:bg-slate-50">+</button>
                    </div>
                    <p class="w-16 text-right font-mono-num text-sm font-medium">${lineTotal}</p>
                    <button class="remove-item text-slate-300 hover:text-red-500 ml-1">&times;</button>
                </div>
            `);
        });

        $('#cart-subtotal').text((totals.subtotal_cents).toFixed(0));
        $('#cart-discount').text((totals.discount_cents).toFixed(0));
        $('#cart-tax').text((totals.tax_cents).toFixed(0));
        $('#cart-total').text((totals.total_cents).toFixed(0));
        $('#checkout-btn').prop('disabled', cart.isEmpty());
    }

    $('#cart-items').on('click', '.qty-increase', function () {
        const productId = parseInt($(this).closest('[data-product-id]').data('product-id'));
        const item = cart.items.find(i => i.product.id === productId);
        cart.updateQuantity(productId, item.quantity + 1);
        renderCart();
    });

    $('#cart-items').on('click', '.qty-decrease', function () {
        const productId = parseInt($(this).closest('[data-product-id]').data('product-id'));
        const item = cart.items.find(i => i.product.id === productId);
        cart.updateQuantity(productId, item.quantity - 1);
        renderCart();
    });

    $('#cart-items').on('change', '.qty-input', function () {
        const productId = parseInt($(this).closest('[data-product-id]').data('product-id'));
        cart.updateQuantity(productId, parseInt($(this).val()) || 1);
        renderCart();
    });

    $('#cart-items').on('click', '.remove-item', function () {
        const productId = parseInt($(this).closest('[data-product-id]').data('product-id'));
        cart.removeItem(productId);
        renderCart();
    });

    $('#clear-cart-btn').on('click', function () {
        cart.clear();
        renderCart();
    });

    // --- Checkout -----------------------------------------------------------
    $('#checkout-btn').on('click', function () {
        if (cart.isEmpty()) return;
        $('#payment-modal').removeClass('hidden');
        $('#payment-amount-input').val((cart.computeTotals().total_cents).toFixed(0)).focus();
    });

    $('#payment-form').on('submit', async function (e) {
        e.preventDefault();

        const totals = cart.computeTotals();
        const paidAmount = parseFloat($('#payment-amount-input').val()) || 0;
        const paidCents = Math.round(paidAmount);

        if (paidCents < totals.total_cents) {
            flashMessage('Payment amount is less than the total due.', 'error');
            return;
        }

        const clientUuid = crypto.randomUUID();
        const method = $('#payment-method-select').val();

        const payload = {
            client_uuid: clientUuid,
            customer_id: cart.customer ? cart.customer.id : null,
            created_offline_at: new Date().toISOString(),
            notes: null,
            ...totals,
            change_cents: Math.max(0, paidCents - totals.total_cents),
            paid_cents: paidCents,
            items: totals.lines,
            payments: [{ method, amount_cents: paidCents, reference_number: null }],
        };
        delete payload.lines; // 'lines' renamed to 'items' above; avoid sending both

        $('#payment-modal').addClass('hidden');
        await completeSale(payload);
    });

    async function completeSale(payload) {
        let result = null;
        let wasOffline = false;

        if (navigator.onLine) {
            try {
                const response = await fetch('/pos/checkout', {
                    method: 'POST',
                    headers: syncQueue.headers(),
                    body: JSON.stringify({
                        client_uuid: payload.client_uuid,
                        customer_id: payload.customer_id,
                        warehouse_id: warehouseId,
                        discount_type: payload.discount_type,
                        discount_value: payload.discount_value,
                        items: payload.items.map(i => ({
                            product_id: i.product_id, quantity: i.quantity,
                            discount_type: i.discount_type, discount_value: i.discount_value,
                        })),
                        payments: payload.payments.map(p => ({
                            method: p.method, amount: p.amount_cents, reference_number: p.reference_number,
                        })),
                    }),
                });

                if (response.ok) {
                    result = await response.json();
                } else if (response.status >= 400 && response.status < 500) {
                    const body = await response.json().catch(() => ({}));
                    flashMessage(body.message || 'Checkout failed.', 'error');
                    return; // validation error -- do NOT queue offline, the cart needs fixing
                } else {
                    wasOffline = true;
                }
            } catch (e) {
                wasOffline = true;
            }
        } else {
            wasOffline = true;
        }

        if (wasOffline) {
            await db.queueSale({ client_uuid: payload.client_uuid, payload, created_offline_at: payload.created_offline_at });

            for (const line of payload.items) {
                await db.decrementLocalStock(line.product_id, line.quantity);
            }

            renderPendingBadge();
            showReceipt({ ...payload, invoice_number: 'PENDING SYNC', offline: true });
            syncQueue.syncPendingSales(); // attempt immediately in case connectivity is actually fine
        } else {
            showReceipt({ ...payload, invoice_number: result.invoice_number, offline: false });
        }

        cart.clear();
        renderCart();
    }

    function showReceipt(sale) {
        const $modal = $('#receipt-modal');
        $modal.find('#receipt-invoice-number').text(sale.invoice_number);
        $modal.find('#receipt-offline-note').toggleClass('hidden', !sale.offline);
        $modal.find('#receipt-total').text((sale.total_cents).toFixed(0));
        $modal.find('#receipt-change').text((sale.change_cents).toFixed(0));
        $modal.removeClass('hidden');
    }

    $(document).on('click', '[data-modal-close]', function () {
        // shared admin.js modal-close handler also covers this; explicit
        // here too in case register.blade.php is used standalone.
        $('#' + $(this).data('modal-close')).addClass('hidden');
    });

    function registerEventHandlers() {
        renderCart();
    }

    function flashMessage(text, type = 'success') {
        const colors = { success: 'bg-emerald-600', error: 'bg-red-600' };
        const $flash = $(`<div class="fixed bottom-4 right-4 ${colors[type]} text-white text-sm font-medium px-4 py-2.5 rounded-lg shadow-lg z-50">${escapeHtml(text)}</div>`);
        $('body').append($flash);
        setTimeout(() => $flash.fadeOut(300, () => $flash.remove()), 2500);
    }

    function escapeHtml(str) {
        return $('<div>').text(str ?? '').html();
    }
});
