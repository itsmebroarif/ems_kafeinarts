<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventTicketTier;
use App\Models\Registrant;
use App\Models\TicketItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParticipantPrintDocumentTest extends TestCase
{
    use RefreshDatabase;

    private Event $event;

    private EventTicketTier $tier;

    private Registrant $registrant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->event = Event::factory()->create(['is_published' => true]);
        $this->tier = EventTicketTier::factory()->create([
            'event_id' => $this->event->id,
            'price' => '150000.00',
            'fee' => '5000.00',
        ]);
        $this->registrant = Registrant::factory()->paid()->create([
            'event_id' => $this->event->id,
            'ticket_tier_id' => $this->tier->id,
            'participant_token' => 'PART-PRINTTEST01',
            'subtotal' => '150000.00',
            'addons_total' => '0.00',
            'discount' => '0.00',
            'total' => '150000.00',
            'fee' => '5000.00',
            'exchange_id' => 'EXCH-123',
        ]);

        TicketItem::factory()->create([
            'registrant_id' => $this->registrant->id,
            'ticket_tier_id' => $this->tier->id,
            'qr_code' => 'data:image/png;base64,'.base64_encode('fake-png'),
        ]);
    }

    public function test_ticket_page_shows_event_and_document_links(): void
    {
        $response = $this->get(route('public.participants.ticket', $this->registrant->participant_token));

        $response->assertOk();
        $response->assertSee($this->event->name);
        $response->assertSee('Cetak Tiket', false);
        $response->assertSee(route('public.participants.ticket.print', $this->registrant->participant_token), false);
        $response->assertSee(route('public.participants.checkout-letter', $this->registrant->participant_token), false);
    }

    public function test_ticket_print_page_renders_a_ready_to_print_sheet(): void
    {
        $response = $this->get(route('public.participants.ticket.print', $this->registrant->participant_token));

        $response->assertOk();
        $response->assertSee('ticket-print-sheet', false);
        $response->assertSee($this->event->name);
        $response->assertSee($this->registrant->full_name);
        $response->assertSee('window.print()', false);
    }

    public function test_checkout_letter_page_renders_the_letter(): void
    {
        $response = $this->get(route('public.participants.checkout-letter', $this->registrant->participant_token));

        $response->assertOk();
        $response->assertSee('Surat Checkout Event');
        $response->assertSee('checkout-sheet', false);
        $response->assertSee($this->registrant->full_name);
        $response->assertSee($this->event->name);
        $response->assertSee('window.print()', false);
    }

    public function test_ticket_download_returns_a_non_empty_pdf(): void
    {
        $response = $this->get(route('public.participants.ticket.download', $this->registrant->participant_token));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');

        // BinaryFileResponse::getContent() selalu false; baca langsung dari file.
        $this->assertPdfIsNotEmpty($response->getFile()->getPathname());
    }

    public function test_checkout_letter_download_returns_a_non_empty_pdf(): void
    {
        $response = $this->get(route('public.participants.checkout-letter.download', $this->registrant->participant_token));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');

        $this->assertPdfIsNotEmpty($response->getFile()->getPathname());
    }

    /**
     * PDF tanpa render() hanya berisi header + kanvas kosong (±900 byte).
     */
    private function assertPdfIsNotEmpty(string $path): void
    {
        $this->assertFileExists($path);

        $content = file_get_contents($path);

        $this->assertNotFalse($content);
        $this->assertStringStartsWith('%PDF', $content);
        $this->assertGreaterThan(3000, strlen($content));
    }

    public function test_download_routes_reject_unknown_token(): void
    {
        $this->get(route('public.participants.ticket.download', 'PART-DOES-NOT-EXIST'))->assertNotFound();
        $this->get(route('public.participants.checkout-letter.download', 'PART-DOES-NOT-EXIST'))->assertNotFound();
        $this->get(route('public.participants.ticket.print', 'PART-DOES-NOT-EXIST'))->assertNotFound();
    }
}
