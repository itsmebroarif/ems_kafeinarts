<?php

namespace Database\Factories;

use App\Models\EventTicketTier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EventTicketTier>
 */
class EventTicketTierFactory extends Factory
{
    protected $model = EventTicketTier::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'code' => 'TST-'.strtoupper(uniqid()),
            'slug' => strtolower(str_replace(' ', '-', fake()->words(2, true))),
            'price' => (string) round(random_int(50000, 500000) / 1000).'000',
            'fee' => '0.00',
            'description' => null,
            'is_downloadable' => true,
            'is_show_in_list' => true,
            'is_selectable_on_site' => true,
            'is_selectable_online' => true,
            'is_show_row_flag' => false,
            'row_flag_type' => null,
            'seat_qty' => 1,
            'stock_qty' => 0,
            'sold_qty' => 0,
            'is_metasuggest' => false,
            'qty' => 0,
        ];
    }

    public function withStock(): static
    {
        return $this->state(fn () => ['stock_qty' => random_int(10, 100), 'sold_qty' => 0]);
    }
}
