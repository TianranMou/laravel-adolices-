<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Status;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Status::updateOrCreate(
            ['status_id' => 1],
            ['status_label' => 'CDI Cadre de gestion']
        );
        Status::updateOrCreate(
            ['status_id' => 2],
            ['status_label' => 'CDD + 3 mois']
        );
        Status::updateOrCreate(
            ['status_id' => 3],
            ['status_label' => 'Fonctionnaire']
        );
        Status::updateOrCreate(
            ['status_id' => 4],
            ['status_label' => 'Retraité']
        );
        Status::updateOrCreate(
            ['status_id' => 5],
            ['status_label' => 'Agent mis à disposition pour une durée > 6 mois']
        );
    }
}
