<?php

namespace Database\Factories;

use App\Models\SiteUser;
use App\Models\Site;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SiteUserFactory extends Factory
{
    protected $model = SiteUser::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'site_id' => Site::factory(),
            'user_id' => User::inRandomOrder()->first()->user_id ?? User::factory()->create([
                'status_id' => 1,
                'group_id' => 1,
                'photo_release' => true,
                'photo_consent' => true,
                'is_admin' => false
            ])->user_id,
        ];
    }
}
