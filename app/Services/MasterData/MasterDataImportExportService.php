<?php

namespace App\Services\MasterData;

use App\Enums\RegistrationStatus;
use App\Models\Event;
use App\Models\Registrant;
use App\Services\Ticket\TicketGenerationService;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Impor/ekspor master data per event dalam format CSV:
 * tier tiket, add-on, voucher, dan pendaftar.
 */
class MasterDataImportExportService
{
    /**
     * @var array<int, string>
     */
    public const TYPES = ['tiers', 'addons', 'vouchers', 'registrants', 'result'];

    /**
     * Kolom (header) tiap tipe master data, sesuai urutan ekspor.
     *
     * @var array<string, array<int, string>>
     */
    private const COLUMNS = [
        'tiers' => [
            'name', 'code', 'slug', 'price', 'fee', 'description',
            'stock_qty', 'seat_qty', 'sold_qty',
            'is_downloadable', 'is_show_in_list', 'is_selectable_on_site', 'is_selectable_online',
        ],
        'addons' => [
            'name', 'code', 'slug', 'price', 'fee', 'description',
            'qty', 'max_qty', 'sold_qty', 'order_by',
            'is_selectable_on_site', 'is_selectable_online',
        ],
        'vouchers' => [
            'code', 'value', 'is_percent', 'is_single_use', 'is_active',
            'total_uses', 'uses', 'expired_at', 'order_by',
        ],
        'registrants' => [
            'full_name', 'email', 'phone', 'status', 'ticket_tier',
            'city', 'address', 'notes',
            'subtotal', 'addons_total', 'discount', 'total', 'fee',
            'is_paid', 'participant_token',
        ],
        'result' => [
            'category_id', 'board_status', 'result_details',
            'final_score', 'winner', 'mvp',
        ],
    ];

    /**
     * Kolom wajib ada di file impor; kolom lain opsional (memakai default).
     *
     * @return array<string, array<int, string>>
     */
    private const REQUIRED_COLUMNS = [
        'tiers' => ['name', 'code', 'price'],
        'addons' => ['name', 'code', 'price'],
        'vouchers' => ['code', 'value'],
        'registrants' => ['full_name', 'email', 'ticket_tier'],
        'result' => ['category_id', 'board_status', 'result_details'],
    ];

    /**
     * Baris contoh untuk template CSV.
     *
     * @var array<string, array<int, string>>
     */
    private const SAMPLES = [
        'tiers' => ['Regular', 'REG', 'regular', '150000', '5000', 'Tiket masuk umum', '100', '0', '0', '0', '1', '1', '1'],
        'addons' => ['Merch Kaos', 'MERCH', 'merch-kaos', '75000', '0', 'Kaos eksklusif', '50', '5', '0', '1', '1', '1'],
        'vouchers' => ['HEMAT10', '10', '1', '0', '1', '100', '0', '2026-12-31 23:59:59', '1'],
        'registrants' => [
            'Budi Santoso', 'budi@example.test', '08123456789', 'confirmed', 'REG',
            'Bandung', 'Jl. Merdeka No. 1', '', '150000', '0', '0', '150000', '5000', '1', '',
        ],
        'result' => [
            'Olahraga', 'SCE-PART-001/2023', 'final: Tim A 2-1 Tim B', '2-1', 'Tim A', 'John Doe',
        ],
    ];

    public function __construct(
        private readonly TicketGenerationService $ticket,
    ) {}

    public static function isValidType(string $type): bool
    {
        return in_array($type, self::TYPES, true);
    }

    /**
     * Label tampilan tiap tipe master data.
     *
     * @return array<string, string>
     */
    public static function labels(): array
    {
        return [
            'tiers' => 'Tier Tiket',
            'addons' => 'Add-On',
            'vouchers' => 'Voucher',
            'registrants' => 'Pendaftar',
        ];
    }

    /**
     * @return array<int, string>
     */
    public function columns(string $type): array
    {
        return self::COLUMNS[$type];
    }

    /**
     * @return array<int, string>
     */
    public function sampleRow(string $type): array
    {
        return self::SAMPLES[$type];
    }

    /**
     * @return array<int, string>
     */
    public function requiredColumns(string $type): array
    {
        return self::REQUIRED_COLUMNS[$type];
    }

    /**
     * Baris data (sesuai urutan kolom) untuk unduhan CSV.
     *
     * @return array<int, array<int, mixed>>
     */
    public function exportRecords(string $type, Event $event): array
    {
        return match ($type) {
            'tiers' => $event->tiers()->orderBy('id')->get()
                ->map(fn ($tier) => $this->rowFor($type, $tier))->all(),
            'addons' => $event->addons()->orderBy('id')->get()
                ->map(fn ($addon) => $this->rowFor($type, $addon))->all(),
            'vouchers' => $event->vouchers()->orderBy('id')->get()
                ->map(fn ($voucher) => $this->rowFor($type, $voucher))->all(),
            'registrants' => $event->registrants()->orderBy('created_at')->get()
                ->map(fn (Registrant $registrant) => $this->rowFor($type, $registrant))->all(),
            default => [],
        };
    }

