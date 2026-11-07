<?php

namespace App\Models;

use App\Enums\PanitiaRole;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventCommittee extends Model
{
    protected $fillable = [
        'event_id', 'user_id', 'role', 'status', 'joined_at', 'left_at',
    ];

    protected $casts = [
        'joined_at' => 'date',
        'left_at' => 'date',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }

    public function roleLabel(): string
    {
        return PanitiaRole::label($this->role);
    }

    public function totalNetSalary(): float
    {
        return (float) $this->payrolls()->sum('net_salary');
    }
}
