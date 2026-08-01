<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Item>
 */
final class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $officeItems = [
            'Laptop Dell Latitude',
            'Laptop MacBook Air',
            'Monitor Dell 24 Inch',
            'Keyboard Logitech K120',
            'Mouse Wireless HP',
            'Kursi Kantor Ergonomis',
            'Meja Kerja Minimalis',
            'Proyektor Epson',
            'Kabel HDMI 3 Meter',
            'Printer Canon Pixma',
            'Scanner Brother',
            'Webcam Logitech C922',
            'Headset Jabra',
            'Whiteboard Magnetik',
            'Spidol Boardmarker Snowman',
            'Penghapus Whiteboard',
            'Stapler Kangaro',
            'Gunting Kantor',
            'Penggaris Besi 30cm',
            'Map Dokumen Plastik',
            'Paper Shredder',
            'Dispenser Air Minum',
            'Mesin Fotokopi Sharp',
            'Router TP-Link',
            'UPS APC 650VA',
        ];

        $createdAt = fake()->dateTimeBetween('-1 year', 'now');

        return [
            'category_id' => Category::factory(),
            'name' => fake()->unique()->randomElement($officeItems),
            'inventory_number' => 'INV-'.fake()->unique()->bothify('??-#####'),
            'quantity' => 1,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ];
    }
}
