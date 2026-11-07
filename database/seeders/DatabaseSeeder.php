<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventAddon;
use App\Models\EventAddonOption;
use App\Models\EventTicketTier;
use App\Models\EventVoucher;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $event = Event::firstOrCreate(
            ['slug' => 'kafeinarts-festival-2026'],
            [
                'name' => 'Kafeinarts Festival 2026',
                'description' => 'Festival musik dan cosplay tahunan Kafeinarts.',
                'location' => 'Jakarta, Indonesia',
                'starts_at' => now()->addDays(14)->endOfDay(),
                'ends_at' => now()->addDays(16)->endOfDay(),
                'venue_details' => 'Indonesian Expo, Jakarta',
                'is_published' => true,
                'max_registrants' => 500,
            ]
        );
        if (empty($event->slug)) {
            $event->slug = 'kafeinarts-festival-2026-'.rand(1000, 9999);
            $event->save();
        }

        $tiers = [
            ['Early Bird', 'EB-2026-'.rand(100, 999), 'early-bird-'.rand(1000, 9999), '75000.00', 100, 0],
            ['Regular', 'REG-2026-'.rand(100, 999), 'regular-'.rand(1000, 9999), '125000.00', 200, 0],
            ['VIP', 'VIP-2026-'.rand(100, 999), 'vip-'.rand(1000, 9999), '250000.00', 20, 0],
        ];

        foreach ($tiers as $t) {
            EventTicketTier::create([
                'name' => $t[0],
                'code' => $t[1],
                'slug' => $t[2],
                'price' => $t[3],
                'fee' => '0.00',
                'is_show_in_list' => true,
                'is_selectable_on_site' => true,
                'is_selectable_online' => true,
                'stock_qty' => (int) $t[4],
                'sold_qty' => (int) $t[5],
                'event_id' => $event->id,
            ]);
        }

        $addons = [
            ['T-Shirt XL', 'TSHIRT-XL-'.rand(100, 999), 't-shirt-xl-'.rand(1000, 9999), '75000.00', 50, 0],
            ['Tour Guide', 'TOUR-GUIDE-'.rand(100, 999), 'tour-guide-'.rand(1000, 9999), '45000.00', 20, 0],
            ['Backpack', 'BACKPACK-'.rand(100, 999), 'backpack-'.rand(1000, 9999), '65000.00', 30, 0],
            ['Lanyard', 'LANYARD-'.rand(100, 999), 'lanyard-'.rand(1000, 9999), '15000.00', 100, 0],
            ['Stencil Kit', 'STENCIL-KIT-'.rand(100, 999), 'stencil-kit-'.rand(1000, 9999), '25000.00', 40, 0],
            ['X-Large Tee', 'TEE-XL-'.rand(100, 999), 'tee-xl-'.rand(1000, 9999), '85000.00', 25, 0],
        ];

        foreach ($addons as $a) {
            $addon = EventAddon::create([
                'name' => $a[0],
                'code' => $a[1],
                'slug' => $a[2],
                'price' => $a[3],
                'fee' => '0.00',
                'is_new' => false,
                'is_show_in_list' => true,
                'is_allow_buying_stack' => false,
                'qty' => (int) $a[4],
                'sold_qty' => (int) $a[5],
                'is_selectable_on_site' => true,
                'is_selectable_online' => true,
                'is_displayed' => true,
                'is_displayed_on_backend' => true,
                'display_type' => 'count',
                'event_id' => $event->id,
            ]);

            for ($i = 0; $i < 3; $i++) {
                EventAddonOption::create([
                    'name' => 'Option '.($i + 1).' for '.$addon->name,
                    'code' => strtoupper(uniqid()),
                    'slug' => $addon->slug."-opt-{$i}",
                    'price' => '0.00',
                    'fee' => '0.00',
                    'is_free' => true,
                    'is_highlight' => false,
                    'order_by' => $i,
                    'addon_id' => $addon->id,
                ]);
            }
        }

        EventVoucher::create([
            'event_id' => $event->id,
            'code' => 'KAFEINARTS10-'.rand(100, 999),
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
            'order_by' => 0,
        ]);

        EventVoucher::create([
            'event_id' => $event->id,
            'code' => 'KAFEINARTS50-'.rand(100, 999),
            'is_percent' => false,
            'value' => '50000.00',
            'is_single_use' => true,
            'is_active' => true,
            'total_uses' => 50,
            'uses' => 0,
            'is_auto_apply' => false,
            'is_show_in_list' => true,
            'is_show_on_front' => true,
            'is_show_on_payment' => true,
            'order_by' => 1,
        ]);

        $this->call(EventCategorySeeder::class);
    }
}
