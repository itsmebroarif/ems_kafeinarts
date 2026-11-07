<?php

namespace Database\Seeders;

use App\Enums\EventBoardStatus;
use App\Enums\EventType;
use App\Models\Event;
use App\Models\EventAddon;
use App\Models\EventCategory;
use App\Models\EventTicketTier;
use App\Models\EventVoucher;
use App\Models\Registrant;
use App\Models\TicketItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DummyEventsSeeder extends Seeder
{
    private array $eventNames = [
        'Tech Conference Jakarta 2026',
        'Kafeinarts Music Fest 2026',
        'Webinar Digital Business Summit',
        'Hackathon Nasional 2026',
        'Design Sprint Week',
        'Startup Pitch Night',
        'Futsal Championship 2026',
        'Badminton Open 2026',
        'Basketball League 2026',
        'Konser Seni dan Budaya',
        'Art Exhibition Jakarta',
        'Workshop AI & Machine Learning',
        'Seminar Mindfulness',
        'Festival Kuliner Nusantara',
        'Conference on Innovation',
        'Esport Tournament 2026',
        'Renang Nasional Cup',
        'Lari Marathon Jakarta',
        'Panahan Indoor Championship',
        'Career Fair 2026',
        'Natura Science Fair',
        'Debat Universitas',
        'Kerja Bareng Community Meetup',
        'Concert EDM Night',
        'Workshop Fotografi',
        'Kompetisi Startup Utama',
        'Gathering Komunitas Tech',
        'Seminar Kesehatan',
        'Pameran Karya Siswa',
        'Konser Musik Indie',
    ];

    private array $categoriesMap = [
        'Tech Conference Jakarta 2026' => 'Seminar',
        'Kafeinarts Music Fest 2026' => 'Festival',
        'Webinar Digital Business Summit' => 'Webinar',
        'Hackathon Nasional 2026' => 'Hackathon',
        'Design Sprint Week' => 'Workshop',
        'Startup Pitch Night' => 'Pitch Competition',
        'Futsal Championship 2026' => 'Olahraga',
        'Badminton Open 2026' => 'Olahraga',
        'Basketball League 2026' => 'Olahraga',
        'Konser Seni dan Budaya' => 'Concert',
        'Art Exhibition Jakarta' => 'Art Exhibition',
        'Workshop AI & Machine Learning' => 'Workshop',
        'Seminar Mindfulness' => 'Seminar',
        'Festival Kuliner Nusantara' => 'Festival',
        'Conference on Innovation' => 'Seminar',
        'Esport Tournament 2026' => 'Esport',
        'Renang Nasional Cup' => 'Olahraga',
        'Lari Marathon Jakarta' => 'Olahraga',
        'Panahan Indoor Championship' => 'Olahraga',
        'Career Fair 2026' => 'Career Fair',
        'Natura Science Fair' => 'Science Fair',
        'Debat Universitas' => 'Debat',
        'Kerja Bareng Community Meetup' => 'Meetup',
        'Concert EDM Night' => 'Concert',
        'Workshop Fotografi' => 'Workshop',
        'Kompetisi Startup Utama' => 'Startup Meetup',
        'Gathering Komunitas Tech' => 'Networking',
        'Seminar Kesehatan' => 'Seminar',
        'Pameran Karya Siswa' => 'Pameran',
        'Konser Musik Indie' => 'Konser',
    ];

    private array $provinces = ['DKI Jakarta', 'Jawa Barat', 'Jawa Timur', 'Jawa Tengah', 'Bali', 'Sumatera Utara', 'Lampung', 'Banten'];
    private array $cities = [
        'DKI Jakarta' => ['Jakarta Selatan', 'Jakarta Timur', 'Jakarta Barat', 'Jakarta Utara', 'Jakarta Pusat'],
        'Jawa Barat' => ['Bandung', 'Kota Bekasi', 'Kota Bogor', 'Kota Depok', 'Kota Cimahi'],
        'Jawa Timur' => ['Surabaya', 'Kota Malang', 'Kota Kediri', 'Kota Pasuruan', 'Kota Mojokerto'],
        'Jawa Tengah' => ['Semarang', 'Kota Surakarta (Solo)', 'Kota Magelang', 'Kota Pekalongan'],
        'Bali' => ['Denpasar', 'Kabupaten Badung'],
        'Sumatera Utara' => ['Medan', 'Kota Binjai'],
        'Lampung' => ['Bandar Lampung', 'Metro'],
        'Banten' => ['Serang', 'Kota Tangerang', 'Kota Tangerang Selatan'],
    ];

    private array $boardStatuses = [
        EventBoardStatus::REGISTERED,
        EventBoardStatus::STARTING,
        EventBoardStatus::CLOSED,
        EventBoardStatus::RESULT,
    ];

    private array $addonNames = ['Kaos Eksklusif', 'Lanyard', 'Photo Card', 'Tote Bag', 'Sticker Pack', 'Buku Digital', 'Merch Hoodie'];
    private array $sellerNotes = ['Diskon 10% untuk 100 pembeli pertama', 'Harga spesial untuk member', 'Free merchandise', 'Early bird discount'];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing dummy data (keep seeder idempotent)
        $dummyEventNames = $this->eventNames;
        $dummyEventIds = Event::whereIn('name', $dummyEventNames)->pluck('id');

        if ($dummyEventIds->isNotEmpty()) {
            Registrant::whereIn('event_id', $dummyEventIds)->delete();
            TicketItem::whereIn('registrant_id', function ($query) use ($dummyEventIds) {
                $query->select('id')->from('registrants')->whereIn('event_id', $dummyEventIds);
            })->delete();
            EventTicketTier::whereIn('event_id', $dummyEventIds)->delete();
            EventAddon::whereIn('event_id', $dummyEventIds)->delete();
            EventVoucher::whereIn('event_id', $dummyEventIds)->delete();
            Event::whereIn('id', $dummyEventIds)->delete();
        }

        $this->command->info('Creating 30 dummy events with participants...');

        foreach ($this->eventNames as $index => $eventName) {
            $this->createDummyEvent($eventName, $index);
        }

        $this->command->info('✅ 30 dummy events created successfully with participants!');
    }

    private function createDummyEvent(string $name, int $index): void
    {
        $categoryName = $this->categoriesMap[$name] ?? 'Lainnya';
        $category = EventCategory::firstOrCreate(
            ['slug' => Str::slug($categoryName)],
            ['name' => $categoryName, 'color' => '#6c757d', 'is_active' => true]
        );

        $provinsi = $this->provinces[array_rand($this->provinces)];
        $cities = $this->cities[$provinsi] ?? ['Jakarta Selatan'];
        $city = $cities[array_rand($cities)];

        // Mix board statuses for variety
        $boardStatus = $this->boardStatuses[$index % count($this->boardStatuses)];

        // Some events started, some future
        if ($index < 10) {
            $startsAt = now()->addDays(random_int(1, 30))->setTime(9, random_int(0, 59));
            $endsAt = (clone $startsAt)->addHours(random_int(2, 8));
        } elseif ($index < 20) {
            $startsAt = now()->subDays(random_int(1, 30))->setTime(9, random_int(0, 59));
            $endsAt = (clone $startsAt)->addHours(random_int(2, 8));
        } else {
            $startsAt = now()->subDays(random_int(31, 60))->setTime(9, random_int(0, 59));
            $endsAt = (clone $startsAt)->addHours(random_int(2, 12));
        }

        $isSport = $category->is_sport ?? false;
        $eventType = $isSport ? EventType::KOMPETISI : (random_int(0, 1) ? EventType::KOMPETISI : EventType::HIBURAN);

        $event = Event::create([
            'name' => $name,
            'slug' => Str::slug($name . ' ' . now()->timestamp),
            'category_id' => $category->id,
            'event_type' => $eventType,
            'description' => $this->generateDescription($name, $eventType),
            'location' => $city,
            'provinsi' => $provinsi,
            'city' => $city,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'venue_details' => $this->generateVenueDetails($provinsi, $city),
            'is_published' => true,
            'max_registrants' => random_int(50, 500),
            'price' => '0.00',
            'fee' => '0.00',
            'submit_fee' => '0.00',
            'status' => 'complete',
            'board_status' => $boardStatus,
        ]);

        // Create ticket tiers
        $tiers = $this->createTicketTiers($event);

        // Create addons (for some events)
        if ($index % 3 === 0) {
            $this->createAddons($event);
        }

        // Create vouchers (for some events)
        if ($index % 4 === 0) {
            $this->createVouchers($event);
        }

        // Create participants
        $this->createParticipants($event, $tiers);

        $this->command->info("  ✓ {$name} ({$boardStatus}) — {$provinsi}, {$city}");
    }

    private function createTicketTiers(Event $event): array
    {
        $tierNames = ['Regular', 'VIP', 'Early Bird', 'Student', 'Premium'];
        $usedNames = [];
        $tiers = [];

        // Create 1-4 tiers per event
        $numTiers = random_int(1, 4);
        shuffle($tierNames);
        $selectedNames = array_slice($tierNames, 0, $numTiers);

        foreach ($selectedNames as $tierName) {
            $price = match ($tierName) {
                'Early Bird' => (string) random_int(50, 100) * 1000,
                'Student' => (string) random_int(25, 75) * 1000,
                'Regular' => (string) random_int(75, 150) * 1000,
                'VIP' => (string) random_int(200, 500) * 1000,
                'Premium' => (string) random_int(150, 350) * 1000,
                default => '100000.00',
            };

            $tier = EventTicketTier::create([
                'event_id' => $event->id,
                'name' => $tierName . ' Pass',
                'code' => strtoupper(substr($tierName, 0, 3)) . '-' . strtoupper(Str::random(6)),
                'slug' => Str::slug($tierName . '-' . uniqid()),
                'price' => $price,
                'fee' => '5000.00',
                'description' => "Tier {$tierName} untuk event {$event->name}",
                'is_downloadable' => true,
                'is_show_in_list' => true,
                'is_selectable_on_site' => true,
                'is_selectable_online' => true,
                'is_metasuggest' => random_int(0, 1),
                'seat_qty' => 1,
                'stock_qty' => random_int(20, 150),
                'sold_qty' => random_int(0, 150),
            ]);

            $tiers[] = $tier;
        }

        return $tiers;
    }

    private function createAddons(Event $event): void
    {
        $addons = array_slice($this->addonNames, 0, random_int(1, 4));

        foreach ($addons as $addonName) {
            EventAddon::create([
                'event_id' => $event->id,
                'name' => $addonName,
                'code' => strtoupper(substr($addonName, 0, 3)) . '-' . strtoupper(Str::random(6)),
                'slug' => Str::slug($addonName . '-' . uniqid()),
                'image' => null,
                'price' => (string) random_int(25, 150) * 1000,
                'fee' => '0.00',
                'description' => "Add-on {$addonName} untuk event {$event->name}",
                'qty' => random_int(20, 100),
                'max_qty' => random_int(1, 5),
                'sold_qty' => random_int(0, 50),
                'buy_qty_step' => 1,
                'show_qty' => 10,
                'is_new' => random_int(0, 1),
                'is_show_in_list' => true,
                'is_selectable_on_site' => true,
                'is_selectable_online' => true,
                'is_metasuggest' => random_int(0, 1),
                'is_fit_disable' => false,
                'order_by' => 0,
            ]);
        }
    }

    private function createVouchers(Event $event): void
    {
        $voucherCodes = ['EARLY50', 'MEMBER30', 'NEWBIE20', 'HEMAT100', 'FESTIVE25'];
        $code = $voucherCodes[array_rand($voucherCodes)] . '-' . random_int(100, 999);

        EventVoucher::create([
            'event_id' => $event->id,
            'code' => strtoupper($code),
            'is_percent' => random_int(0, 1),
            'value' => (string) random_int(5, 50),
            'is_single_use' => false,
            'is_active' => true,
            'total_uses' => random_int(10, 100),
            'uses' => random_int(0, 10),
            'expired_at' => now()->addDays(random_int(30, 90))->toDateString(),
            'order_by' => 0,
        ]);
    }

    private function createParticipants(Event $event, array $tiers): void
    {
        // Random 10-50 participants per event
        $numParticipants = random_int(10, 50);

        for ($i = 0; $i < $numParticipants; $i++) {
            $tier = $tiers[array_rand($tiers)];

            // Mix of paid/unpaid
            $isPaid = random_int(0, 3) < 2; // ~67% paid
            $status = $isPaid ? (random_int(0, 1) ? 'confirmed' : 'paid') : 'pending';
            $isConfirmed = $isPaid && random_int(0, 3) > 0;
            $isPending = !$isPaid;

            $registrant = Registrant::factory()->create([
                'event_id' => $event->id,
                'ticket_tier_id' => $tier->id,
                'full_name' => fake()->name(),
                'email' => fake()->unique()->safeEmail(),
                'phone' => fake()->phoneNumber(),
                'city' => fake()->city(),
                'district' => fake()->citySuffix(),
                'address' => fake()->address(),
                'is_paid' => $isPaid,
                'is_confirmed' => $isConfirmed,
                'is_pending' => $isPending,
                'status' => $status,
                'subtotal' => (string) $tier->price,
                'addons_total' => '0.00',
                'discount' => '0.00',
                'total' => (string) $tier->price,
                'fee' => '5000.00',
                'session_key' => 'dummy_' . uniqid(),
                'participant_token' => 'PART-' . strtoupper(Str::random(13)),
                'paid_at' => $isPaid ? now()->subDays(random_int(0, 30)) : null,
            ]);

            // Create ticket item for paid registrants
            if ($isPaid) {
                TicketItem::factory()->create([
                    'registrant_id' => $registrant->id,
                    'ticket_tier_id' => $tier->id,
                    'code' => 'TKT-' . strtoupper(Str::random(8)),
                    'qr_code' => 'data:image/png;base64,' . base64_encode('dummy-qr-' . $registrant->id),
                    'barcode_type' => 'qrcode',
                    'is_used' => random_int(0, 10) > 8, // ~20% used
                ]);
            }
        }
    }

    private function generateDescription(string $name, string $eventType): string
    {
        $type = $eventType === EventType::KOMPETISI ? 'kompetisi' : 'event hiburan';
        return "Selamat datang di {$name}! {$type} ini akan menghadirkan pengalaman terbaik untuk semua peserta. Bergabunglah dengan ratusan peserta dari seluruh Indonesia dan rasakan keseruan dalam acara ini.\n\n" .
               "Highlights:\n" .
               "- Persiapan terbaik untuk peserta\n" .
               "- Networking dengan ahli di bidangnya\n" .
               "- Hadiah menarik untuk pemenang\n" .
               "- Sertifikat kehadiran untuk semua peserta\n\n" .
               "Jangan lewatkan kesempatan ini! Dapatkan early bird discount sekarang.";
    }

    private function generateVenueDetails(string $provinsi, string $city): string
    {
        return "Venue: {$city}, {$provinsi}\n" .
               "Parkir: Tersedia parkir gratis di lantai B1-B3\n" .
               "Transportasi: Silakan gunakan TransJakarta, KRL, atau angkutan umum terdekat\n" .
               "Fasilitas: AC, Kantin, Toilet, Musholla, Wi-Fi\n" .
               "Contact: 0812-XXXX-XXXX";
    }
}