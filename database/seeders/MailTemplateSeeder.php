<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('template_mail')->insert([
            [
                'subject' => 'Subject test',
                'content' => 'CONTENT TEST',
            ]
        ]);
    }
}
