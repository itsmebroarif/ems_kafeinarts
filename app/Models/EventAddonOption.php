<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string $slug
 * @property string $price
 * @property string $fee
 * @property bool $is_free
 * @property bool $is_highlight
 * @property int $order_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property EventAddon $addon
 */
class EventAddonOption extends Model
{
    use HasFactory, HasUuids;

    public $table = 'addon_options';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'addon_id', 'name', 'code', 'slug', 'price', 'fee', 'is_free', 'is_highlight', 'order_by',
    ];

    protected $casts = [
        'id' => 'string',
        'is_free' => 'boolean',
        'is_highlight' => 'boolean',
        'order_by' => 'int',
        'price' => 'decimal:2',
        'fee' => 'decimal:2',
    ];

    public function addon(): BelongsTo
    {
        return $this->belongsTo(EventAddon::class, 'addon_id');
    }
}
