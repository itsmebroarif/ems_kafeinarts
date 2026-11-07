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
 * @property string $image
 * @property string $price
 * @property string $fee
 * @property string|null $description
 * @property bool $is_percent
 * @property bool $is_single_use
 * @property bool $is_active
 * @property int $total_uses
 * @property int $uses
 * @property bool $is_auto_apply
 * @property bool $is_show_in_list
 * @property bool $is_show_on_front
 * @property bool $is_show_on_payment
 * @property Carbon|null $expired_at
 * @property int $order_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Event $event
 * @property Registrant[] $registrants
 */
class EventVoucher extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'event_id', 'code', 'is_percent', 'value', 'is_single_use', 'is_active',
        'total_uses', 'uses', 'is_auto_apply', 'is_show_in_list', 'is_show_on_front', 'is_show_on_payment',
        'expired_at', 'order_by',
    ];

    protected $casts = [
        'id' => 'string',
        'is_percent' => 'boolean',
        'is_single_use' => 'boolean',
        'is_active' => 'boolean',
        'total_uses' => 'int',
        'uses' => 'int',
        'is_auto_apply' => 'boolean',
        'is_show_in_list' => 'boolean',
        'is_show_on_front' => 'boolean',
        'is_show_on_payment' => 'boolean',
        'value' => 'decimal:2',
        'expired_at' => 'datetime',
        'order_by' => 'int',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function registrants(): HasMany
    {
        return $this->hasMany(Registrant::class);
    }
}
