<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Category>
 */
final class CategoryFactory extends Factory
{
    public function definition(): array
    {
        $categories = [
            'Laptop',
            'UPS',
            'Monitor',
            'Keyboard',
            'Mouse',
            'Printer',
            'Proyektor',
            'Scanner',
            'Webcam',
            'Headset',
        ];

        return [
            'name' => fake()->unique()->randomElement($categories),
        ];
    }
}
