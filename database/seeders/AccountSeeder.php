<?php

namespace Database\Seeders;

use App\Enums\AccountType;
use App\Models\Account;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = [
            [
                'name' => 'Cash',
                'type' => AccountType::Cash,
                'initial_balance' => 0,
                'current_balance' => 0,
                'description' => 'Uang tunai / dompet fisik',
            ],
            [
                'name' => 'Bank',
                'type' => AccountType::Bank,
                'initial_balance' => 0,
                'current_balance' => 0,
                'description' => 'Rekening bank utama',
            ],
            [
                'name' => 'E-Wallet',
                'type' => AccountType::EWallet,
                'initial_balance' => 0,
                'current_balance' => 0,
                'description' => 'Dompet digital (GoPay, OVO, Dana, dll.)',
            ],
        ];

        foreach ($accounts as $account) {
            Account::firstOrCreate(
                ['name' => $account['name']],
                $account
            );
        }
    }
}
