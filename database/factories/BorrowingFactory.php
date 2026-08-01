<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Borrowing;
use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Borrowing>
 */
final class BorrowingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'item_id' => Item::factory(),
            'borrowed_at' => fake()->dateTimeBetween('-1 month', 'now'),
            'due_at' => fake()->dateTimeBetween('now', '+1 month'),
            'returned_at' => fake()->boolean(50) ? fake()->dateTimeBetween('-1 week', 'now') : null,
        ];
    }
}
