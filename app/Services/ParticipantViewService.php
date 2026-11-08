<?php

namespace App\Services;

use App\Models\Event;
use Illuminate\Support\Facades\DB;

/**
 * Menjaga SQL view `peserta_<slug>` yang berisi detail peserta untuk tiap event.
 *
 * View adalah cerminan langsung dari tabel `registrants` (JOIN tier & voucher),
 * bukan tabel fisik — data selalu sinkron dan tidak perlu di-duplikasi.
 * View dibuat otomatis saat event dibuat, di-rename saat slug berubah,
 * dan dihapus saat event dihapus.
 */
class ParticipantViewService
{
    public const PREFIX = 'peserta_';

    /**
     * Kolom view secara berurutan — dipakai sebagai header export (CSV) & daftar kolom SQL dump.
     *
     * @var array<int, string>
     */
    public const COLUMNS = [
        'id', 'tanggal_daftar', 'full_name', 'email', 'phone', 'nik', 'gender', 'nationality',
        'province', 'city', 'district', 'village', 'address', 'postal_code', 'registrant_type',
        'guests_quantity', 'guests_quantity_other', 'ticket_tier', 'ticket_tier_code', 'ticket_price',
        'voucher_code', 'status', 'is_paid', 'paid_at', 'subtotal', 'addons_total', 'discount',
        'fee', 'total', 'notes', 'custom_fields', 'updated_at',
    ];

    public function viewName(string $slug): string
    {
        return self::PREFIX.preg_replace('/[^a-z0-9_]+/', '_', strtolower($slug));
    }

    public function sync(Event $event): void
    {
        $name = $this->viewName($event->slug);

        DB::statement("DROP VIEW IF EXISTS `{$name}`");
        DB::statement("CREATE VIEW `{$name}` AS ".$this->definition($event));
    }

    public function drop(string $slug): void
    {
        DB::statement('DROP VIEW IF EXISTS `'.$this->viewName($slug).'`');
    }

    /**
     * Rebuild semua view peserta & hapus view yatim (event sudah tidak ada).
     *
     * @return array{synced: int, dropped: int}
     */
    public function syncAll(): array
    {
        $count = 0;

        foreach (Event::query()->cursor() as $event) {
            $this->sync($event);
            $count++;
        }

        return ['synced' => $count, 'dropped' => $this->dropOrphans()];
    }

    /**
     * Hapus view `peserta_*` yang event-nya sudah tidak ada (yatim).
     *
     * @return int jumlah view yang dihapus
     */
    public function dropOrphans(): int
    {
        $views = $this->existingParticipantViews();
        $valid = Event::query()->pluck('slug')->map(fn (string $slug) => $this->viewName($slug));
        $dropped = 0;

        foreach ($views as $view) {
            if ($valid->contains($view)) {
                continue;
            }

            DB::statement("DROP VIEW IF EXISTS `{$view}`");
            $dropped++;
        }

        return $dropped;
    }

    /**
     * @return array<int, string>
     */
    private function existingParticipantViews(): array
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            return collect(DB::select("SELECT name FROM sqlite_master WHERE type = 'view' AND name GLOB 'peserta_*'"))
                ->pluck('name')
                ->all();
        }

        $database = DB::connection()->getDatabaseName();

        return collect(DB::select("SHOW FULL TABLES WHERE Table_type = 'VIEW' AND `Tables_in_{$database}` LIKE 'peserta\\_%'"))
            ->map(fn ($row) => $row->{"Tables_in_{$database}"})
            ->all();
    }

    public function definition(Event $event): string
    {
        $eventId = str_replace("'", "''", $event->id);

        return <<<SQL
SELECT
    r.id,
    r.created_at AS tanggal_daftar,
    r.full_name,
    r.email,
    r.phone,
    r.nik,
    r.gender,
    r.nationality,
    r.province,
    r.city,
    r.district,
    r.village,
    r.address,
    r.postal_code,
    r.registrant_type,
    r.guests_quantity,
    r.guests_quantity_other,
    t.name AS ticket_tier,
    t.code AS ticket_tier_code,
    t.price AS ticket_price,
    v.code AS voucher_code,
    r.status,
    r.is_paid,
    r.paid_at,
    r.subtotal,
    r.addons_total,
    r.discount,
    r.fee,
    r.total,
    r.notes,
    r.custom_fields,
    r.updated_at
FROM registrants r
INNER JOIN event_ticket_tiers t ON t.id = r.ticket_tier_id
LEFT JOIN event_vouchers v ON v.id = r.voucher_id
WHERE r.event_id = '{$eventId}'
SQL;
    }
}
