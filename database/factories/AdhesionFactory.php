<?php

namespace Database\Factories;

use App\Models\Adhesion;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdhesionFactory extends Factory
{
    protected $model = Adhesion::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'date_adhesion' => $this->faker->date,
        ];
    }
}
