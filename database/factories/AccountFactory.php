<?php

namespace Database\Factories;

use App\Enums\AccountType;
use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Account>
 */
class AccountFactory extends Factory
{
    protected $model = Account::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $initialBalance = fake()->randomFloat(2, 0, 5000000);

        return [
            'user_id' => User::factory(),
            'name' => ucfirst(fake()->word()).' Account',
            'type' => fake()->randomElement(AccountType::cases()),
            'initial_balance' => $initialBalance,
            'current_balance' => $initialBalance,
            'account_number' => fake()->optional()->numerify('##########'),
            'description' => fake()->optional()->sentence(),
            'is_active' => true,
        ];
    }
}
