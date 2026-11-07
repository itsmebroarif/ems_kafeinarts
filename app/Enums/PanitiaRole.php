<?php

namespace App\Enums;

/**
 * Role kepanitiaan pada sebuah event.
 */
class PanitiaRole
{
    public const KETUA = 'ketua';

    public const WAKIL_KETUA = 'wakil_ketua';

    public const SEKRETARIS = 'sekretaris';

    public const BENDAHARA = 'bendahara';

    public const KOORDINATOR = 'koordinator';

    public const STAFF = 'staff';

    public const VOLUNTEER = 'volunteer';

    /**
     * @var array<int, string>
     */
    public const ALL = [
        self::KETUA,
        self::WAKIL_KETUA,
        self::SEKRETARIS,
        self::BENDAHARA,
        self::KOORDINATOR,
        self::STAFF,
        self::VOLUNTEER,
    ];

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return [
            self::KETUA => 'Ketua Panitia',
            self::WAKIL_KETUA => 'Wakil Ketua',
            self::SEKRETARIS => 'Sekretaris',
            self::BENDAHARA => 'Bendahara',
            self::KOORDINATOR => 'Koordinator',
            self::STAFF => 'Staff',
            self::VOLUNTEER => 'Sukarelawan',
        ];
    }

    public static function label(string $role): string
    {
        return self::options()[$role] ?? $role;
    }

    public static function isValid(string $role): bool
    {
        return in_array($role, self::ALL, true);
    }
}
