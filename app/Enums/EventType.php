<?php

namespace App\Enums;

/**
 * Jenis event saat pendaftaran: kompetisi atau event hiburan.
 */
class EventType
{
    public const KOMPETISI = 'kompetisi';

    public const HIBURAN = 'hiburan';

    /**
     * @var array<int, string>
     */
    public const ALL = [
        self::KOMPETISI,
        self::HIBURAN,
    ];

    /**
     * Definisi tiap jenis event: label, ikon, warna, dan keterangan.
     *
     * @return array<string, array{label: string, icon: string, color: string, hint: string}>
     */
    public static function options(): array
    {
        return [
            self::KOMPETISI => [
                'label' => 'Kompetisi',
                'icon' => 'bi-trophy',
                'color' => 'danger',
                'hint' => 'Event adu prestasi: turnamen, lomba, esport, atau seleksi dengan pemenang.',
            ],
            self::HIBURAN => [
                'label' => 'Event Hiburan',
                'icon' => 'bi-stars',
                'color' => 'info',
                'hint' => 'Event hiburan: konser, festival, pameran, atau gathering tanpa kompetisi.',
            ],
        ];
    }

    public static function label(string $type): string
    {
        return self::options()[$type]['label'] ?? $type;
    }

    public static function isValid(string $type): bool
    {
        return in_array($type, self::ALL, true);
    }
}
