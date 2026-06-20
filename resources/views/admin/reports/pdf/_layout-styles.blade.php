<style>
    /* DomPDF-safe CSS only: no flexbox, no grid, no CSS variables.
       Summary stats use a <table> layout rather than inline-block/float,
       since DomPDF's box model support for those is inconsistent across
       versions -- a table row of cells renders reliably every time. */
    body {
        font-family: Helvetica, Arial, sans-serif;
        font-size: 10px;
        color: #1e293b;
        margin: 0;
        padding: 24px;
    }

    h1 {
        font-size: 16px;
        margin: 0 0 4px 0;
    }

    .subtitle {
        color: #64748b;
        font-size: 10px;
        margin-bottom: 16px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 8px;
    }

    th {
        background: #f1f5f9;
        text-align: left;
        padding: 6px 8px;
        font-size: 9px;
        text-transform: uppercase;
        color: #64748b;
        border-bottom: 1px solid #e2e8f0;
    }

    td {
        padding: 6px 8px;
        border-bottom: 1px solid #f1f5f9;
    }

    .right {
        text-align: right;
    }

    .totals-table td {
        font-weight: bold;
        border-top: 2px solid #1e293b;
        border-bottom: none;
    }

    .summary-table td {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 8px 12px;
        width: 25%;
    }

    .summary-table .label {
        display: block;
        color: #64748b;
        font-size: 8px;
        text-transform: uppercase;
        margin-bottom: 2px;
    }

    .summary-table .value {
        display: block;
        font-size: 13px;
        font-weight: bold;
    }
</style>
