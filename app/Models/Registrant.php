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
 * @property string $full_name
 * @property string $email
 * @property string|null $phone
 * @property string|null $nik
 * @property string|null $nationality
 * @property string|null $city
 * @property string|null $district
 * @property string|null $address
 * @property string|null $postal_code
 * @property string|null $province
 * @property string|null $village
 * @property string|null $guests_quantity
 * @property string|null $registrant_type
 * @property string|null $guests_quantity_other
 * @property string $status
 * @property string|null $session_key
 * @property string|null $participant_token
 * @property string|null $notes
 * @property string|null $custom_fields
 * @property string $subtotal
 * @property string $addons_total
 * @property string $discount
 * @property string $total
 * @property string $fee
 * @property bool $is_paid
 * @property bool $is_confirmed
 * @property bool $is_rejected
 * @property bool $is_pending
 * @property bool $is_deleted
 * @property bool $is_referred
 * @property bool $is_hide
 * @property bool $is_approved
 * @property string|null $shipping_method
 * @property Carbon|null $paid_at
 * @property string|null $paid_by
 * @property string|null $exchange_id
 * @property string|null $ip_address
 * @property string|null $device_info
 * @property string|null $device_type
 * @property string|null $os_version
 * @property string|null $browser
 * @property string|null $browser_version
 * @property string|null $midtrans_order_id
 * @property string|null $midtrans_snap_token
 * @property string|null $midtrans_snap_redirect_url
 * @property string|null $midtrans_status
 * @property string|null $locale
 * @property bool $is_wallet_payment
 * @property string|null $employee_id
 * @property bool $is_remove
 * @property string|null $to_payment_url
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Event $event
 * @property EventTicketTier $ticketTier
 * @property EventVoucher|null $voucher
 * @property RegistrantAddon[] $addons
 * @property TicketItem[] $ticketItems
 */
class Registrant extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'user_id', 'full_name', 'email', 'phone', 'nationality', 'city', 'district', 'address', 'postal_code',
        'nik', 'province', 'village',
        'guests_quantity', 'registrant_type', 'guests_quantity_other', 'event_id', 'ticket_tier_id', 'voucher_id',
        'status', 'notes', 'custom_fields', 'subtotal', 'addons_total', 'discount', 'total', 'fee',
        'session_key', 'participant_token',
        'is_paid', 'is_confirmed', 'is_rejected', 'is_pending', 'is_deleted', 'is_referred', 'is_hide', 'is_approved',
        'shipping_method', 'paid_at', 'paid_by', 'exchange_id', 'ip_address', 'device_info', 'device_type',
        'os_version', 'browser', 'browser_version', 'locale', 'is_wallet_payment', 'employee_id', 'is_remove',
        'to_payment_url', 'deleted_at', 'midtrans_order_id', 'midtrans_snap_token', 'midtrans_snap_redirect_url',
        'midtrans_status',
    ];

    protected $casts = [
        'id' => 'string',
        'user_id' => 'int',
        'is_paid' => 'boolean',
        'is_confirmed' => 'boolean',
        'is_rejected' => 'boolean',
        'is_pending' => 'boolean',
        'is_deleted' => 'boolean',
        'is_referred' => 'boolean',
        'is_hide' => 'boolean',
        'is_approved' => 'boolean',
        'is_wallet_payment' => 'boolean',
        'is_remove' => 'boolean',
        'subtotal' => 'decimal:2',
        'addons_total' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'fee' => 'decimal:2',
        'paid_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function ticketTier(): BelongsTo
    {
        return $this->belongsTo(EventTicketTier::class, 'ticket_tier_id');
    }

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(EventVoucher::class);
    }

    public function addons(): HasMany
    {
        return $this->hasMany(RegistrantAddon::class);
    }

    public function ticketItems(): HasMany
    {
        return $this->hasMany(TicketItem::class);
    }
}
