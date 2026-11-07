<?php

namespace Database\Factories;

use App\Models\EventAddon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EventAddon>
 */
class EventAddonFactory extends Factory
{
    protected $model = EventAddon::class;

    public function definition(): array
    {
        return [
            'name' => fake()->word(2).' Add-on',
            'code' => 'ADD-'.strtoupper(uniqid()),
            'slug' => strtolower(str_replace(' ', '-', fake()->word(2))),
            'image' => null,
            'price' => (string) round(random_int(5000, 100000) / 1000).'000',
            'fee' => '0.00',
            'description' => null,
            'is_new' => false,
            'is_show_in_list' => true,
            'is_allow_buying_stack' => false,
            'qty' => 0,
            'max_qty' => 0,
            'sold_qty' => 0,
            'is_selectable_on_site' => true,
            'is_selectable_online' => true,
            'is_metasuggest' => false,
            'buy_qty_step' => 0,
            'show_qty' => 0,
            'is_displayed' => true,
            'is_displayed_on_backend' => true,
            'display_type' => 'count',
            'is_fit_disable' => false,
            'order_by' => 0,
        ];
    }

    public function withStock(): static
    {
        return $this->state(fn () => ['qty' => random_int(10, 200), 'sold_qty' => 0]);
    }
}
