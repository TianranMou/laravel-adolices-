<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\StateSub;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StateSub>
 */
class StateSubFactory extends Factory
{

    protected $model = StateSub::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'label_state' => 'En Cours',
        ];
    }
}
