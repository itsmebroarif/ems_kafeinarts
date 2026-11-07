<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Registrant;
use App\Services\Payment\PaymentSimulationService;
use App\Services\Ticket\TicketGenerationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ParticipantController extends Controller
{
    public function __construct(
        private readonly TicketGenerationService $ticket,
        private readonly PaymentSimulationService $payment,
    ) {}

    public function payment(Request $request, string $token): View
    {
        $registrant = $this->findRegistrant($token);

        $simulation = $this->payment->simulate($registrant);

        return view('public.participants.payment', [
            'registrant' => $registrant,
            'simulation' => $simulation,
            'token' => $token,
        ]);
    }

    public function paymentStore(Request $request, string $token): RedirectResponse
    {
        $registrant = $this->findRegistrant($token);

        $validated = $request->validate([
            'payment_method' => ['required', 'string', 'max:255'],
            'payment_provider' => ['nullable', 'string', 'max:255'],
            'exchange_id' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'max:255'],
            'ip_address' => ['nullable', 'string', 'max:255'],
            'device_type' => ['nullable', 'string', 'max:255'],
            'os_version' => ['nullable', 'string', 'max:255'],
            'browser' => ['nullable', 'string', 'max:255'],
            'browser_version' => ['nullable', 'string', 'max:255'],
            'locale' => ['nullable', 'string', 'max:255'],
        ]);

        $result = $this->payment->capture($registrant, $validated);

        return redirect()->route('public.participants.done', $token)->with([
            'payment' => $result,
        ]);
    }

    public function done(string $token): View
    {
        $registrant = $this->findRegistrant($token);

        return view('public.participants.done', [
            'registrant' => $registrant,
        ]);
    }

    public function ticket(string $token): View
    {
        $registrant = $this->findRegistrant($token);

        return view('public.participants.ticket', [
            'registrant' => $registrant,
            'event' => $registrant->event,
        ]);
    }

    /**
     * Halaman cetak tiket: tampilan kertas A4 siap print langsung dari browser.
     */
    public function ticketPrint(string $token): View
    {
        $registrant = $this->findRegistrant($token);

        return view('public.participants.ticket-print', [
            'registrant' => $registrant,
            'event' => $registrant->event,
            'tier' => $registrant->ticketTier,
            'ticket' => $registrant->ticketItems()->first(),
        ]);
    }

    /**
     * Surat checkout event (bukti pendaftaran & pembayaran) versi HTML untuk dipreview/dicetak.
     */
    public function checkoutLetter(string $token): View
    {
        $registrant = $this->findRegistrant($token);

        return view('public.participants.checkout-letter', [
            'registrant' => $registrant,
            'event' => $registrant->event,
            'tier' => $registrant->ticketTier,
        ]);
    }

    /**
     * Unduh surat checkout event dalam format PDF.
     */
    public function checkoutLetterDownload(string $token): BinaryFileResponse
    {
        $registrant = $this->findRegistrant($token);

        $path = $this->ticket->exportCheckoutLetter($registrant);

        return response()->download($path)->deleteFileAfterSend(true);
    }

    public function ticketDownload(string $token): BinaryFileResponse
    {
        $registrant = $this->findRegistrant($token);

        $path = $this->ticket->export($registrant);

        return response()->download($path)->deleteFileAfterSend(true);
    }

    /**
     * Ambil registrant berdasar token peserta beserta relasi yang dipakai halaman cetak.
     */
    private function findRegistrant(string $token): Registrant
    {
        return Registrant::with([
            'event',
            'event.category',
            'ticketTier',
            'ticketItems',
            'addons.addon',
            'voucher',
        ])
            ->where('participant_token', $token)
            ->where('is_deleted', false)
            ->firstOrFail();
    }
}
