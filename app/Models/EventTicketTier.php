<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property string $image
 * @property string $price
 * @property string $fee
 * @property string|null $description
 * @property bool $is_downloadable
 * @property bool $is_show_in_list
 * @property bool $is_selectable_on_site
 * @property bool $is_selectable_online
 * @property bool $is_show_row_flag
 * @property string|null $row_flag_type
 * @property int $seat_qty
 * @property int $stock_qty
 * @property int $sold_qty
 * @property bool $is_metasuggest
 * @property int $qty
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Event $event
 * @property EventAddonOption[] $options
 * @property TicketItem[] $items
 * @property Registrant[] $registrants
 */
class EventTicketTier extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'event_id', 'name', 'code', 'slug', 'price', 'fee', 'description',
        'is_downloadable', 'is_show_in_list', 'is_selectable_on_site', 'is_selectable_online',
        'is_show_row_flag', 'row_flag_type', 'seat_qty', 'stock_qty', 'sold_qty', 'is_metasuggest', 'qty',
    ];

    protected $casts = [
        'id' => 'string',
        'is_downloadable' => 'boolean',
        'is_show_in_list' => 'boolean',
        'is_selectable_on_site' => 'boolean',
        'is_selectable_online' => 'boolean',
        'is_show_row_flag' => 'boolean',
        'is_metasuggest' => 'boolean',
        'seat_qty' => 'int',
        'stock_qty' => 'int',
        'sold_qty' => 'int',
        'qty' => 'int',
        'price' => 'decimal:2',
        'fee' => 'decimal:2',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(EventAddonOption::class, 'addon_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(TicketItem::class);
    }

    public function registrants(): HasMany
    {
        return $this->hasMany(Registrant::class);
    }
}
