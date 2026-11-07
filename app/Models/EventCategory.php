<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $color
 * @property bool $is_active
 * @property bool $is_sport
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Event[] $events
 */
class EventCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'color', 'is_active', 'is_sport',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_sport' => 'boolean',
    ];

    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'category_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Kategori sport/esport (berlaku untuk kolom Result pada papan event).
     */
    public function scopeSport(Builder $query): Builder
    {
        return $query->where('is_sport', true);
    }

    /**
     * Warna badge dalam format rgb() siap pakai di inline style.
     *
     * @return array{0: int, 1: int, 2: int}
     */
    public function rgb(): array
    {
        $hex = ltrim($this->color ?: '#0d6efd', '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        if (strlen($hex) !== 6) {
            return [13, 110, 253];
        }

        return [
            (int) hexdec(substr($hex, 0, 2)),
            (int) hexdec(substr($hex, 2, 2)),
            (int) hexdec(substr($hex, 4, 2)),
        ];
    }
}
