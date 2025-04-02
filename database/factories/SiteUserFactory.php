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
        // Use static cache to track combinations across multiple factory instances
        static $generatedCombinations = null;

        // Initialize the cache with existing combinations from the database
        if ($generatedCombinations === null) {
            $generatedCombinations = [];
            $existingCombos = SiteUser::select('site_id', 'user_id')->get();

            foreach ($existingCombos as $combo) {
                $key = $combo->site_id . '-' . $combo->user_id;
                $generatedCombinations[$key] = true;
            }
        }

        $maxAttempts = 20;
        $attempts = 0;
        $siteId = null;
        $userId = null;

        do {
            $attempts++;

            // Get a random site
            $site = Site::inRandomOrder()->first();
            if (!$site) {
                $site = Site::factory()->create();
            }
            $siteId = $site->site_id;

            // After several attempts, create a new user to ensure uniqueness
            if ($attempts > 10) {
                $user = User::factory()->create([
                    'status_id' => 1,
                    'group_id' => 1,
                    'photo_release' => true,
                    'photo_consent' => true,
                    'is_admin' => false
                ]);
                $userId = $user->user_id;
            } else {
                $user = User::inRandomOrder()->first();
                if (!$user) {
                    $user = User::factory()->create([
                        'status_id' => 1,
                        'group_id' => 1,
                        'photo_release' => true,
                        'photo_consent' => true,
                        'is_admin' => false
                    ]);
                }
                $userId = $user->user_id;
            }

            // Check if this combination already exists
            $combinationKey = $siteId . '-' . $userId;
            $exists = isset($generatedCombinations[$combinationKey]);

        } while ($exists && $attempts <= $maxAttempts);

        // Mark this combination as used even before it's saved to the database
        $generatedCombinations[$siteId . '-' . $userId] = true;

        return [
            'site_id' => $siteId,
            'user_id' => $userId,
        ];
    }
}
