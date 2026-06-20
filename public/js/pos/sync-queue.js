/**
 * Background sync engine for the POS offline outbox. Responsibilities:
 *   1. Detect connectivity changes (online/offline events + an active
 *      ping, since `navigator.onLine` is unreliable -- it can report
 *      "online" for a connected-but-not-actually-working network).
 *   2. Drain pending_sales ONE AT A TIME (not batched) so a single bad
 *      sale never blocks the rest, and the server's idempotency check
 *      (client_uuid) makes every retry safe.
 *   3. Refresh the catalog snapshot periodically while online, so the
 *      offline cache doesn't go stale for days if a register is rarely
 *      restarted.
 *
 * This file has no UI logic -- register.js subscribes to the events this
 * class emits (via simple callback hooks) to update the on-screen sync
 * status indicator.
 */
class SyncQueue {
    constructor(db, registerToken, { onStatusChange, onSaleSynced, onSaleFailed } = {}) {
        this.db = db;
        this.registerToken = registerToken;
        this.onStatusChange = onStatusChange || (() => {});
        this.onSaleSynced = onSaleSynced || (() => {});
        this.onSaleFailed = onSaleFailed || (() => {});

        this.isSyncing = false;
        this.retryDelayMs = 5000;
        this.maxRetryDelayMs = 60000;

        window.addEventListener('online', () => this.handleConnectivityChange());
        window.addEventListener('offline', () => this.handleConnectivityChange());
    }

    headers(extra = {}) {
        return {
            'X-Register-Token': this.registerToken,
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            Accept: 'application/json',
            ...extra,
        };
    }

    async handleConnectivityChange() {
        const reachable = await this.checkServerReachable();
        this.onStatusChange(reachable ? 'online' : 'offline');

        if (reachable) {
            this.syncPendingSales();
        }
    }

    /**
     * Actively pings the server rather than trusting navigator.onLine alone
     * -- a device can be "connected" to wifi with no real internet path,
     * which navigator.onLine won't catch but a failed fetch will.
     */
    async checkServerReachable() {
        try {
            const response = await fetch('/api/v1/ping', {
                method: 'GET',
                headers: this.headers(),
                cache: 'no-store',
            });
            return response.ok;
        } catch (e) {
            return false;
        }
    }

    /**
     * Call this periodically (register.js sets an interval) and on every
     * connectivity-restored event. Safe to call concurrently with itself --
     * the isSyncing guard prevents overlapping drain passes.
     */
    async syncPendingSales() {
        if (this.isSyncing) return;
        this.isSyncing = true;

        try {
            const pending = await this.db.getPendingSales();
            const failed = await this.db.getFailedSales();
            const toSync = [...pending, ...failed];

            if (toSync.length === 0) {
                this.onStatusChange('synced');
                return;
            }

            this.onStatusChange('syncing', { count: toSync.length });

            for (const sale of toSync) {
                await this.syncOne(sale);
            }

            const remaining = await this.db.countPending();
            this.onStatusChange(remaining === 0 ? 'synced' : 'partial', { remaining });
        } finally {
            this.isSyncing = false;
        }
    }

    async syncOne(sale) {
        try {
            const response = await fetch('/api/v1/sales/sync', {
                method: 'POST',
                headers: this.headers(),
                body: JSON.stringify(sale.payload),
            });

            if (response.ok) {
                const result = await response.json();
                await this.db.markSaleSynced(sale.client_uuid);
                this.onSaleSynced(sale, result);
                return;
            }

            // 4xx = the server rejected the payload itself (validation error)
            // -- retrying without a fix would loop forever, so mark failed
            // and surface it for manual review rather than silently retrying.
            if (response.status >= 400 && response.status < 500) {
                const body = await response.json().catch(() => ({}));
                await this.db.markSaleFailed(sale.client_uuid, body.message || `HTTP ${response.status}`);
                this.onSaleFailed(sale, body.message);
                return;
            }

            // 5xx / network-adjacent failure -- worth retrying later.
            await this.db.markSaleFailed(sale.client_uuid, `Server error ${response.status}`);
            this.onSaleFailed(sale, `Server error ${response.status}`);
        } catch (e) {
            await this.db.markSaleFailed(sale.client_uuid, e.message);
            this.onSaleFailed(sale, e.message);
        }
    }

    /**
     * Manually re-queue a failed sale for another sync attempt (e.g. after
     * the cashier/admin reviews the error and confirms it's safe to retry).
     */
    async retrySale(clientUuid) {
        await this.db.markSaleRetrying(clientUuid);
        this.syncPendingSales();
    }

    async refreshCatalog() {
        try {
            const response = await fetch('/api/v1/catalog/snapshot', {
                method: 'GET',
                headers: this.headers(),
                cache: 'no-store',
            });

            if (!response.ok) return false;

            const data = await response.json();
            await this.db.replaceCatalog(data.products, data.customers, {
                warehouse_id: data.warehouse_id,
                guest_customer_id: data.guest_customer_id,
            });

            return true;
        } catch (e) {
            return false;
        }
    }
}
