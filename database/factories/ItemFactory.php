<?php

namespace Database\Factories;

use App\Enums\ItemPriority;
use App\Enums\ItemStatus;
use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Item>
 */
class ItemFactory extends Factory
{
    protected $model = Item::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $targetAmount = fake()->randomFloat(2, 100000, 10000000);

        return [
            'category_id' => Category::factory(),
            'user_id' => User::factory(),
            'name' => ucfirst(fake()->words(2, true)),
            'target_amount' => $targetAmount,
            'current_amount' => 0,
            'target_date' => fake()->optional()->dateTimeBetween('now', '+1 year'),
            'priority' => fake()->randomElement(ItemPriority::cases()),
            'status' => ItemStatus::Belum,
            'note' => fake()->optional()->sentence(),
        ];
    }
}
