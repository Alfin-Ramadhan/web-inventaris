<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

final class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Laptop',
            'UPS',
            'Monitor',
            'Keyboard',
            'Mouse',
            'Printer',
            'Proyektor',
        ];

        foreach ($categories as $categoryName) {
            Category::query()->firstOrCreate(['name' => $categoryName]);
        }
    }
}
