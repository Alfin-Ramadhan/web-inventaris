<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Borrowing;
use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Seeder;

final class BorrowingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Borrowing::factory()
            ->count(10)
            ->recycle(User::all())
            ->recycle(Item::all())
            ->create();
    }
}
