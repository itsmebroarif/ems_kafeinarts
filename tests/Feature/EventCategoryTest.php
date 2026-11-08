<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class EventCategoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAsAdmin();
    }

    public function test_admin_can_create_category_inline(): void
    {
        $response = $this->from(route('admin.events.create'))->post(route('admin.categories.store'), [
            'category_name' => 'Webinar',
            'color' => '#123456',
        ]);

        $response
            ->assertRedirect(route('admin.events.create'))
            ->assertSessionHas('success')
            ->assertSessionHas('selected_category');

        $this->assertDatabaseHas('event_categories', [
            'name' => 'Webinar',
            'slug' => 'webinar',
            'color' => '#123456',
            'is_active' => true,
        ]);
    }

    public function test_duplicate_category_is_rejected(): void
    {
        EventCategory::create([
            'name' => 'Festival',
            'slug' => Str::slug('Festival'),
            'color' => '#d63384',
        ]);

        $response = $this->from(route('admin.events.create'))->post(route('admin.categories.store'), [
            'category_name' => 'Festival',
        ]);

        $response->assertRedirect(route('admin.events.create'));
        $response->assertSessionHasErrors('category_name');
        $this->assertSame(1, EventCategory::count());
    }

    public function test_invalid_color_is_rejected(): void
    {
        $response = $this->from(route('admin.events.create'))->post(route('admin.categories.store'), [
            'category_name' => 'Meetup',
            'color' => 'biru',
        ]);

        $response->assertSessionHasErrors('color');
        $this->assertDatabaseMissing('event_categories', ['name' => 'Meetup']);
    }

    public function test_public_event_list_filters_by_category(): void
    {
        $festival = EventCategory::create([
            'name' => 'Festival Musik',
            'slug' => 'festival-musik',
            'color' => '#d63384',
        ]);

        $seminar = EventCategory::create([
            'name' => 'Seminar Bisnis',
            'slug' => 'seminar-bisnis',
            'color' => '#0d6efd',
        ]);

        $festivalEvent = Event::factory()->create([
            'category_id' => $festival->id,
            'name' => 'Konser Akhir Tahun',
        ]);

        $seminarEvent = Event::factory()->create([
            'category_id' => $seminar->id,
            'name' => 'Seminar Investasi Pemula',
        ]);

        $response = $this->get(route('public.events.index', ['category' => 'festival-musik']));

        $response->assertOk();
        $response->assertSee($festivalEvent->name);
        $response->assertDontSee($seminarEvent->name);
    }

    public function test_public_event_list_shows_category_badges(): void
    {
        $category = EventCategory::create([
            'name' => 'Workshop',
            'slug' => 'workshop',
            'color' => '#fd7e14',
        ]);

        Event::factory()->create(['category_id' => $category->id]);

        $response = $this->get(route('public.events.index'));

        $response->assertOk();
        $response->assertSee('Workshop');
        // Badge kategori tampil dengan warna solid sesuai color kategori.
        $response->assertSee('background-color: rgb(253, 126, 20)', false);
    }

    public function test_admin_event_list_shows_category_badge(): void
    {
        $category = EventCategory::create([
            'name' => 'Kompetisi',
            'slug' => 'kompetisi',
            'color' => '#dc3545',
        ]);

        Event::factory()->create(['category_id' => $category->id]);

        $response = $this->get(route('admin.events.index'));

        $response->assertOk();
        $response->assertSee('Kompetisi');
    }
}
