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
 * @property string|null $placeholder
 * @property string|null $default_value
 * @property string|null $validation
 * @property int $order_by
 * @property bool $is_required
 * @property bool $is_mandatory
 * @property bool $is_hide_on_form
 * @property bool $is_required_to_reg
 * @property bool $is_required_to_payment
 * @property bool $is_allow_much_more
 * @property bool $is_show_in_list
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Event $event
 * @property EventCustomFieldType $fieldType
 */
class EventCustomField extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'event_id', 'field_type_id', 'name', 'code', 'slug', 'placeholder',
        'default_value', 'validation', 'order_by', 'is_required', 'is_mandatory',
        'is_hide_on_form', 'is_required_to_reg', 'is_required_to_payment',
        'is_allow_much_more', 'is_show_in_list',
    ];

    protected $casts = [
        'id' => 'string',
        'order_by' => 'int',
        'is_required' => 'boolean',
        'is_mandatory' => 'boolean',
        'is_hide_on_form' => 'boolean',
        'is_required_to_reg' => 'boolean',
        'is_required_to_payment' => 'boolean',
        'is_allow_much_more' => 'boolean',
        'is_show_in_list' => 'boolean',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function fieldType(): BelongsTo
    {
        return $this->belongsTo(EventCustomFieldType::class, 'field_type_id');
    }
}
