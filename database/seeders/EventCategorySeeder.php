<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EventCategorySeeder extends Seeder
{
    /**
     * Kategori bawaan untuk event.
     *
     * @var array<int, array{name: string, color: string, is_sport?: bool}>
     */
    private array $categories = [
        // Kompetisi / Sport
        ['name' => 'Festival', 'color' => '#d63384'],
        ['name' => 'Konser', 'color' => '#6f42c1'],
        ['name' => 'Seminar', 'color' => '#0d6efd'],
        ['name' => 'Workshop', 'color' => '#fd7e14'],
        ['name' => 'Tech Talk', 'color' => '#198754'],
        ['name' => 'Webinar', 'color' => '#20c997'],
        ['name' => 'Hackathon', 'color' => '#dc3545'],
        ['name' => 'Startup Meetup', 'color' => '#fd7e14'],
        ['name' => 'Career Fair', 'color' => '#0dcaf0'],
        ['name' => 'Networking', 'color' => '#6c757d'],

        // Sport / Esport (bisa masuk kolom Result)
        ['name' => 'Olahraga', 'color' => '#198754', 'is_sport' => true],
        ['name' => 'Esport', 'color' => '#0dcaf0', 'is_sport' => true],
        ['name' => 'Futsal', 'color' => '#20c997', 'is_sport' => true],
        ['name' => 'Badminton', 'color' => '#dc3545', 'is_sport' => true],
        ['name' => 'Basketball', 'color' => '#fd7e14', 'is_sport' => true],
        ['name' => 'Tari', 'color' => '#d63384', 'is_sport' => false],
        ['name' => 'Panahan', 'color' => '#6f42c1', 'is_sport' => true],
        ['name' => 'Renang', 'color' => '#0d6efd', 'is_sport' => true],
        ['name' => 'Lari', 'color' => '#6c757d', 'is_sport' => true],

        // Kompetisi Umum
        ['name' => 'Kompetisi', 'color' => '#dc3545'],
        ['name' => 'Debat', 'color' => '#6f42c1'],
        ['name' => 'Science Fair', 'color' => '#0d6efd'],
        ['name' => 'Design Sprint', 'color' => '#fd7e14'],
        ['name' => 'Pitch Competition', 'color' => '#198754'],
        ['name' => 'Hackathon Web', 'color' => '#dc3545'],

        // Hiburan / Gathering
        ['name' => 'Pameran', 'color' => '#20c997'],
        ['name' => 'Concert', 'color' => '#6f42c1'],
        ['name' => 'Art Exhibition', 'color' => '#fd7e14'],
        ['name' => 'Meetup', 'color' => '#0d6efd'],
        ['name' => 'Reuni', 'color' => '#198754'],
        ['name' => 'Perayaan', 'color' => '#d63384'],

        // Lain-lain
        ['name' => 'Lainnya', 'color' => '#6c757d'],
        ['name' => 'SOS', 'color' => '#fd7e14'],
        ['name' => 'Corporate', 'color' => '#0d6efd'],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [];

        foreach ($this->categories as $category) {
            $categories[$category['name']] = EventCategory::firstOrCreate(
                ['slug' => Str::slug($category['name'])],
                [
                    'name' => $category['name'],
                    'color' => $category['color'],
                    'is_active' => true,
                    'is_sport' => $category['is_sport'] ?? false,
                ]
            );
        }

        // Make sure existing sport categories are marked correctly
        EventCategory::where('name', 'Olahraga')->update(['is_sport' => true]);
        EventCategory::where('name', 'Esport')->update(['is_sport' => true]);
        EventCategory::where('name', 'Futsal')->update(['is_sport' => true]);
        EventCategory::where('name', 'Badminton')->update(['is_sport' => true]);
        EventCategory::where('name', 'Basketball')->update(['is_sport' => true]);
        EventCategory::where('name', 'Panahan')->update(['is_sport' => true]);
        EventCategory::where('name', 'Renang')->update(['is_sport' => true]);
        EventCategory::where('name', 'Lari')->update(['is_sport' => true]);

        Event::query()
            ->whereNull('category_id')
            ->where('name', 'like', '%festival%')
            ->update(['category_id' => $categories['Festival']->id ?? null]);

        Event::query()
            ->whereNull('category_id')
            ->update(['category_id' => $categories['Lainnya']->id ?? null]);
    }
}
