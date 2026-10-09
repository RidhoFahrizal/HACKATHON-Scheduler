<?php

namespace App\Domain\Scheduling\Enums;

enum DayOfWeek: int
{
    case SENIN = 0;
    case SELASA = 1;
    case RABU = 2;
    case KAMIS = 3;
    case JUMAT = 4;
    case SABTU = 5;
    case MINGGU = 6;

    public function label(): string
    {
        return match($this) {
            self::SENIN => 'Senin',
            self::SELASA => 'Selasa',
            self::RABU => 'Rabu',
            self::KAMIS => 'Kamis',
            self::JUMAT => 'Jumat',
            self::SABTU => 'Sabtu',
            self::MINGGU => 'Minggu',
        };
    }

    public function isFriday(): bool
    {
        return $this === self::JUMAT;
    }
}
