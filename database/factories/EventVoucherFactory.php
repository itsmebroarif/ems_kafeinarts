<?php

namespace Database\Factories;

use App\Models\EventVoucher;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EventVoucher>
 */
class EventVoucherFactory extends Factory
{
    protected $model = EventVoucher::class;

    public function definition(): array
    {
        return [
            'code' => 'VOCT-'.strtoupper(uniqid()),
            'is_percent' => true,
            'value' => '10.00',
            'is_single_use' => false,
            'is_active' => true,
            'total_uses' => 100,
            'uses' => 0,
            'is_auto_apply' => true,
            'is_show_in_list' => true,
            'is_show_on_front' => true,
            'is_show_on_payment' => true,
            'expired_at' => null,
            'order_by' => 0,
        ];
    }

    public function percent(): static
    {
        return $this->state(fn () => ['is_percent' => true, 'value' => '10.00']);
    }

    public function fixed(): static
    {
        return $this->state(fn () => ['is_percent' => false, 'value' => '50000.00']);
    }

    public function used(): static
    {
        return $this->state(fn () => ['is_active' => true, 'uses' => random_int(1, 50)]);
    }
}
