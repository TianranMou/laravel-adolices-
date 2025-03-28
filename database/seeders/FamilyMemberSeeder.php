<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FamilyMember;

class FamilyMemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        FamilyMember::factory()->count(10)->create();
    }
}
