<?php

namespace App\Enums;

enum AccountType: string
{
    case Cash = 'cash';
    case Bank = 'bank';
    case EWallet = 'ewallet';
    case Savings = 'savings';

    public function label(): string
    {
        return match ($this) {
            self::Cash => 'Cash / Tunai',
            self::Bank => 'Bank',
            self::EWallet => 'E-Wallet',
            self::Savings => 'Savings / Tabungan',
        };
    }
}
