<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventAddon;
use App\Models\EventTicketTier;
use App\Models\Registrant;
use App\Models\TicketItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventRegistrationFlowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array{0: Event, 1: EventTicketTier}
     */
    private function createEventWithTier(array $tierAttributes = []): array
    {
        $event = Event::factory()->create();

        $tier = EventTicketTier::factory()->create(array_merge([
            'event_id' => $event->id,
            'price' => '150000.00',
            'fee' => '5000.00',
            'stock_qty' => 50,
            'sold_qty' => 0,
        ], $tierAttributes));

        return [$event, $tier];
    }

    public function test_registration_persists_token_and_creates_ticket(): void
    {
        [$event, $tier] = $this->createEventWithTier();

        $response = $this->post(route('public.events.store', $event->slug), [
            'full_name' => 'Budi Santoso',
            'email' => 'budi@example.test',
            'nik' => '3171234567890001',
            'gender' => 'Laki-laki',
            'phone' => '081234567890',
            'ticket_tier_id' => $tier->id,
        ]);

        $response->assertRedirect();
        $token = $response->getSession()->get('participant_token');
        $this->assertNotNull($token);

        $registrant = Registrant::where('participant_token', $token)->firstOrFail();

        $this->assertSame($event->id, $registrant->event_id);
        $this->assertSame($tier->id, $registrant->ticket_tier_id);
        $this->assertSame('pending', $registrant->status);
        $this->assertFalse($registrant->is_paid);
        $this->assertSame('150000.00', (string) $registrant->subtotal);
        $this->assertSame('5000.00', (string) $registrant->fee);
        $this->assertSame('150000.00', (string) $registrant->total);

        $ticket = TicketItem::where('registrant_id', $registrant->id)->firstOrFail();
        $this->assertSame($tier->id, $ticket->ticket_tier_id);
        $this->assertNotEmpty($ticket->qr_code);
    }

    public function test_registration_with_addons_reserves_stock_and_persists_addons(): void
    {
        [$event, $tier] = $this->createEventWithTier();

        $addon = EventAddon::factory()->create([
            'event_id' => $event->id,
            'price' => '25000.00',
            'fee' => '1000.00',
            'qty' => 10,
            'sold_qty' => 0,
            'is_displayed' => true,
            'is_selectable_online' => true,
        ]);

        $response = $this->post(route('public.events.store', $event->slug), [
            'full_name' => 'Siti Aminah',
            'email' => 'siti@example.test',
            'nik' => '3171234567890001',
            'gender' => 'Laki-laki',
            'ticket_tier_id' => $tier->id,
            'addons' => [$addon->id => 1],
        ]);

        $response->assertRedirect();

        $token = $response->getSession()->get('participant_token');
        $registrant = Registrant::where('participant_token', $token)->firstOrFail();

        $this->assertSame('25000.00', (string) $registrant->addons_total);
        $this->assertSame('175000.00', (string) $registrant->total);

        $addon->refresh();
        $this->assertSame(1, $addon->sold_qty);

        $this->assertDatabaseHas('registrant_addons', [
            'registrant_id' => $registrant->id,
            'addon_id' => $addon->id,
            'qty' => 1,
        ]);
    }

    public function test_registration_rejects_addon_quantity_above_one(): void
    {
        [$event, $tier] = $this->createEventWithTier();

        $addon = EventAddon::factory()->create([
            'event_id' => $event->id,
            'price' => '25000.00',
            'qty' => 10,
            'sold_qty' => 0,
            'is_displayed' => true,
            'is_selectable_online' => true,
        ]);

        $response = $this->from(route('public.events.stage', $event->slug))
            ->post(route('public.events.store', $event->slug), [
                'full_name' => 'Dodi',
                'email' => 'dodi@example.test',
                'nik' => '3171234567890001',
                'gender' => 'Laki-laki',
                'gender' => 'Laki-laki',
                'ticket_tier_id' => $tier->id,
                'addons' => [$addon->id => 2],
            ]);

        // Switcher hanya mengizinkan maksimal 1 unit per add-on.
        $response->assertSessionHasErrors('addons.'.$addon->id);
        $this->assertSame(0, Registrant::count());
        $this->assertSame(0, $addon->fresh()->sold_qty);
    }

    public function test_registration_fails_when_addon_stock_insufficient(): void
    {
        [$event, $tier] = $this->createEventWithTier();

        $addon = EventAddon::factory()->create([
            'event_id' => $event->id,
            'price' => '25000.00',
            'qty' => 1,
            'sold_qty' => 1,
            'is_displayed' => true,
            'is_selectable_online' => true,
        ]);

        $response = $this->from(route('public.events.stage', $event->slug))
            ->post(route('public.events.store', $event->slug), [
                'full_name' => 'Rina',
                'email' => 'rina@example.test',
                'nik' => '3171234567890001',
                'gender' => 'Laki-laki',
                'gender' => 'Laki-laki',
                'ticket_tier_id' => $tier->id,
                'addons' => [$addon->id => 1],
            ]);

        $response->assertRedirect(route('public.events.stage', $event->slug));
        $response->assertSessionHasErrors('registration');
        $this->assertSame(0, Registrant::count());
        $this->assertSame(1, $addon->fresh()->sold_qty);
    }

    public function test_registration_requires_valid_tier(): void
    {
        [$event, $tier] = $this->createEventWithTier();

        $response = $this->from(route('public.events.stage', $event->slug))
            ->post(route('public.events.store', $event->slug), [
                'full_name' => 'Andi',
                'email' => 'andi@example.test',
                'nik' => '3171234567890001',
                'gender' => 'Laki-laki',
                'gender' => 'Laki-laki',
                'ticket_tier_id' => '00000000-0000-0000-0000-000000000000',
            ]);

        $response->assertSessionHasErrors('ticket_tier_id');
        $this->assertSame(0, Registrant::count());
    }
}
