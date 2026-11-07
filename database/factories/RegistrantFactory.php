<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\EventTicketTier;
use App\Models\Registrant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Registrant>
 */
class RegistrantFactory extends Factory
{
    protected $model = Registrant::class;

    public function definition(): array
    {
        return [
            'full_name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'nationality' => 'Indonesia',
            'city' => fake()->city(),
            'district' => fake()->citySuffix(),
            'address' => fake()->address(),
            'postal_code' => fake()->postcode(),
            'guests_quantity' => '1',
            'registrant_type' => 'individual',
            'guests_quantity_other' => null,
            'event_id' => Event::factory(),
            'ticket_tier_id' => EventTicketTier::factory(),
            'voucher_id' => null,
            'is_paid' => false,
            'is_confirmed' => false,
            'is_rejected' => false,
            'is_pending' => true,
            'is_deleted' => false,
            'is_referred' => false,
            'is_hide' => false,
            'is_approved' => false,
            'notes' => null,
            'custom_fields' => null,
            'subtotal' => '0.00',
            'addons_total' => '0.00',
            'discount' => '0.00',
            'total' => '0.00',
            'fee' => '0.00',
            'status' => 'pending',
            'shipping_method' => null,
            'paid_at' => null,
            'paid_by' => null,
            'exchange_id' => null,
            'ip_address' => null,
            'device_info' => null,
            'device_type' => 'desktop',
            'os_version' => null,
            'browser' => 'Chrome',
            'browser_version' => '120.0',
            'locale' => 'id',
            'is_wallet_payment' => false,
            'employee_id' => null,
            'is_remove' => false,
            'to_payment_url' => null,
            'note' => null,
            'deleted_at' => null,
        ];
    }

    public function paid(): static
    {
        return $this->state(fn () => [
            'is_paid' => true,
            'is_confirmed' => true,
            'is_rejected' => false,
            'is_pending' => false,
            'status' => 'paid',
            'paid_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn () => [
            'is_paid' => false,
            'is_confirmed' => false,
            'is_rejected' => true,
            'is_pending' => false,
            'status' => 'rejected',
        ]);
    }

    public function confirmed(): static
    {
        return $this->state(fn () => [
            'is_paid' => true,
            'is_confirmed' => true,
            'is_rejected' => false,
            'is_pending' => false,
            'status' => 'confirmed',
        ]);
    }
}
