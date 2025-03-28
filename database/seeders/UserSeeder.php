<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Factory
        //User::factory()->count(50)->create();

        ///*
        $faker = Faker::create();

        // Create one admin user if it doesn't exist
        if (!DB::table('users')->where('email', 'admin@example.com')->exists()) {
            DB::table('users')->insert([
                'status_id' => 1,
                'group_id' => 1,
                'last_name' => $faker->lastName(),
                'first_name' => $faker->firstName(),
                'email' => 'admin@example.com',
                'email_imt' => 'admin@imt.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'phone_number' => $faker->phoneNumber(),
                'photo_release' => true,
                'photo_consent' => true,
                'is_admin' => true,
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        // test admin
        if (!DB::table('users')->where('email', 'testAdmin@example.com')->exists()) {
            DB::table('users')->insert([
                'status_id' => 1,
                'group_id' => 1,
                'last_name' => "testAdmin",
                'first_name' => "testAdmin",
                'email' => 'testAdmin@example.com',
                'email_imt' => 'testAdmin@imt.com',
                'email_verified_at' => now(),
                'password' => Hash::make('testAdmin'),
                'phone_number' => $faker->phoneNumber(),
                'photo_release' => true,
                'photo_consent' => true,
                'is_admin' => true,
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        // standard user test
        if (!DB::table('users')->where('email', 'testUser@example.com')->exists()) {
            DB::table('users')->insert([
                'status_id' => 1,
                'group_id' => 1,
                'last_name' => "testUser",
                'first_name' => "testUser",
                'email' => 'testUser@example.com',
                'email_imt' => 'testUser@imt.com',
                'email_verified_at' => now(),
                'password' => Hash::make('testUser'),
                'phone_number' => $faker->phoneNumber(),
                'photo_release' => true,
                'photo_consent' => true,
                'is_admin' => false,
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Create 10 regular users
        for ($i = 0; $i < 10; $i++) {
            $firstName = $faker->firstName();
            $lastName = $faker->lastName();

            DB::table('users')->insert([
                'status_id' => $faker->numberBetween(1, 3),
                'group_id' => $faker->numberBetween(1, 5),
                'last_name' => $lastName,
                'first_name' => $firstName,
                'email' => $faker->unique()->safeEmail(),
                'email_imt' => strtolower($firstName) . '.' . strtolower($lastName) . '@imt.com',
                'email_verified_at' => $faker->randomElement([now(), null]),
                'password' => Hash::make('password'),
                'phone_number' => $faker->phoneNumber(),
                'photo_release' => $faker->boolean(),
                'photo_consent' => $faker->boolean(),
                'is_admin' => false,
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        }
        //*/
    }
}