    /**
     * Baca file CSV menjadi header + baris asosiatif.
     *
     * @return array{columns: array<int, string>, records: array<int, array<string, string>>}
     */
    public function parseFile(string $path): array
    {
        $handle = fopen($path, 'r');

        if ($handle === false) {
            throw ValidationException::withMessages([
                'import' => 'File CSV tidak bisa dibaca.',
            ]);
        }

        try {
            $firstLine = fgets($handle);

            if ($firstLine === false) {
                return ['columns' => [], 'records' => []];
            }

            $delimiter = $this->detectDelimiter($firstLine);

            // Buang BOM Excel bila ada, lalu ulangi dari awal file.
            $columns = array_map(
                fn (string $column) => trim($this->stripBom($column)),
                str_getcsv($firstLine, $delimiter, '"', '\\') ?: []
            );

            $records = [];

            while (($row = fgetcsv($handle, 0, $delimiter, '"', '\\')) !== false) {
                if ($row === [null] || trim(implode('', array_map(strval(...), $row))) === '') {
                    continue;
                }

                $records[] = array_map(
                    fn ($value) => trim($this->stripBom((string) $value)),
                    array_combine($columns, $row) ?: []
                );
            }

            return ['columns' => $columns, 'records' => $records];
        } finally {
            fclose($handle);
        }
    }

    /**
     * Impor baris CSV: seluruh baris divalidasi dulu, lalu disimpan dalam satu transaksi.
     * Mengembalikan jumlah data baru/diperbarui, atau daftar kesalahan per baris.
     *
     * @param  array<int, array<string, string>>  $records
     * @return array{created: int, updated: int, errors: array<int, string>}
     */
    public function import(string $type, Event $event, array $records): array
    {
        $errors = [];
        $prepared = [];
        $seenKeys = [];

        foreach (array_values($records) as $index => $record) {
            $line = $index + 2;
            $payload = $this->prepareRow($type, $event, $record, $line, $errors);

            if ($payload === null) {
                continue;
            }

            $key = $this->upsertKey($type, $payload);

            if (isset($seenKeys[$key])) {
                $errors[] = "Baris {$line}: data duplikat dengan baris {$seenKeys[$key]} ({$key}).";

                continue;
            }

            $seenKeys[$key] = $line;
            $prepared[] = $payload;
        }

        if ($errors !== []) {
            return ['created' => 0, 'updated' => 0, 'errors' => $errors];
        }

        $created = 0;
        $updated = 0;

        DB::transaction(function () use ($type, $event, $prepared, &$created, &$updated) {
            foreach ($prepared as $payload) {
                $this->persist($type, $event, $payload) ? $created++ : $updated++;
            }
        });

        return ['created' => $created, 'updated' => $updated, 'errors' => []];
    }

    /**
     * Validasi + normalisasi satu baris menjadi payload siap simpan.
     *
     * @param  array<string, string>  $record
     * @param  array<int, string>  $errors
     * @return array<string, mixed>|null
     */
    private function prepareRow(string $type, Event $event, array $record, int $line, array &$errors): ?array
    {
        if ($type === 'registrants') {
            return $this->prepareRegistrantRow($event, $record, $line, $errors);
        }

        $slug = $this->text($record['slug'] ?? '');

        $payload = [
            'name' => $this->text($record['name'] ?? ''),
            'code' => $this->text($record['code'] ?? ''),
            'slug' => $slug !== '' ? $slug : Str::slug($this->text($record['code'] ?? $record['name'] ?? 'item')),
            'price' => $this->number($record['price'] ?? null, 0),
            'fee' => $this->number($record['fee'] ?? null, 0),
            'description' => $this->text($record['description'] ?? '') ?: null,
        ];

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'fee' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
        ];

        if ($type === 'tiers') {
            $payload += [
                'stock_qty' => $this->integer($record['stock_qty'] ?? null, 0),
                'seat_qty' => $this->integer($record['seat_qty'] ?? null, 0),
                'sold_qty' => $this->integer($record['sold_qty'] ?? null, 0),
                'is_downloadable' => $this->boolean($record['is_downloadable'] ?? null, false),
                'is_show_in_list' => $this->boolean($record['is_show_in_list'] ?? null, true),
                'is_selectable_on_site' => $this->boolean($record['is_selectable_on_site'] ?? null, true),
                'is_selectable_online' => $this->boolean($record['is_selectable_online'] ?? null, true),
            ];

            $rules += [
                'stock_qty' => ['nullable', 'integer', 'min:0'],
                'seat_qty' => ['nullable', 'integer', 'min:0'],
                'sold_qty' => ['nullable', 'integer', 'min:0'],
            ];
        }

