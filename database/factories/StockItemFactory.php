<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StockItem>
 */
class StockItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $kondisi = $this->faker->randomElement(['Baru', 'Lama']);
        $lokasi = $this->faker->randomElement(['Warehouse 1', 'Warehouse 2', 'Warehouse 4']);
        return [
            'nama' => $this->faker->words(3, true),
            'sku' => $this->faker->bothify('???####'),
            'kondisi' => $kondisi,
            'lokasi' => $lokasi,
            'tersedia' => $this->faker->numberBetween(1000, 4000),
            'disimpan' => $this->faker->numberBetween(1000, 4000),
            'harga' => $this->faker->numberBetween(5000, 50000),
            'diperbaharui' => $this->faker->dateTimeBetween('-3 years', 'now')->format('Y-m-d'),
            'gambar' => $this->faker->imageUrl(100, 100, 'food', true),
        ];
    }
}
