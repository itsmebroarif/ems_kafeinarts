<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $registrant_id
 * @property string $addon_id
 * @property int $qty
 * @property string $price
 * @property string $fee
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Registrant $registrant
 * @property EventAddon $addon
 */
class RegistrantAddon extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'registrant_id', 'addon_id', 'qty', 'price', 'fee',
    ];

    protected $casts = [
        'id' => 'string',
        'registrant_id' => 'string',
        'addon_id' => 'string',
        'qty' => 'int',
        'price' => 'decimal:2',
        'fee' => 'decimal:2',
    ];

    public function registrant(): BelongsTo
    {
        return $this->belongsTo(Registrant::class);
    }

    public function addon(): BelongsTo
    {
        return $this->belongsTo(EventAddon::class, 'addon_id');
    }
}
