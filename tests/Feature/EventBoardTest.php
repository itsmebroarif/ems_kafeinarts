<?php

namespace Tests\Feature;

use App\Enums\EventBoardStatus;
use App\Models\Event;
use App\Models\EventCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventBoardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAsAdmin();
    }

    public function test_board_page_renders_all_columns(): void
    {
        Event::factory()->create(['name' => 'Konser Uji Coba']);

        $response = $this->get(route('admin.events.board'));

        $response->assertOk();
        $response->assertSee('Event Terdaftar');
        $response->assertSee('Event Akan Dimulai');
        $response->assertSee('Event Ditutup');
        $response->assertSee('Result');
        $response->assertSee('Konser Uji Coba');
    }

    public function test_event_can_be_moved_between_columns(): void
    {
        $event = Event::factory()->create(['board_status' => EventBoardStatus::REGISTERED]);

        $response = $this->postJson(route('admin.events.board.move', $event), [
            'board_status' => EventBoardStatus::STARTING,
        ]);

        $response->assertOk()->assertJsonPath('board_status', EventBoardStatus::STARTING);
        $this->assertSame(EventBoardStatus::STARTING, $event->fresh()->board_status);
    }

    public function test_invalid_board_status_is_rejected(): void
    {
        $event = Event::factory()->create();

        $response = $this->postJson(route('admin.events.board.move', $event), [
            'board_status' => 'tidak-ada',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors('board_status');
        $this->assertSame(EventBoardStatus::REGISTERED, $event->fresh()->board_status);
    }

    public function test_non_sport_event_cannot_move_to_result_column(): void
    {
        $category = EventCategory::create([
            'name' => 'Konser Musik',
            'slug' => 'konser-musik',
            'color' => '#6f42c1',
            'is_sport' => false,
        ]);

        $event = Event::factory()->create(['category_id' => $category->id]);

        $response = $this->postJson(route('admin.events.board.move', $event), [
            'board_status' => EventBoardStatus::RESULT,
        ]);

        $response->assertStatus(422);
        $this->assertSame(EventBoardStatus::REGISTERED, $event->fresh()->board_status);
    }

    public function test_sport_event_can_move_to_result_column(): void
    {
        $category = EventCategory::create([
            'name' => 'Esport',
            'slug' => 'esport',
            'color' => '#0dcaf0',
            'is_sport' => true,
        ]);

        $event = Event::factory()->create(['category_id' => $category->id]);

        $response = $this->postJson(route('admin.events.board.move', $event), [
            'board_status' => EventBoardStatus::RESULT,
        ]);

        $response->assertOk()->assertJsonPath('board_status', EventBoardStatus::RESULT);
        $this->assertSame(EventBoardStatus::RESULT, $event->fresh()->board_status);
    }

    public function test_new_event_starts_in_registered_column(): void
    {
        $event = Event::factory()->create();

        $this->assertSame(EventBoardStatus::REGISTERED, $event->board_status);
    }
}
