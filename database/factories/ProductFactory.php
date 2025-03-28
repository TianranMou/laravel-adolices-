<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Quota;
use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory {
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quotaId = Quota::inRandomOrder()->first()?->quota_id ?? 1;
        $shopId = Shop::inRandomOrder()->first()?->id ?? 1;

        // Generate price first
        $price = $this->faker->randomFloat(2, 100, 1000);
        // Then generate subsidized price that's less than price
        $subsidizedPrice = $this->faker->randomFloat(2, 10, $price * 0.8);

        return [
            //'name' => $this->faker->word,
            //'description' => $this->faker->sentence, cette colonne n'existe pas
            'quota_id' => $quotaId,
            'shop_id' => $shopId,
            'withdrawal_method' => $this->faker->randomElement(['pickup', 'delivery', 'digital']),
            'product_name' => $this->faker->word,
            'price' => $price,
            'subsidized_price' => $subsidizedPrice,
            'dematerialized' => $this->faker->boolean,
        ];
    }
}
