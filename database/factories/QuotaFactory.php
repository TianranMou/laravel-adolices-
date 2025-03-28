<?php

namespace Database\Factories;

use App\Models\Quota;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuotaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Quota::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'quota_id' => $this->faker->unique()->randomNumber(),
            'value' => $this->faker->numberBetween(10, 100),
            'duration' => $this->faker->numberBetween(1, 12),
        ];
    }
}