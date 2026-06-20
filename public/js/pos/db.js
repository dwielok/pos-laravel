/**
 * IndexedDB layer for the offline-capable POS register, using Dexie for a
 * sane query API over raw IndexedDB. This is the local "source of truth"
 * while offline: products (cached catalog snapshot), customers, and a
 * pending_sales outbox of sales rung up with no connectivity, waiting to
 * sync.
 *
 * Database is scoped per-register (db name includes the register code) so
 * multiple registers sharing one browser profile (rare, but possible on a
 * shared kiosk) don't collide.
 */
class PosDatabase {
    constructor(registerCode) {
        this.db = new Dexie(`pos_register_${registerCode}`);

        this.db.version(1).stores({
            // Primary key 'id' = product_id from the server. Indexed on sku
            // and barcode for fast lookup during barcode scanning.
            products: 'id, sku, barcode, name',

            // Primary key 'id' = customer_id from the server.
            customers: 'id, phone, name',

            // Primary key 'client_uuid' generated in-browser at checkout time
            // -- this IS the idempotency key the server keys on too. 'status'
            // indexed so the sync queue can quickly grab all 'pending' rows.
            pending_sales: 'client_uuid, status, created_offline_at',

            // Single-row table holding catalog metadata (last sync time,
            // warehouse_id, guest_customer_id) -- key is always 'meta'.
            meta: 'key',
        });
    }

    async replaceCatalog(products, customers, meta) {
        await this.db.transaction('rw', this.db.products, this.db.customers, this.db.meta, async () => {
            await this.db.products.clear();
            await this.db.products.bulkPut(products);

            await this.db.customers.clear();
            await this.db.customers.bulkPut(customers);

            await this.db.meta.put({ key: 'catalog', ...meta, synced_at: new Date().toISOString() });
        });
    }

    async getCatalogMeta() {
        return this.db.meta.get('catalog');
    }

    async searchProducts(term, limit = 20) {
        const lower = term.toLowerCase();
        return this.db.products
            .filter(p =>
                p.name.toLowerCase().includes(lower) ||
                p.sku.toLowerCase().includes(lower) ||
                (p.barcode && p.barcode.includes(term))
            )
            .limit(limit)
            .toArray();
    }

    async findByBarcode(barcode) {
        return this.db.products.where('barcode').equals(barcode).first();
    }

    async getProduct(id) {
        return this.db.products.get(id);
    }

    async searchCustomers(term, limit = 10) {
        const lower = term.toLowerCase();
        return this.db.customers
            .filter(c => c.name.toLowerCase().includes(lower) || (c.phone && c.phone.includes(term)))
            .limit(limit)
            .toArray();
    }

    /**
     * Locally decrements the cached stock_quantity for a product after an
     * offline sale, so the NEXT sale on this same device (still offline)
     * reflects what was just sold -- without this, two offline sales in a
     * row on the same device would both see the original cached quantity.
     * This is purely a local UX nicety (shows the cashier a more accurate
     * "in stock" number); the server's StockService is still the real
     * source of truth and reconciles fully on sync regardless of what
     * this local count says.
     */
    async decrementLocalStock(productId, quantity) {
        const product = await this.db.products.get(productId);
        if (product) {
            product.stock_quantity = (product.stock_quantity || 0) - quantity;
            await this.db.products.put(product);
        }
    }

    async queueSale(sale) {
        await this.db.pending_sales.put({ ...sale, status: 'pending', queued_at: new Date().toISOString() });
    }

    async getPendingSales() {
        return this.db.pending_sales.where('status').equals('pending').toArray();
    }

    async getFailedSales() {
        return this.db.pending_sales.where('status').equals('failed').toArray();
    }

    async markSaleSynced(clientUuid) {
        await this.db.pending_sales.update(clientUuid, { status: 'synced', synced_at: new Date().toISOString() });
    }

    async markSaleFailed(clientUuid, errorMessage) {
        await this.db.pending_sales.update(clientUuid, { status: 'failed', last_error: errorMessage, last_attempt_at: new Date().toISOString() });
    }

    async markSaleRetrying(clientUuid) {
        await this.db.pending_sales.update(clientUuid, { status: 'pending' });
    }

    async countPending() {
        return this.db.pending_sales.where('status').anyOf(['pending', 'failed']).count();
    }
}
