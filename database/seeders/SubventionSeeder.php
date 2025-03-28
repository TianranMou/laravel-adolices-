<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subvention;

class SubventionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Subvention::factory()->count(10)->create();
    }
}
