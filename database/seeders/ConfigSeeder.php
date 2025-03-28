<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('config')->insert([
            ['config_label' => 'lien adhésion', 'config_value' => 'test'],
            ['config_label' => 'condition adhésion', 'config_value' => 'test'],
            ['config_label' => 'message acceuil', 'config_value' => 'test'],
            ['config_label' => 'limite min tickets', 'config_value' => 'test'],
            ['config_label' => 'attestation vierge', 'config_value' => 'test'],
        ]);
    }
}
