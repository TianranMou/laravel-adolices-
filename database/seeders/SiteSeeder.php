<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class SiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('site')->insert([
            ['label_site' => 'Douai Bourseul'],
            ['label_site'=>'Douai Lahure'],
            ['label_site'=>'Douai MDE'],
            ['label_site'=>'Villeneuve d\'Ascq'],
            ['label_site'=>'Dunkerque']  
            ]   
        );

    }
}
