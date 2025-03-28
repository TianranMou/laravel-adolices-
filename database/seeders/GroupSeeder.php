<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Group;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Group::updateOrCreate(
            ['group_id' => 1],
            ['label_group' => 'AG.Compt']
        );
        Group::updateOrCreate(
            ['group_id' => 2],
            ['label_group' => 'CERI EE']
        );
        Group::updateOrCreate(
            ['group_id' => 3],
            ['label_group' => 'CERI MP']
        );
        Group::updateOrCreate(
            ['group_id' => 4],
            ['label_group' => 'CERI SN']
        );
        Group::updateOrCreate(
            ['group_id' => 5],
            ['label_group' => 'DAF']
        );
        Group::updateOrCreate(
            ['group_id' => 6],
            ['label_group' => 'DCP']
        );
        Group::updateOrCreate(
            ['group_id' => 7],
            ['label_group' => 'DE-FS']
        );
        Group::updateOrCreate(
            ['group_id' => 8],
            ['label_group' => 'DE-MI']
        );
        Group::updateOrCreate(
            ['group_id' => 9],
            ['label_group' => 'DISI']
        );
        Group::updateOrCreate(
            ['group_id' => 10],
            ['label_group' => 'DMG']
        );
        Group::updateOrCreate(
            ['group_id' => 11],
            ['label_group' => 'DP']
        );
        Group::updateOrCreate(
            ['group_id' => 12],
            ['label_group' => 'DRH']
        );
        Group::updateOrCreate(
            ['group_id' => 13],
            ['label_group' => 'DRI']
        );
        Group::updateOrCreate(
            ['group_id' => 14],
            ['label_group' => 'DRIPA']
        );
        Group::updateOrCreate(
            ['group_id' => 15],
            ['label_group' => 'Aucun']
        );
    }
}
