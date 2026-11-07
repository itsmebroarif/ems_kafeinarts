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
     * Rebuild semua view peserta — dipakai juga oleh command `events:sync-participant-views`.
     *
     * @return int jumlah view yang dibuat
     */
    public function syncAll(): int
    {
        $count = 0;

        foreach (Event::query()->cursor() as $event) {
            $this->sync($event);
            $count++;
        }

        return $count;
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
