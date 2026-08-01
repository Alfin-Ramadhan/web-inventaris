<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

final class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Alfin',
            'email' => 'alfin@mail.com',
            'role' => UserRole::Administrator,
        ]);

        User::factory()->create([
            'name' => 'Budi',
            'email' => 'budi@mail.com',
            'role' => UserRole::Employee,
        ]);

        User::factory()->count(10)->create();

        User::factory()->count(3)->unverified()->create();
    }
}
