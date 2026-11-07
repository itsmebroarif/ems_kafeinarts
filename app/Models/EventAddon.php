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
 * @property string $name
 * @property string $code
 * @property string $slug
 * @property string $description
 * @property string $image
 * @property string $price
 * @property string $fee
 * @property string|null $description
 * @property bool $is_new
 * @property bool $is_show_in_list
 * @property bool $is_allow_buying_stack
 * @property int $qty
 * @property int $max_qty
 * @property int $sold_qty
 * @property bool $is_selectable_on_site
 * @property bool $is_selectable_online
 * @property bool $is_metasuggest
 * @property int $buy_qty_step
 * @property int $show_qty
 * @property bool $is_displayed
 * @property bool $is_displayed_on_backend
 * @property string $display_type
 * @property bool $is_fit_disable
 * @property int $order_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Event $event
 * @property EventAddonOption[] $options
 * @property RegistrantAddon[] $registrantAddons
 */
class EventAddon extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'event_id', 'name', 'code', 'slug', 'image', 'price', 'fee', 'description',
        'is_new', 'is_show_in_list', 'is_allow_buying_stack', 'qty', 'max_qty',
        'sold_qty', 'is_selectable_on_site', 'is_selectable_online', 'is_metasuggest',
        'buy_qty_step', 'show_qty', 'is_displayed', 'is_displayed_on_backend',
        'display_type', 'is_fit_disable', 'order_by',
    ];

    protected $casts = [
        'id' => 'string',
        'is_new' => 'boolean',
        'is_show_in_list' => 'boolean',
        'is_allow_buying_stack' => 'boolean',
        'qty' => 'int',
        'max_qty' => 'int',
        'sold_qty' => 'int',
        'is_selectable_on_site' => 'boolean',
        'is_selectable_online' => 'boolean',
        'is_metasuggest' => 'boolean',
        'buy_qty_step' => 'int',
        'show_qty' => 'int',
        'is_displayed' => 'boolean',
        'is_displayed_on_backend' => 'boolean',
        'is_fit_disable' => 'boolean',
        'order_by' => 'int',
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

    public function registrantAddons(): HasMany
    {
        return $this->hasMany(RegistrantAddon::class);
    }
}
