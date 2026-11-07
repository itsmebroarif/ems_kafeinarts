<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\Midtrans\MidtransService;
use App\Services\Midtrans\MidtransSyncService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MidtransSyncController extends Controller
{
    public function __construct(
        private readonly MidtransService $midtrans,
        private readonly MidtransSyncService $sync,
    ) {}

    /**
     * Halaman status Midtrans per event: checklist, preview item_details, & test koneksi.
     */
    public function index(Event $event): View
    {
        $event->loadMissing(['tiers', 'addons']);

        return view('admin.events.midtrans', [
            'event' => $event,
            'readiness' => $this->sync->readiness($event),
            'items' => $this->sync->itemDetails($event),
            'isReady' => $this->sync->isReady($event),
            'isConfigured' => $this->midtrans->isConfigured(),
        ]);
    }

    /**
     * Uji koneksi ke Midtrans Snap API secara langsung (dipakai tombol di halaman status).
     */
    public function test(Request $request, Event $event): RedirectResponse
    {
        $amount = (int) $request->input('amount', 1000);

        $result = $this->midtrans->probeConnection($amount);

        $message = match (true) {
            $result['token'] !== null => sprintf(
                'Koneksi Midtrans OK. Transaksi tes "%s" berhasil dibuat (Rp %s).',
                $result['order_id'],
                number_format($amount, 0, ',', '.'),
            ),
            $result['message'] !== null => 'Midtrans menolak: '.$result['message'],
            default => 'Gagal terhubung ke Midtrans (HTTP '.$result['http_status'].').',
        };

        return back()->with('midtrans_test', $result)->with('success', $message);
    }
}
