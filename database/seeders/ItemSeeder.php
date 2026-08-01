<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Database\Seeder;

final class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::query()->get();

        if ($categories->isEmpty()) {
            $categories = Category::factory()->count(5)->create();
        }

        foreach (range(1, 25) as $i) {
            Item::factory()->create([
                'category_id' => $categories->random()->id,
            ]);
        }
    }
}
