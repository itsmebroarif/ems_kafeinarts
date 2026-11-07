<?php

namespace App\Enums;

/**
 * Status kolom pada papan (kanban) event.
 */
class EventBoardStatus
{
    public const REGISTERED = 'registered';

    public const STARTING = 'starting';

    public const CLOSED = 'closed';

    public const RESULT = 'result';

    /**
     * @var array<int, string>
     */
    public const ALL = [
        self::REGISTERED,
        self::STARTING,
        self::CLOSED,
        self::RESULT,
    ];

    /**
     * Definisi tiap kolom papan: label, ikon, warna, dan keterangan.
     *
     * @return array<string, array{label: string, icon: string, color: string, hint: string}>
     */
    public static function columns(): array
    {
        return [
            self::REGISTERED => [
                'label' => 'Event Terdaftar',
                'icon' => 'bi-journal-check',
                'color' => 'primary',
                'hint' => 'Event yang sudah dibuat dan menunggu pelaksanaan.',
            ],
            self::STARTING => [
                'label' => 'Event Akan Dimulai',
                'icon' => 'bi-hourglass-split',
                'color' => 'warning',
                'hint' => 'Event yang sudah mendekati atau sedang berlangsung.',
            ],
            self::CLOSED => [
                'label' => 'Event Ditutup',
                'icon' => 'bi-calendar-check',
                'color' => 'secondary',
                'hint' => 'Event yang sudah selesai dan ditutup.',
            ],
            self::RESULT => [
                'label' => 'Result',
                'icon' => 'bi-trophy',
                'color' => 'success',
                'hint' => 'Khusus event sport/esport yang sudah memiliki hasil.',
            ],
        ];
    }

    public static function label(string $status): string
    {
        return self::columns()[$status]['label'] ?? $status;
    }

    public static function isValid(string $status): bool
    {
        return in_array($status, self::ALL, true);
    }
}
