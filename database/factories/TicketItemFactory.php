<?php

namespace Database\Factories;

use App\Models\EventTicketTier;
use App\Models\Registrant;
use App\Models\TicketItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TicketItem>
 */
class TicketItemFactory extends Factory
{
    protected $model = TicketItem::class;

    public function definition(): array
    {
        return [
            'registrant_id' => Registrant::factory(),
            'ticket_tier_id' => EventTicketTier::factory(),
            'code' => 'TKT-'.strtoupper(uniqid()),
            'qr_code' => null,
            'barcode' => null,
            'is_used' => false,
            'barcode_type' => 'qrcode',
            'is_printed' => false,
        ];
    }
}
