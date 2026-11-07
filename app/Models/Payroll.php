<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payroll extends Model
{
    protected $fillable = [
        'event_committee_id', 'base_salary', 'allowance', 'overtime', 'deduction',
        'net_salary', 'period_start', 'period_end', 'status', 'paid_at', 'notes',
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
        'allowance' => 'decimal:2',
        'overtime' => 'decimal:2',
        'deduction' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'period_start' => 'date',
        'period_end' => 'date',
        'paid_at' => 'datetime',
    ];

    public function committee(): BelongsTo
    {
        return $this->belongsTo(EventCommittee::class, 'event_committee_id');
    }

    /**
     * Hitung gaji bersih: base + tunjangan + lembur - potongan.
     */
    public static function computeNet(float $base, float $allowance, float $overtime, float $deduction): float
    {
        return round($base + $allowance + $overtime - $deduction, 2);
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }
}
