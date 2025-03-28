<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Relation;

class FamilyRelationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('family_relation')->insert([
            'relation_id' => 1,
            'relation_label' => 'child',
        ]);
        DB::table('family_relation')->insert([
            'relation_id' => 2,
            'relation_label' => 'spouse',
        ]);
    }
}
