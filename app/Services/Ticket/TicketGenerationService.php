<?php

namespace App\Services\Ticket;

use App\Models\Registrant;
use App\Models\TicketItem;
use App\Services\Ticket\Exceptions\TicketGenerationException;
use Dompdf\Dompdf;
use Dompdf\Options;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;

class TicketGenerationService
{
    public function generate(Registrant $registrant): TicketItem
    {
        $tier = $registrant->ticketTier;

        $ticket = TicketItem::create([
            'registrant_id' => $registrant->id,
            'ticket_tier_id' => $tier->id,
            'code' => 'KFT-'.strtoupper(uniqid()),
            'qr_code' => $this->generateQrCode($registrant->participant_token),
            'barcode' => $this->generateBarcode($registrant->participant_token),
            'is_used' => false,
            'barcode_type' => 'qrcode',
            'is_printed' => false,
        ]);

        event('event.ticket.generated', $ticket, $registrant);

        return $ticket;
    }

    public function export(Registrant $registrant): string
    {
        $ticketItem = $registrant->ticketItems()->first();

        if (! $ticketItem) {
            throw TicketGenerationException::noTicketGenerated($registrant->id);
        }

        $html = view('tickets.pdf', [
            'registrant' => $registrant,
            'event' => $registrant->event,
            'tier' => $ticketItem->ticketTier,
        ])->render();

        $filename = 'tickets/ticket-'.str_replace(['-', '_'], '', $registrant->id).'.pdf';

        Storage::disk('local')->put($filename, $this->renderPdf($html));

        return Storage::disk('local')->path($filename);
    }

    /**
     * Generate PDF surat checkout event (bukti pendaftaran & pembayaran peserta).
     */
    public function exportCheckoutLetter(Registrant $registrant): string
    {
        $html = view('tickets.checkout-letter', [
            'registrant' => $registrant,
            'event' => $registrant->event,
            'tier' => $registrant->ticketTier,
        ])->render();

        $filename = 'checkout/checkout-'.str_replace(['-', '_'], '', $registrant->id).'.pdf';

        Storage::disk('local')->put($filename, $this->renderPdf($html));

        return Storage::disk('local')->path($filename);
    }

    public function dompdf(string $html): Dompdf
    {
        $options = new Options;
        $options->setDefaultFont('Arial');
        $options->setIsHtml5ParserEnabled(true);

        // Dompdf v3 tidak lagi menerima HTML lewat konstruktor; harus loadHtml().
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);

        return $dompdf;
    }

    /**
     * Render HTML menjadi binary PDF siap disimpan.
     *
     * Catatan: output() saja tidak cukup — konten baru ada setelah render(),
     * tanpa render() yang keluar hanya kanvas kosong.
     */
    private function renderPdf(string $html): string
    {
        $dompdf = $this->dompdf($html);
        $dompdf->render();

        return $dompdf->output();
    }

    public function generateQrCode(string $payload): string
    {
        return 'data:image/png;base64,'.base64_encode($this->renderQr($payload));
    }

    public function renderQr(string $payload): string
    {
        $qrCode = new QrCode($payload);
        $qrCode->setSize(200);

        return (new PngWriter)->write($qrCode)->getString();
    }

    public function generateBarcode(string $payload): string
    {
        return 'code128:'.$payload;
    }
}
