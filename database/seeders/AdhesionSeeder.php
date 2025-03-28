<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use App\Models\Adhesion;

class AdhesionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        //Factory
        //Adhesion::factory(25)->create();

        ///*
        $faker = Faker::create();
        
        $userIds = DB::table('users')->pluck('user_id')->toArray();
        
        if (empty($userIds)) {
            return;
        }

        
        
        foreach ($userIds as $userId) {
            DB::table('adhesion')->insert([
                'user_id' => $userId,
                'date_adhesion' => $faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
            ]);
        }
        //*/
    }
}