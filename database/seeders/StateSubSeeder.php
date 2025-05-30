<?php

namespace Database\Seeders;

use App\Models\StateSub;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StateSubSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $labelState=["En cours","Refusée","Payée"];

        foreach ($labelState as $label) {
            StateSub::factory()->create([
            'label_state' => $label,
            ]);
        }
    }
}
