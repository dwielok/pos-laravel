/**
 * In-memory cart state + pricing math for the POS register. This logic
 * intentionally MIRRORS PosService::checkout() on the server (same rounding,
 * same discount-then-tax order, same tax-inclusive handling) so that:
 *   1. the total the cashier sees and the customer pays is what gets
 *      charged, both online and offline, and
 *   2. when an offline sale syncs, the price-locked totals sent to the
 *      server are *consistent* with what the receipt already printed --
 *      the server does not recompute them, but they must still make sense
 *      as a self-consistent invoice if anyone audits it later.
 *
 * All money is handled in CENTS (integers) here too, for the same
 * floating-point-safety reason as the PHP Money class.
 */
class Cart {
    constructor() {
        this.items = []; // { product, quantity, discountType, discountValue }
        this.customer = null;
        this.orderDiscountType = null;
        this.orderDiscountValue = 0;
    }

    addItem(product, quantity = 1) {
        const existing = this.items.find(i => i.product.id === product.id);
        if (existing) {
            existing.quantity += quantity;
        } else {
            this.items.push({ product, quantity, discountType: null, discountValue: 0 });
        }
    }

    updateQuantity(productId, quantity) {
        const item = this.items.find(i => i.product.id === productId);
        if (!item) return;
        if (quantity <= 0) {
            this.removeItem(productId);
            return;
        }
        item.quantity = quantity;
    }

    setItemDiscount(productId, type, value) {
        const item = this.items.find(i => i.product.id === productId);
        if (item) {
            item.discountType = type;
            item.discountValue = value;
        }
    }

    setOrderDiscount(type, value) {
        this.orderDiscountType = type;
        this.orderDiscountValue = value;
    }

    removeItem(productId) {
        this.items = this.items.filter(i => i.product.id !== productId);
    }

    clear() {
        this.items = [];
        this.customer = null;
        this.orderDiscountType = null;
        this.orderDiscountValue = 0;
    }

    isEmpty() {
        return this.items.length === 0;
    }

    static calculateDiscount(baseCents, type, value) {
        if (!type || !value) return 0;
        if (type === 'fixed') return Math.min(baseCents, Math.round(value * 100));
        if (type === 'percent') return Math.round(baseCents * (Math.min(100, Math.max(0, value)) / 100));
        return 0;
    }

    /**
     * Computes the full line-by-line + order-level breakdown. Returns the
     * exact shape the offline-sync payload needs (see sync-queue.js) and
     * that the on-screen cart/receipt render from -- single source of
     * truth for "what does this cart cost".
     */
    computeTotals() {
        let subtotalCents = 0;
        let taxCents = 0;
        const lines = [];

        for (const item of this.items) {
            const unitPriceCents = item.product.selling_price_cents;
            const lineSubtotal = unitPriceCents * item.quantity;
            const lineDiscount = Cart.calculateDiscount(lineSubtotal, item.discountType, item.discountValue);
            const taxableAmount = lineSubtotal - lineDiscount;

            const taxRate = parseFloat(item.product.tax_rate_percent) || 0;
            const lineTax = item.product.is_tax_inclusive_price
                ? 0
                : Math.round(taxableAmount * (taxRate / 100));

            const lineTotal = taxableAmount + lineTax;

            lines.push({
                product_id: item.product.id,
                product_name_snapshot: item.product.name,
                product_sku_snapshot: item.product.sku,
                quantity: item.quantity,
                unit_price_cents: unitPriceCents,
                unit_cost_cents: item.product.cost_price_cents || 0,
                discount_cents: lineDiscount,
                discount_type: item.discountType,
                discount_value: item.discountValue || 0,
                tax_cents: lineTax,
                tax_rate_percent: taxRate,
                subtotal_cents: lineSubtotal,
                total_cents: lineTotal,
            });

            subtotalCents += lineSubtotal;
            taxCents += lineTax;
        }

        const orderDiscountCents = Cart.calculateDiscount(subtotalCents, this.orderDiscountType, this.orderDiscountValue);
        const totalCents = Math.max(0, subtotalCents - orderDiscountCents + taxCents);

        return {
            lines,
            subtotal_cents: subtotalCents,
            discount_cents: orderDiscountCents,
            discount_type: this.orderDiscountType,
            discount_value: this.orderDiscountValue || 0,
            tax_cents: taxCents,
            tax_rate_percent: subtotalCents > 0 ? Math.round((taxCents / subtotalCents) * 10000) / 100 : 0,
            total_cents: totalCents,
        };
    }
}
