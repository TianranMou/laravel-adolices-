<?php

namespace Database\Factories;

use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShopFactory extends Factory
{
    protected $model = Shop::class;

    public function definition(): array
    {
        return [
            'shop_name' => $this->faker->company,
            'short_description' => $this->faker->sentence,
            'long_description' => $this->faker->paragraph,
            'min_limit' => $this->faker->optional()->numberBetween(1, 1000),
            'end_date' => $this->faker->optional()->date(),
            'is_active' => $this->faker->boolean,
            'thumbnail' => $this->faker->imageUrl(),
            'doc_link' => $this->faker->url,
            'bc_link' => $this->faker->url,
            'ha_link' => $this->faker->url,
            'photo_link' => $this->faker->imageUrl(),
        ];
    }

    public function inactive(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => false,
            ];
        });
    }

    public function withEndDate(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'end_date' => $this->faker->dateTimeBetween('now', '+1 year')->format('Y-m-d'),
            ];
        });
    }
}
