<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventTicketTier;
use App\Models\Registrant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ParticipantViewTest extends TestCase
{
    use RefreshDatabase;

    private function createEvent(array $attributes = []): Event
    {
        return Event::factory()->create(array_merge([
            'slug' => 'event-satu',
            'name' => 'Event Satu',
        ], $attributes));
    }

    /**
     * @return array<int, string>
     */
    private function viewNames(): array
    {
        return collect(DB::select("SELECT name FROM sqlite_master WHERE type = 'view'"))
            ->pluck('name')
            ->all();
    }

    public function test_creating_event_creates_participant_view(): void
    {
        $this->createEvent();

        $this->assertContains('peserta_event_satu', $this->viewNames());
    }

    public function test_participant_view_contains_registrant_form_details(): void
    {
        $event = $this->createEvent();
        $tier = EventTicketTier::factory()->create([
            'event_id' => $event->id,
            'name' => 'Reguler',
            'code' => 'REG',
        ]);

        Registrant::factory()->create([
            'event_id' => $event->id,
            'ticket_tier_id' => $tier->id,
            'full_name' => 'Budi Santoso',
            'nik' => '3171234567890001',
            'gender' => 'Laki-laki',
            'province' => 'Daerah Khusus Ibukota Jakarta',
            'city' => 'Kota Administrasi Jakarta Selatan',
        ]);

        $row = DB::table('peserta_event_satu')->first();

        $this->assertNotNull($row);
        $this->assertSame('Budi Santoso', $row->full_name);
        $this->assertSame('3171234567890001', $row->nik);
        $this->assertSame('Laki-laki', $row->gender);
        $this->assertSame('Daerah Khusus Ibukota Jakarta', $row->province);
        $this->assertSame('Reguler', $row->ticket_tier);
        $this->assertSame('REG', $row->ticket_tier_code);
    }

    public function test_participant_view_is_scoped_to_its_event(): void
    {
        $this->createEvent(['slug' => 'event-satu']);
        $this->createEvent(['slug' => 'event-dua', 'name' => 'Event Dua']);

        $this->assertContains('peserta_event_satu', $this->viewNames());
        $this->assertContains('peserta_event_dua', $this->viewNames());

        $row = DB::table('peserta_event_dua')->first();
        $this->assertNull($row);
    }

    public function test_renaming_event_slug_renames_view(): void
    {
        $event = $this->createEvent(['slug' => 'nama-lama']);

        $event->update(['slug' => 'nama-baru']);

        $names = $this->viewNames();
        $this->assertNotContains('peserta_nama_lama', $names);
        $this->assertContains('peserta_nama_baru', $names);
    }

    public function test_deleting_event_drops_view(): void
    {
        $event = $this->createEvent(['slug' => 'akan-dihapus']);

        $this->assertContains('peserta_akan_dihapus', $this->viewNames());

        $event->delete();

        $this->assertNotContains('peserta_akan_dihapus', $this->viewNames());
    }
}
