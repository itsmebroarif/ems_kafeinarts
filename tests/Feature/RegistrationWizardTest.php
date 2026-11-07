<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventAddon;
use App\Models\EventTicketTier;
use App\Models\Registrant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationWizardTest extends TestCase
{
    use RefreshDatabase;

    private Event $event;

    private EventTicketTier $tier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->event = Event::factory()->create(['is_published' => true]);
        $this->tier = EventTicketTier::factory()->create([
            'event_id' => $this->event->id,
            'price' => '150000.00',
            'fee' => '5000.00',
        ]);
    }

    public function test_stage_page_renders_wizard_steps(): void
    {
        $response = $this->get(route('public.events.stage', [
            'slug' => $this->event->slug,
            'tier_id' => $this->tier->id,
        ]));

        $response->assertOk();
        $response->assertSee('Data Diri');
        $response->assertSee('Tiket &amp; Add-on', false);
        $response->assertSee('Konfirmasi');
        $response->assertSee('registrationWizard', false);
        $response->assertSee('data-reg="next"', false);
        // Form tetap satu (single form) dengan langkah yang disembunyikan via JS.
        $response->assertSee('name="ticket_tier_id"', false);
        $response->assertSee('name="full_name"', false);
    }

    public function test_stage_page_shows_banner_above_wizard(): void
    {
        $response = $this->get(route('public.events.stage', [
            'slug' => $this->event->slug,
            'tier_id' => $this->tier->id,
        ]));

        $response->assertOk();
        $response->assertSee('event-banner', false);

        // Banner harus muncul sebelum form wizard dalam urutan DOM.
        $content = $response->getContent();
        $bannerPosition = strpos($content, 'event-banner');
        $formPosition = strpos($content, 'id="registrationWizard"');

        $this->assertNotFalse($bannerPosition);
        $this->assertNotFalse($formPosition);
        $this->assertLessThan($formPosition, $bannerPosition);
    }

    public function test_stage_page_shows_event_info_panel_on_the_right(): void
    {
        $response = $this->get(route('public.events.stage', [
            'slug' => $this->event->slug,
            'tier_id' => $this->tier->id,
        ]));

        $response->assertOk();
        $response->assertSee('Informasi Event');
        $response->assertSee('Tanggal');
        $response->assertSee('Lokasi');
        $response->assertSee('Tiket Kamu');
        // Panel info memakai kolom kanan.
        $response->assertSee('reg-event-info', false);
    }

    public function test_stage_page_lists_addons_with_switchers(): void
    {
        $addon = EventAddon::factory()->create([
            'event_id' => $this->event->id,
            'name' => 'Kaos Merch',
            'price' => '75000.00',
            'is_displayed' => true,
        ]);

        $response = $this->get(route('public.events.stage', [
            'slug' => $this->event->slug,
            'tier_id' => $this->tier->id,
        ]));

        $response->assertOk();
        $response->assertSee($addon->name);
        $response->assertSee('addons['.$addon->id.']', false);
        $response->assertSee('addon-qty', false);
        // Add-on ditampilkan sebagai switcher (maks. 1 unit), bukan input jumlah.
        $response->assertSee('form-switch', false);
        $response->assertSee('type="checkbox"', false);
        $response->assertDontSee('type="number" name="addons', false);
        // Grid kolom agar tampilan tidak boros tempat.
        $response->assertSee('row-cols-sm-2', false);
    }

    public function test_registration_still_works_from_wizard_form(): void
    {
        $response = $this->post(route('public.events.store', $this->event->slug), [
            'full_name' => 'Peserta Wizard',
            'email' => 'wizard@example.test',
            'ticket_tier_id' => $this->tier->id,
        ]);

        $response->assertRedirect(route('public.events.confirmed', [
            'slug' => $this->event->slug,
            'token' => Registrant::first()->participant_token,
        ]));

        $this->assertSame(1, $this->event->registrants()->count());
    }
}
