<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Quota;

class QuotaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        //Factory
        //Quota::factory(25)->create();

        ///*
        $quotas = [
            [
                'value' => 100,
                'duration' => 12, // 12 months
            ],
            [
                'value' => 50,
                'duration' => 6, // 6 months
            ],
            [
                'value' => 25,
                'duration' => 3, // 3 months
            ],
            [
                'value' => 200,
                'duration' => 12, // 12 months
            ],
            [
                'value' => 150,
                'duration' => 9, // 9 months
            ],
        ];

        foreach ($quotas as $quota) {
            DB::table('quota')->insert($quota);
        }
        //*/
    }
}