        if ($type === 'addons') {
            $payload += [
                'qty' => $this->integer($record['qty'] ?? null, 0),
                'max_qty' => $this->integer($record['max_qty'] ?? null, 0),
                'sold_qty' => $this->integer($record['sold_qty'] ?? null, 0),
                'order_by' => $this->integer($record['order_by'] ?? null, 0),
                'is_selectable_on_site' => $this->boolean($record['is_selectable_on_site'] ?? null, true),
                'is_selectable_online' => $this->boolean($record['is_selectable_online'] ?? null, true),
            ];

            $rules += [
                'qty' => ['nullable', 'integer', 'min:0'],
                'max_qty' => ['nullable', 'integer', 'min:0'],
                'sold_qty' => ['nullable', 'integer', 'min:0'],
                'order_by' => ['nullable', 'integer', 'min:0'],
            ];
        }

        if ($type === 'vouchers') {
            $payload = [
                'code' => $payload['code'],
                'value' => $this->number($record['value'] ?? null, 0),
                'is_percent' => $this->boolean($record['is_percent'] ?? null, false),
                'is_single_use' => $this->boolean($record['is_single_use'] ?? null, false),
                'is_active' => $this->boolean($record['is_active'] ?? null, true),
                'total_uses' => $this->integer($record['total_uses'] ?? null, 0),
                'uses' => $this->integer($record['uses'] ?? null, 0),
                'expired_at' => $this->text($record['expired_at'] ?? '') ?: null,
                'order_by' => $this->integer($record['order_by'] ?? null, 0),
            ];

            $rules = [
                'code' => ['required', 'string', 'max:255'],
                'value' => ['required', 'numeric', 'min:0'],
                'is_percent' => ['boolean'],
                'is_single_use' => ['boolean'],
                'is_active' => ['boolean'],
                'total_uses' => ['nullable', 'integer', 'min:0'],
                'uses' => ['nullable', 'integer', 'min:0'],
                'expired_at' => ['nullable', 'date'],
                'order_by' => ['nullable', 'integer', 'min:0'],
            ];
        }

