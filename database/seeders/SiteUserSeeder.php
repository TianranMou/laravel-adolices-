<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SiteUser;

class SiteUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SiteUser::factory()->count(10)->create();
    }
}
