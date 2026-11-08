<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Services\ParticipantViewService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

#[Signature('events:export-peserta {slug? : Slug event — kosongkan untuk semua event} {--format=both : csv, sql, atau both} {--dir=exports/peserta : Direktori output relatif terhadap storage/app}')]
#[Description('Export data peserta per event ke CSV dan/atau file SQL (tabel fisik) untuk migrasi & keperluan eksternal')]
class ExportParticipantViews extends Command
{
    /**
     * Definisi kolom untuk dump CREATE TABLE (nama => definisi tipe).
     *
     * @var array<string, string>
     */
    private const SQL_COLUMN_TYPES = [
        'id' => 'char(36) NOT NULL',
        'tanggal_daftar' => 'timestamp NULL',
        'full_name' => 'varchar(255) NOT NULL',
        'email' => 'varchar(255) NOT NULL',
        'phone' => 'varchar(255) NULL',
        'nik' => 'varchar(16) NULL',
        'gender' => 'varchar(20) NULL',
        'nationality' => 'varchar(255) NULL',
        'province' => 'varchar(255) NULL',
        'city' => 'varchar(255) NULL',
        'district' => 'varchar(255) NULL',
        'village' => 'varchar(255) NULL',
        'address' => 'text NULL',
        'postal_code' => 'varchar(255) NULL',
        'registrant_type' => 'varchar(255) NULL',
        'guests_quantity' => 'varchar(255) NULL',
        'guests_quantity_other' => 'varchar(255) NULL',
        'ticket_tier' => 'varchar(255) NOT NULL',
        'ticket_tier_code' => 'varchar(255) NOT NULL',
        'ticket_price' => 'decimal(12,2) NOT NULL DEFAULT 0.00',
        'voucher_code' => 'varchar(255) NULL',
        'status' => 'varchar(255) NOT NULL DEFAULT \'pending\'',
        'is_paid' => 'tinyint(1) NOT NULL DEFAULT 0',
        'paid_at' => 'datetime NULL',
        'subtotal' => 'decimal(12,2) NOT NULL DEFAULT 0.00',
        'addons_total' => 'decimal(12,2) NOT NULL DEFAULT 0.00',
        'discount' => 'decimal(12,2) NOT NULL DEFAULT 0.00',
        'fee' => 'decimal(12,2) NOT NULL DEFAULT 0.00',
        'total' => 'decimal(12,2) NOT NULL DEFAULT 0.00',
        'notes' => 'text NULL',
        'custom_fields' => 'text NULL',
        'updated_at' => 'timestamp NULL',
    ];

    private const SQL_NUMERIC_COLUMNS = ['ticket_price', 'subtotal', 'addons_total', 'discount', 'fee', 'total'];

    public function handle(ParticipantViewService $service): int
    {
        $format = strtolower((string) $this->option('format'));

        if (! in_array($format, ['csv', 'sql', 'both'], true)) {
            $this->components->error('Format tidak valid. Gunakan csv, sql, atau both.');

            return self::FAILURE;
        }

        $slug = $this->argument('slug');
        $eventsQuery = Event::query();

        if ($slug) {
            $eventsQuery->where('slug', $slug);
        }

        $events = $eventsQuery->get();

        if ($events->isEmpty()) {
            $this->components->error($slug
                ? "Event dengan slug '{$slug}' tidak ditemukan."
                : 'Tidak ada event yang ditemukan.');

            return self::FAILURE;
        }

        $dir = trim((string) $this->option('dir'), '/');
        $stamp = now()->format('Ymd-His');
        $exported = 0;

        foreach ($events as $event) {
            $viewName = $service->viewName($event->slug);

            try {
                $rows = DB::table($viewName)->orderBy('tanggal_daftar')->get();
            } catch (\Throwable $e) {
                $this->components->warn("{$viewName}: gagal membaca view — {$e->getMessage()}. Jalankan events:sync-participant-views.");

                continue;
            }

            $rowCount = $rows->count();

            if ($format === 'csv' || $format === 'both') {
                $csvPath = "{$dir}/{$viewName}_{$stamp}.csv";
                Storage::disk('local')->put($csvPath, $this->buildCsv($rows));
                $this->components->info("{$viewName}: {$rowCount} peserta → ".Storage::disk('local')->path($csvPath));
                $exported++;
            }

            if ($format === 'sql' || $format === 'both') {
                $sqlPath = "{$dir}/{$viewName}_{$stamp}.sql";
                Storage::disk('local')->put($sqlPath, $this->buildSqlDump($viewName, $event, $rows));
                $this->components->info("{$viewName}: {$rowCount} peserta → ".Storage::disk('local')->path($sqlPath));
                $exported++;
            }
        }

        $this->components->info("Selesai. {$exported} file export dibuat.");

        return self::SUCCESS;
    }

    /**
     * @param  Collection<int, object>  $rows
     */
    private function buildCsv(Collection $rows): string
    {
        $handle = fopen('php://temp', 'r+');

        fputcsv($handle, ParticipantViewService::COLUMNS);

        foreach ($rows as $row) {
            fputcsv($handle, array_map(
                fn (string $column) => $row->{$column},
                ParticipantViewService::COLUMNS
            ));
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return $csv;
    }

    /**
     * @param  Collection<int, object>  $rows
     */
    private function buildSqlDump(string $viewName, Event $event, Collection $rows): string
    {
        $columns = ParticipantViewService::COLUMNS;
        $eventName = str_replace("'", "''", $event->name);

        $lines = [
            '-- Export peserta event: '.$eventName.' (slug: '.$event->slug.')',
            '-- Dibuat pada '.now()->toDateTimeString().' oleh events:export-peserta',
            '-- File ini membuat TABEL FISIK (bukan view) di database tujuan.',
            '-- Aman di-import berulang: tabel lama akan dihapus & dibuat ulang.',
            '',
            "DROP TABLE IF EXISTS `{$viewName}`;",
            "CREATE TABLE `{$viewName}` (",
            '  '.implode(",\n  ", array_map(
                fn (string $column) => '`'.$column.'` '.self::SQL_COLUMN_TYPES[$column],
                $columns
            )),
            ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
            '',
        ];

        if ($rows->isNotEmpty()) {
            $lines[] = 'INSERT INTO `'.$viewName.'` (`'.implode('`, `', $columns).'`) VALUES';
            $lines[] = $rows->map(function ($row) use ($columns) {
                $values = array_map(fn (string $column) => $this->sqlLiteral($row->{$column}, $column), $columns);

                return '('.implode(', ', $values).')';
            })->implode(",\n").';';
            $lines[] = '';
        }

        return implode("\n", $lines);
    }

    private function sqlLiteral(mixed $value, string $column): string
    {
        if ($value === null) {
            return 'NULL';
        }

        if (in_array($column, self::SQL_NUMERIC_COLUMNS, true)) {
            return (string) (float) $value;
        }

        if ($column === 'is_paid') {
            return $value ? '1' : '0';
        }

        return "'".str_replace(['\\', "'"], ['\\\\', "\\'"], (string) $value)."'";
    }
}
