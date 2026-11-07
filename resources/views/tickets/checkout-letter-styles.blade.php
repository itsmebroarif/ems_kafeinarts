<style>
    @page {
        size: A4;
        margin: 16mm;
    }

    .checkout-sheet {
        font-family: 'Helvetica', 'Arial', sans-serif;
        color: #1a1a1a;
        font-size: 12px;
        background: #fff;
        padding: 28px;
        border: 1px solid #dee2e6;
        box-shadow: 0 .125rem .25rem rgba(0, 0, 0, .075);
    }

    .checkout-sheet .kop {
        border-bottom: 3px double #1a1a1a;
        padding-bottom: 10px;
        margin-bottom: 6px;
    }

    .checkout-sheet .kop table {
        width: 100%;
        border-collapse: collapse;
    }

    .checkout-sheet .kop td {
        vertical-align: middle;
    }

    .checkout-sheet .kop-logo {
        width: 64px;
        height: 64px;
        border: 2px solid #1a1a1a;
        border-radius: 8px;
        text-align: center;
        line-height: 60px;
        font-size: 26px;
        font-weight: bold;
        color: #2d6a4f;
    }

    .checkout-sheet .kop-org {
        font-size: 18px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .checkout-sheet .kop-sub {
        font-size: 11px;
        color: #444;
    }

    .checkout-sheet .kop-meta {
        text-align: right;
        font-size: 11px;
        line-height: 1.6;
    }

    .checkout-sheet .doc-title {
        text-align: center;
        font-size: 15px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 2px;
        margin: 18px 0 2px;
    }

    .checkout-sheet .doc-number {
        text-align: center;
        font-size: 11px;
        color: #444;
        margin-bottom: 18px;
    }

    .checkout-sheet .intro {
        margin-bottom: 12px;
        line-height: 1.7;
    }

    .checkout-sheet h2.section {
        font-size: 12px;
        text-transform: uppercase;
        margin: 16px 0 6px;
        padding: 4px 8px;
        background: #f1f3f5;
        border-left: 4px solid #2d6a4f;
    }

    .checkout-sheet table.data {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 6px;
    }

    .checkout-sheet table.data td {
        padding: 5px 8px;
        border-bottom: 1px solid #e9ecef;
        vertical-align: top;
    }

    .checkout-sheet table.data td.label {
        width: 170px;
        color: #555;
    }

    .checkout-sheet table.data td.value {
        font-weight: bold;
    }

    .checkout-sheet table.items {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 8px;
    }

    .checkout-sheet table.items th,
    .checkout-sheet table.items td {
        padding: 6px 8px;
        border: 1px solid #dee2e6;
        text-align: left;
    }

    .checkout-sheet table.items th {
        background: #f1f3f5;
        font-size: 11px;
        text-transform: uppercase;
    }

    .checkout-sheet table.items td.num,
    .checkout-sheet table.items th.num {
        text-align: right;
    }

    .checkout-sheet table.total {
        width: 60%;
        margin-left: auto;
        border-collapse: collapse;
    }

    .checkout-sheet table.total td {
        padding: 5px 8px;
        border-bottom: 1px solid #e9ecef;
    }

    .checkout-sheet table.total td.num {
        text-align: right;
    }

    .checkout-sheet table.total tr.grand td {
        border-top: 2px solid #1a1a1a;
        border-bottom: none;
        font-size: 14px;
        font-weight: bold;
    }

    .checkout-sheet .status-box {
        border: 1px solid #1a1a1a;
        padding: 8px 10px;
        margin-top: 10px;
    }

    .checkout-sheet .status-paid {
        color: #2d6a4f;
        font-weight: bold;
    }

    .checkout-sheet .status-unpaid {
        color: #c0392b;
        font-weight: bold;
    }

    .checkout-sheet .closing {
        margin-top: 20px;
        line-height: 1.7;
    }

    .checkout-sheet .signatures {
        width: 100%;
        border-collapse: collapse;
        margin-top: 34px;
    }

    .checkout-sheet .signatures td {
        width: 50%;
        text-align: center;
        vertical-align: top;
    }

    .checkout-sheet .sign-name {
        margin-top: 46px;
        font-weight: bold;
        text-decoration: underline;
    }

    .checkout-sheet .footer-note {
        margin-top: 22px;
        padding-top: 8px;
        border-top: 1px solid #adb5bd;
        font-size: 10px;
        color: #666;
    }

    /* Versi cetak: hanya kertas surat yang ikut tercetak. */
    @media print {
        .checkout-sheet {
            border: none;
            box-shadow: none;
            padding: 0;
        }

        body * {
            visibility: hidden;
        }

        .checkout-sheet,
        .checkout-sheet * {
            visibility: visible;
        }

        .checkout-sheet {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
    }
</style>
