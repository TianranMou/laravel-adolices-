<?php

namespace Database\Factories;

use App\Models\FamilyMember;
use Illuminate\Database\Eloquent\Factories\Factory;

class FamilyMemberFactory extends Factory
{
    protected $model = FamilyMember::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'name_member' => $this->faker->lastName,
            'birth_date_member' => $this->faker->date,
            'first_name_member' => $this->faker->firstName,
            'relation' => $this->faker->word,
        ];
    }
}
