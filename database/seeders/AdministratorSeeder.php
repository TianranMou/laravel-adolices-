<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class AdministratorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $shopId = DB::table('shop')->first()->shop_id ?? null;

        if (!$shopId) {
            return;
        }

        $adminUserIds = DB::table('users')
            ->where('is_admin', true)
            ->pluck('user_id')
            ->toArray();

        if (empty($adminUserIds)) {
            return;
        }

        foreach ($adminUserIds as $userId) {
            // Check if the administrator already exists
            $exists = DB::table('administrator')
                ->where('shop_id', $shopId)
                ->where('user_id', $userId)
                ->exists();

            if (!$exists) {
                DB::table('administrator')->insert([
                    'shop_id' => $shopId,
                    'user_id' => $userId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
