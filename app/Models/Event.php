<?php

namespace App\Models;

use App\Enums\EventType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $slug
 * @property string $name
 * @property string|null $logo
 * @property string|null $banner
 * @property int|null $category_id
 * @property string $event_type
 * @property string|null $description
 * @property string|null $location
 * @property Carbon|null $starts_at
 * @property Carbon|null $ends_at
 * @property string|null $venue_details
 * @property bool $is_published
 * @property int|null $max_registrants
 * @property int $timezone
 * @property int $registrant_hours
 * @property int $delete_at
 * @property int $dudent_at
 * @property string $status
 * @property string $board_status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property EventTicketTier[] $tiers
 * @property EventCategory|null $category
 * @property EventCustomField[] $customFields
 * @property EventAddon[] $addons
 * @property EventVoucher[] $vouchers
 * @property Registrant[] $registrants
 */
class Event extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'slug', 'name', 'logo', 'banner', 'category_id', 'event_type', 'description', 'location', 'provinsi', 'city', 'starts_at', 'ends_at',
        'venue_details', 'is_published', 'max_registrants', 'timezone', 'registrant_hours',
        'delete_at', 'dudent_at', 'price', 'fee', 'submit_fee', 'status', 'board_status',
    ];

    protected $casts = [
        'id' => 'string',
        'is_published' => 'boolean',
        'max_registrants' => 'int',
        'timezone' => 'int',
        'registrant_hours' => 'int',
        'delete_at' => 'int',
        'dudent_at' => 'int',
        'price' => 'decimal:2',
        'fee' => 'decimal:2',
        'submit_fee' => 'decimal:2',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(EventCategory::class, 'category_id');
    }

    public function tiers(): HasMany
    {
        return $this->hasMany(EventTicketTier::class);
    }

    public function customFields(): HasMany
    {
        return $this->hasMany(EventCustomField::class);
    }

    public function addons(): HasMany
    {
        return $this->hasMany(EventAddon::class);
    }

    public function vouchers(): HasMany
    {
        return $this->hasMany(EventVoucher::class);
    }

    public function registrants(): HasMany
    {
        return $this->hasMany(Registrant::class);
    }

    public function committees(): HasMany
    {
        return $this->hasMany(EventCommittee::class);
    }

    /**
     * Event sport/esport: kategorinya ditandai is_sport.
     */
    public function isSport(): bool
    {
        return (bool) ($this->category?->is_sport);
    }

    /**
     * Apakah event berjenis kompetisi (bukan event hiburan).
     */
    public function isCompetition(): bool
    {
        return $this->event_type === EventType::KOMPETISI;
    }

    /**
     * Label tampilan jenis event (Kompetisi / Event Hiburan).
     */
    public function eventTypeLabel(): string
    {
        return EventType::label($this->event_type ?: EventType::KOMPETISI);
    }

    /**
     * URL publik untuk avatar (logo) event, null bila belum diunggah.
     */
    public function getLogoUrlAttribute(): ?string
    {
        return $this->resolveMediaUrl($this->logo);
    }

    /**
     * URL publik untuk banner event, null bila belum diunggah.
     */
    public function getBannerUrlAttribute(): ?string
    {
        return $this->resolveMediaUrl($this->banner);
    }

    private function resolveMediaUrl(?string $path): ?string
    {
        if ($path === null || trim($path) === '') {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://', '//', 'data:'])) {
            return $path;
        }

        return Storage::disk('public')->url($path);
    }
}