        $validator = Validator::make($payload, $rules);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $message) {
                $errors[] = "Baris {$line}: {$message}";
            }

            return null;
        }

        return $payload;
    }

    /**
     * Validasi + normalisasi baris pendaftar (butuh kode tier milik event).
     *
     * @param  array<string, string>  $record
     * @param  array<int, string>  $errors
     * @return array<string, mixed>|null
     */
    private function prepareRegistrantRow(Event $event, array $record, int $line, array &$errors): ?array
    {
        $tierCode = $this->text($record['ticket_tier'] ?? '');
        $tier = $tierCode !== ''
            ? $event->tiers()->where('code', $tierCode)->first()
            : null;

        if (! $tier) {
            $errors[] = "Baris {$line}: kode tier \"{$tierCode}\" tidak ditemukan pada event ini.";

            return null;
        }

        $subtotal = $this->number($record['subtotal'] ?? null, (float) $tier->price);
        $addonsTotal = $this->number($record['addons_total'] ?? null, 0);
        $discount = $this->number($record['discount'] ?? null, 0);
        $total = $this->numberOrNull($record['total'] ?? null)
            ?? max(0, $subtotal + $addonsTotal - $discount);

        $payload = [
            'full_name' => $this->text($record['full_name'] ?? ''),
            'email' => $this->text($record['email'] ?? ''),
            'phone' => $this->text($record['phone'] ?? '') ?: null,
            'status' => $this->text($record['status'] ?? '') ?: RegistrationStatus::PENDING,
            'ticket_tier_id' => $tier->id,
            'city' => $this->text($record['city'] ?? '') ?: null,
            'address' => $this->text($record['address'] ?? '') ?: null,
            'notes' => $this->text($record['notes'] ?? '') ?: null,
            'subtotal' => $subtotal,
            'addons_total' => $addonsTotal,
            'discount' => $discount,
            'total' => $total,
            'fee' => $this->number($record['fee'] ?? null, 0),
            'is_paid' => $this->boolean($record['is_paid'] ?? null, false),
            'participant_token' => $this->text($record['participant_token'] ?? '')
                ?: 'PART-'.strtoupper(uniqid()),
        ];

        $validator = Validator::make($payload, [
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'status' => ['required', Rule::in([
                RegistrationStatus::PENDING,
                RegistrationStatus::PAID,
                RegistrationStatus::CONFIRMED,
                RegistrationStatus::REJECTED,
                RegistrationStatus::CANCELLED,
                RegistrationStatus::EXPIRED,
            ])],
            'subtotal' => ['required', 'numeric', 'min:0'],
            'total' => ['required', 'numeric', 'min:0'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $message) {
                $errors[] = "Baris {$line}: {$message}";
            }

            return null;
        }

        return $payload;
    }

    /**
     * Simpan payload (insert bila belum ada, update bila sudah ada).
     *
     * @param  array<string, mixed>  $payload
     */
    private function persist(string $type, Event $event, array $payload): bool
    {
        return match ($type) {
            'tiers' => $this->upsert(
                $event->tiers(),
                'code',
                $payload['code'],
                $payload
            ),
            'addons' => $this->upsert(
                $event->addons(),
                'code',
                $payload['code'],
                $payload
            ),
            'vouchers' => $this->upsert(
                $event->vouchers(),
                'code',
                $payload['code'],
                $payload
            ),
            'registrants' => $this->upsertRegistrant($event, $payload),
            default => false,
        };
    }

    /**
     * @param  HasMany  $relation
     * @param  array<string, mixed>  $payload
     */
    private function upsert($relation, string $keyColumn, string $keyValue, array $payload): bool
    {
        $existing = $relation->where($keyColumn, $keyValue)->first();

        if ($existing) {
            $existing->fill($payload);
            $existing->save();

            return false;
        }

        $relation->create($payload);

        return true;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function upsertRegistrant(Event $event, array $payload): bool
    {
        $existing = $event->registrants()->where('email', $payload['email'])->first();
        $isNew = $existing === null;

        if ($existing) {
            $existing->fill($payload);
            $existing->save();
            $registrant = $existing;
        } else {
            $registrant = $event->registrants()->create($payload + [
                'session_key' => 'import_'.uniqid(),
                'is_paid' => $payload['is_paid'],
            ]);
        }

        if ($registrant->is_paid && ! $registrant->paid_at) {
            $registrant->forceFill(['paid_at' => now()])->save();
        }

        if ($isNew && ! $registrant->ticketItems()->exists()) {
            $this->ticket->generate($registrant);
        }

        return $isNew;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function upsertKey(string $type, array $payload): string
    {
        return match ($type) {
            'registrants' => 'email:'.mb_strtolower($payload['email']),
            default => 'code:'.mb_strtoupper($payload['code']),
        };
    }

    /**
     * @param  array<string, string>  $record
     */
    private function rowFor(string $type, $model): array
    {
        $row = [];

        foreach (self::COLUMNS[$type] as $column) {
            $row[$column] = match ($column) {
                'ticket_tier' => (string) ($model->ticketTier?->code ?? ''),
                'expired_at' => $model->expired_at
                    ? Carbon::parse($model->expired_at)->format('Y-m-d H:i:s')
                    : '',
                'address', 'description', 'notes' => (string) ($model->{$column} ?? ''),
                'is_downloadable', 'is_show_in_list', 'is_selectable_on_site', 'is_selectable_online',
                'is_percent', 'is_single_use', 'is_active', 'is_paid' => $model->{$column} ? '1' : '0',
                default => (string) ($model->{$column} ?? ''),
            };
        }

        return $row;
    }

    private function text(mixed $value): string
    {
        return trim((string) $value);
    }

    private function numberOrNull(mixed $value): ?float
    {
        $text = $this->text($value);

        return $text === '' ? null : (float) str_replace([',', ' '], '', $text);
    }

    private function number(mixed $value, float $default): float
    {
        return $this->numberOrNull($value) ?? $default;
    }

    private function integer(mixed $value, int $default): int
    {
        $number = $this->numberOrNull($value);

        return $number === null ? $default : (int) round($number);
    }

    /**
     * Terima 1/0, true/false, yes/no, ya/tidak (huruf besar/kecil bebas).
     */
    private function boolean(mixed $value, bool $default): bool
    {
        $text = mb_strtolower($this->text($value));

        if ($text === '') {
            return $default;
        }

        if (in_array($text, ['1', 'true', 'yes', 'y', 'ya'], true)) {
            return true;
        }

        if (in_array($text, ['0', 'false', 'no', 'n', 'tidak'], true)) {
            return false;
        }

        return $default;
    }

    private function stripBom(string $value): string
    {
        return str_starts_with($value, "\xEF\xBB\xBF") ? substr($value, 3) : $value;
    }

    /**
     * Dukung pemisah koma (bawaan) maupun titik koma (pilihan Excel Indonesia).
     */
    private function detectDelimiter(string $line): string
    {
        $semicolons = substr_count($line, ';');
        $commas = substr_count($line, ',');

        return $semicolons > $commas ? ';' : ',';
    }
}
