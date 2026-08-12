<?php

namespace Database\Factories;

use App\Enums\TransactionType;
use App\Models\Account;
use App\Models\Category;
use App\Models\Item;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date = fake()->dateTimeBetween('-1 year', 'now');
        $type = fake()->randomElement(TransactionType::cases());

        return [
            'user_id' => User::factory(),
            'account_id' => Account::factory(),
            'target_account_id' => $type === TransactionType::Transfer ? Account::factory() : null,
            'category_id' => Category::factory(),
            'item_id' => Item::factory(),
            'target_item_id' => null,
            'type' => $type,
            'amount' => fake()->randomFloat(2, 10000, 1000000),
            'transaction_date' => $date->format('Y-m-d'),
            'description' => fake()->sentence(),
        ];
    }
}
