<?php

namespace Database\Factories;

use App\Enums\EventBoardStatus;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        return [
            'id' => null,
            'name' => fake()->word(3).' Festival',
            'slug' => fake()->unique()->slug(),
            'description' => fake()->paragraphs(3, true),
            'location' => fake()->city(),
            'starts_at' => now()->addDays(random_int(7, 90))->endOfDay(),
            'ends_at' => now()->addDays(random_int(7, 120))->endOfDay(),
            'venue_details' => fake()->paragraph(2),
            'is_published' => true,
            'max_registrants' => 0,
            'timezone' => 0,
            'registrant_hours' => 0,
            'delete_at' => 0,
            'dudent_at' => 0,
            'price' => '0.00',
            'fee' => '0.00',
            'submit_fee' => '0.00',
            'status' => 'complete',
            'board_status' => EventBoardStatus::REGISTERED,
        ];
    }

    public function unpublished(): static
    {
        return $this->state(fn () => ['is_published' => false, 'status' => 'draft']);
    }
}
