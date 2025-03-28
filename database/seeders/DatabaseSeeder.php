<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\Product;
use App\Models\Site;
use App\Models\StateSub;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use PSpell\Config;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call([
            StatusSeeder::class,
            GroupSeeder::class,
            ConfigSeeder::class,
            QuotaSeeder::class,
            ShopSeeder::class,
            MailTemplateSeeder::class,
            StateSubSeeder::class,
            SiteSeeder::class,

            UserSeeder::class,
            SiteUserSeeder::class,
            AdhesionSeeder::class,
            AdministratorSeeder::class,
            ProductSeeder::class,

            SubventionSeeder::class,
            TicketSeeder::class,

            FamilyRelationSeeder::class,
            FamilyMemberSeeder::class,
        ]);
    }
}
