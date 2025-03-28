<?php

namespace Database\Factories;

use App\Models\Subvention;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubventionFactory extends Factory
{
    protected $model = Subvention::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'state_id' => \App\Models\StateSub::pluck('state_id')->random(),
            'name_asso' => $this->faker->company,
            'RIB' => $this->faker->bankAccountNumber,
            'montant' => $this->faker->randomFloat(2, 1000, 100000),
            'link_attestation' => null,
            'motif_refus' => null,
            'payment_subvention' => null,
        ];
    }
}
