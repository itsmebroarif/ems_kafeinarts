<?php

namespace Database\Factories;

use App\Models\EventAddonOption;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EventAddonOption>
 */
class EventAddonOptionFactory extends Factory
{
    protected $model = EventAddonOption::class;

    public function definition(): array
    {
        return [
            'addon_id' => 1,
            'name' => fake()->word(2),
            'code' => 'OPT-'.strtoupper(uniqid()),
            'slug' => strtolower(str_replace(' ', '-', fake()->word(2))),
            'price' => '0.00',
            'fee' => '0.00',
            'is_free' => true,
            'is_highlight' => false,
            'order_by' => 0,
        ];
    }
}
