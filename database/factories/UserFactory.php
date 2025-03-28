<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $firstName = $this->faker->firstName();
        $lastName = $this->faker->lastName();

        return [
            'status_id' => $this->faker->numberBetween(1, 5),
            'group_id' => $this->faker->numberBetween(1, 14),
            'last_name' => $lastName,
            'first_name' => $firstName,
            'email' => $this->faker->unique()->safeEmail(),
            'email_imt' => $firstName . '.' . $lastName . '@imt.com',
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'phone_number' => $this->faker->phoneNumber(),
            'photo_release' => $this->faker->boolean(),
            'photo_consent' => $this->faker->boolean(),
            'is_admin' => false,
            'remember_token' => Str::random(10),
        ];
    }
}
