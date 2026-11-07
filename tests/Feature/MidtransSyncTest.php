<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventTicketTier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MidtransSyncTest extends TestCase
{
    use RefreshDatabase;

    private Event $event;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAsAdmin();
        $this->event = Event::factory()->create(['is_published' => true]);

        EventTicketTier::factory()->create([
            'event_id' => $this->event->id,
            'name' => 'Regular',
            'price' => '150000.00',
            'fee' => '10000.00',
        ]);
    }

    public function test_midtrans_status_page_renders_checklist_and_items(): void
    {
        $response = $this->get(route('admin.events.midtrans.index', $this->event));

        $response->assertOk();
        $response->assertSee('Checklist Kesiapan');
        $response->assertSee('Item yang dikirim ke Midtrans');
        $response->assertSee('Regular');
        $response->assertSee('Rp 150.000');
    }

    public function test_midtrans_probe_button_uses_fake_http_and_reports_success(): void
    {
        config(['midtrans.server_key' => 'sandbox-server-key']);

        Http::fake([
            'https://app.sandbox.midtrans.com/snap/v1/transactions' => Http::response([
                'token' => 'probe-token-123',
                'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v4/redirection/probe-token-123',
            ], 201),
        ]);

        $response = $this->from(route('admin.events.midtrans.index', $this->event))
            ->post(route('admin.events.midtrans.test', $this->event->id), ['amount' => 1000]);

        $response->assertRedirect(route('admin.events.midtrans.index', $this->event));
        $response->assertSessionHas('success');
        $this->assertStringContainsString('Koneksi Midtrans OK', session('success'));
    }

    public function test_midtrans_probe_failure_is_reported_to_user(): void
    {
        config(['midtrans.server_key' => 'sandbox-server-key']);

        Http::fake([
            'https://app.sandbox.midtrans.com/snap/v1/transactions' => Http::response([
                'error_messages' => ['Invalid server key'],
            ], 401),
        ]);

        $response = $this->from(route('admin.events.midtrans.index', $this->event))
            ->post(route('admin.events.midtrans.test', $this->event->id), ['amount' => 1000]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertStringContainsString('Midtrans menolak', session('success'));
    }

    public function test_readiness_flags_published_free_tier(): void
    {
        $freeEvent = Event::factory()->create(['is_published' => true]);

        EventTicketTier::factory()->create([
            'event_id' => $freeEvent->id,
            'name' => 'Free',
            'price' => '0.00',
        ]);

        $response = $this->get(route('admin.events.midtrans.index', $freeEvent));

        $response->assertOk();
        $response->assertSee('Belum Siap');
        $response->assertSee('Minimal satu tier berbayar');
    }
}
