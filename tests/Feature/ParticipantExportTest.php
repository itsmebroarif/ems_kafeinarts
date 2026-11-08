<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventTicketTier;
use App\Models\Registrant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ParticipantExportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
    }

    private function createEventWithParticipant(): Event
    {
        $event = Event::factory()->create(['slug' => 'event-export']);
        $tier = EventTicketTier::factory()->create([
            'event_id' => $event->id,
            'name' => 'Reguler',
            'code' => 'REG',
        ]);

        Registrant::factory()->create([
            'event_id' => $event->id,
            'ticket_tier_id' => $tier->id,
            'full_name' => 'Siti Aminah',
            'email' => 'siti@example.test',
            'nik' => '3171234567890001',
            'gender' => 'Perempuan',
        ]);

        return $event;
    }

    public function test_export_creates_csv_with_header_and_rows(): void
    {
        $this->createEventWithParticipant();

        $this->artisan('events:export-peserta', ['slug' => 'event-export', '--format' => 'csv'])
            ->assertExitCode(0);

        $files = Storage::disk('local')->files('exports/peserta');
        $this->assertCount(1, $files);
        $this->assertStringEndsWith('.csv', $files[0]);

        $csv = Storage::disk('local')->get($files[0]);
        $this->assertStringContainsString('id,tanggal_daftar,full_name', $csv);
        $this->assertStringContainsString('Siti Aminah', $csv);
        $this->assertStringContainsString('3171234567890001', $csv);
    }

    public function test_export_creates_sql_dump_with_table_and_insert(): void
    {
        $this->createEventWithParticipant();

        $this->artisan('events:export-peserta', ['slug' => 'event-export', '--format' => 'sql'])
            ->assertExitCode(0);

        $files = Storage::disk('local')->files('exports/peserta');
        $this->assertCount(1, $files);

        $sql = Storage::disk('local')->get($files[0]);
        $this->assertStringContainsString('CREATE TABLE `peserta_event_export`', $sql);
        $this->assertStringContainsString('DROP TABLE IF EXISTS `peserta_event_export`', $sql);
        $this->assertStringContainsString('Siti Aminah', $sql);
        $this->assertStringContainsString('INSERT INTO `peserta_event_export`', $sql);
    }

    public function test_export_all_events_when_slug_omitted(): void
    {
        $this->createEventWithParticipant();
        Event::factory()->create(['slug' => 'event-export-kedua']);

        $this->artisan('events:export-peserta', ['--format' => 'csv'])
            ->assertExitCode(0);

        $files = Storage::disk('local')->files('exports/peserta');
        $this->assertCount(2, $files);
    }

    public function test_export_unknown_slug_fails(): void
    {
        $this->artisan('events:export-peserta', ['slug' => 'tidak-ada'])
            ->assertExitCode(1);
    }

    public function test_sync_participant_views_drops_orphan_views(): void
    {
        $event = $this->createEventWithParticipant();

        // View yatim: slug event berubah langsung di DB (mensimulasikan perubahan manual).
        DB::statement('DROP VIEW IF EXISTS `peserta_yatim`');
        DB::statement('CREATE VIEW `peserta_yatim` AS SELECT * FROM registrants');

        $this->artisan('events:sync-participant-views')
            ->assertExitCode(0);

        $views = collect(DB::select("SELECT name FROM sqlite_master WHERE type = 'view'"))->pluck('name');
        $this->assertContains('peserta_event_export', $views->all());
        $this->assertNotContains('peserta_yatim', $views->all());
    }
}
