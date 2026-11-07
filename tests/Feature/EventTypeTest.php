<?php

namespace Tests\Feature;

use App\Enums\EventType;
use App\Models\Event;
use App\Models\EventCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventTypeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAsAdmin();
    }

    public function test_event_form_shows_competition_and_entertainment_options(): void
    {
        $response = $this->get(route('admin.events.create'));

        $response->assertOk();
        $response->assertSee('Kompetisi');
        $response->assertSee('Event Hiburan');
        $response->assertSee('name="event_type"', false);
    }

    public function test_admin_can_create_event_as_competition(): void
    {
        $response = $this->post(route('admin.events.store'), $this->validPayload([
            'event_type' => EventType::KOMPETISI,
        ]));

        $response->assertRedirect(route('admin.events.index'));

        $event = Event::where('slug', 'kompetisi-baru')->firstOrFail();

        $this->assertSame(EventType::KOMPETISI, $event->event_type);
        $this->assertTrue($event->isCompetition());
        $this->assertSame('Kompetisi', $event->eventTypeLabel());
    }

    public function test_admin_can_create_event_as_entertainment(): void
    {
        $response = $this->post(route('admin.events.store'), $this->validPayload([
            'event_type' => EventType::HIBURAN,
            'slug' => 'hiburan-baru',
        ]));

        $response->assertRedirect(route('admin.events.index'));

        $event = Event::where('slug', 'hiburan-baru')->firstOrFail();

        $this->assertSame(EventType::HIBURAN, $event->event_type);
        $this->assertFalse($event->isCompetition());
        $this->assertSame('Event Hiburan', $event->eventTypeLabel());
    }

    public function test_invalid_event_type_is_rejected(): void
    {
        $response = $this->from(route('admin.events.create'))
            ->post(route('admin.events.store'), $this->validPayload([
                'event_type' => 'festival',
            ]));

        $response->assertRedirect(route('admin.events.create'));
        $response->assertSessionHasErrors('event_type');
        $this->assertSame(0, Event::count());
    }

    public function test_event_type_badge_is_shown_on_public_listing(): void
    {
        $event = Event::factory()->create([
            'event_type' => EventType::HIBURAN,
            'name' => 'Festival Ceria',
        ]);

        $response = $this->get(route('public.events.index'));

        $response->assertOk();
        $response->assertSee('Event Hiburan');
        $response->assertSee($event->name);
    }

    public function test_event_type_can_be_updated(): void
    {
        $event = Event::factory()->create(['event_type' => EventType::KOMPETISI]);

        $response = $this->put(route('admin.events.update', $event), $this->validPayload([
            'event_type' => EventType::HIBURAN,
            'slug' => $event->slug,
            'category_id' => EventCategory::create([
                'name' => 'Konser',
                'slug' => 'konser',
                'color' => '#6f42c1',
            ])->id,
        ]));

        $response->assertRedirect(route('admin.events.index'));
        $this->assertSame(EventType::HIBURAN, $event->fresh()->event_type);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Event Uji Coba',
            'slug' => 'kompetisi-baru',
            'price' => 100000,
            'event_type' => EventType::KOMPETISI,
            'provinsi' => 'DKI Jakarta',
            'city' => 'Jakarta Selatan',
        ], $overrides);
    }
}
