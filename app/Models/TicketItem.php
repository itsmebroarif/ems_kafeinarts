<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $code
 * @property string $qr_code
 * @property string $barcode
 * @property bool $is_used
 * @property string $barcode_type
 * @property bool $is_printed
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Registrant $registrant
 * @property EventTicketTier $ticketTier
 */
class TicketItem extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'registrant_id', 'ticket_tier_id', 'code', 'qr_code', 'barcode', 'is_used', 'barcode_type', 'is_printed',
    ];

    protected $casts = [
        'id' => 'string',
        'is_used' => 'boolean',
        'is_printed' => 'boolean',
    ];

    public function registrant(): BelongsTo
    {
        return $this->belongsTo(Registrant::class);
    }

    public function ticketTier(): BelongsTo
    {
        return $this->belongsTo(EventTicketTier::class, 'ticket_tier_id');
    }
}
