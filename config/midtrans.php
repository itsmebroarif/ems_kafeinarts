<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Konfigurasi Midtrans (Sandbox / Production)
    |--------------------------------------------------------------------------
    |
    | Kunci dibaca dari environment. Jangan pernah commit kunci asli.
    | Format sandbox: SB-Mid-server-... / SB-Mid-client-...
    */

    'server_key' => env('MIDTRANS_SERVER_KEY'),

    'client_key' => env('MIDTRANS_CLIENT_KEY'),

    // false = sandbox, true = production.
    'is_production' => (bool) env('MIDTRANS_IS_PRODUCTION', false),

    // Base URL Snap API (sandbox / production) — untuk membuat transaksi Snap.
    'snap_url' => env('MIDTRANS_SNAP_URL', 'https://app.sandbox.midtrans.com/snap/v1'),

    // Base URL API v2 (status transaksi / verifikasi ulang sisi server).
    'api_url' => env('MIDTRANS_API_URL', 'https://api.sandbox.midtrans.com/v2'),

    // Base URL untuk SnapJS (embed JS di halaman) — dipilih berdasarkan mode.
    'snap_js' => env('MIDTRANS_SNAP_JS', 'https://app.sandbox.midtrans.com/snap/snap.js'),
];
