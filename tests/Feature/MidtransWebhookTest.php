<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventTicketTier;
use App\Models\Registrant;
use App\Services\Midtrans\MidtransService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MidtransWebhookTest extends TestCase
{
    use RefreshDatabase;

    private Event $event;

    private Registrant $registrant;

    protected function setUp(): void
    {
        parent::setUp();

        config(['midtrans.server_key' => 'unit-test-server-key']);

        $this->event = Event::factory()->create();
        $tier = EventTicketTier::factory()->create([
            'event_id' => $this->event->id,
            'price' => '150000.00',
            'fee' => '10000.00',
        ]);

        $this->registrant = Registrant::factory()->create([
            'event_id' => $this->event->id,
            'ticket_tier_id' => $tier->id,
            'participant_token' => 'PART-MIDTRANS-WH1',
            'subtotal' => '150000.00',
            'total' => '150000.00',
            'fee' => '10000.00',
            'status' => 'pending',
            'is_paid' => false,
        ]);
    }

    public function test_valid_settlement_notification_marks_registrant_paid(): void
    {
        $payload = $this->notificationPayload('settlement', '200', '150000.00');

        $response = $this->postJson(route('midtrans.notification'), $payload);

        $response->assertOk();
        $response->assertJson(['status' => 'ok', 'transaction_status' => 'settlement']);

        $this->registrant->refresh();

        $this->assertTrue($this->registrant->is_paid);
        $this->assertSame('paid', $this->registrant->status);
        $this->assertSame('paid', $this->registrant->status);
    }

    public function test_notification_with_invalid_signature_is_rejected(): void
    {
        $payload = $this->notificationPayload('settlement', '200', '150000.00');
        $payload['signature_key'] = 'not-a-valid-signature';

        $response = $this->postJson(route('midtrans.notification'), $payload);

        $response->assertStatus(403);
        $this->registrant->refresh();
        $this->assertFalse($this->registrant->is_paid);
    }

    public function test_expired_notification_marks_registrant_cancelled(): void
    {
        $payload = $this->notificationPayload('expire', '407', '150000.00');

        $response = $this->postJson(route('midtrans.notification'), $payload);

        $response->assertOk();

        $this->registrant->refresh();

        $this->assertFalse($this->registrant->is_paid);
        $this->assertTrue($this->registrant->is_rejected);
        $this->assertSame('cancelled', $this->registrant->status);
    }

    public function test_gross_amount_mismatch_still_accepted_when_signature_valid(): void
    {
        // Signature dihitung atas gross_amount mana pun; Midtrans menjamin konsistensinya.
        $payload = $this->notificationPayload('settlement', '200', '150000.00', 'TXN-OFFSET-AMOUNT');

        $response = $this->postJson(route('midtrans.notification'), $payload);

        $response->assertOk();
        $this->assertTrue($this->registrant->fresh()->is_paid);
    }

    public function test_customer_email_returns_valid_email_as_is(): void
    {
        $registrant = $this->makeRegistrantWithEmail('budi@mail.com');

        $email = app(MidtransService::class)->customerEmail($registrant);

        $this->assertSame('budi@mail.com', $email);
    }

    public function test_customer_email_falls_back_for_reserved_tld(): void
    {
        $registrant = $this->makeRegistrantWithEmail('browser@demo.test');

        $email = app(MidtransService::class)->customerEmail($registrant);

        $this->assertStringStartsWith('customer-', $email);
        $this->assertStringEndsWith('@mail.com', $email);
        $this->assertNotSame('browser@demo.test', $email);
    }

    public function test_customer_email_falls_back_for_missing_domain_tld(): void
    {
        $registrant = $this->makeRegistrantWithEmail('test@test');

        $email = app(MidtransService::class)->customerEmail($registrant);

        $this->assertStringStartsWith('customer-', $email);
        $this->assertStringEndsWith('@mail.com', $email);
    }

    private function makeRegistrantWithEmail(string $email): Registrant
    {
        $tier = EventTicketTier::factory()->create([
            'event_id' => $this->event->id,
            'price' => '150000.00',
        ]);

        return Registrant::factory()->create([
            'event_id' => $this->event->id,
            'ticket_tier_id' => $tier->id,
            'email' => $email,
            'participant_token' => 'PART-EMAIL-'.uniqid(),
            'total' => '150000.00',
            'fee' => '0.00',
        ]);
    }

    /**
     * @return array<string, string>
     */
    private function notificationPayload(string $status, string $statusCode, string $grossAmount, ?string $transactionId = null): array
    {
        $orderId = $this->registrant->participant_token;
        $serverKey = config('midtrans.server_key');

        return [
            'transaction_id' => $transactionId ?? 'TXN-UNIT-'.strtoupper(uniqid()),
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'transaction_status' => $status,
            'transaction_time' => '2026-11-07 10:00:00',
            'signature_key' => hash('sha512', $orderId.$statusCode.$grossAmount.$serverKey),
        ];
    }
}
