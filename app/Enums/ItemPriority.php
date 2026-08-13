<?php

namespace App\Enums;

enum ItemPriority: string
{
    case Wajib = 'wajib';
    case RutinBulanan = 'rutin_bulanan';
    case KeinginanShortterm = 'keinginan_shortterm';
    case Emergency = 'emergency';

    public function label(): string
    {
        return match ($this) {
            self::Wajib => 'Wajib',
            self::RutinBulanan => 'Rutin Bulanan',
            self::KeinginanShortterm => 'Keinginan Short-term',
            self::Emergency => 'Emergency / Dana Darurat',
        };
    }
}
