<?php

namespace App\Enums;

enum ItemStatus: string
{
    case Belum = 'belum';
    case Proses = 'proses';
    case Terpenuhi = 'terpenuhi';

    public function label(): string
    {
        return match ($this) {
            self::Belum => 'Belum',
            self::Proses => 'Proses',
            self::Terpenuhi => 'Terpenuhi',
        };
    }
}
