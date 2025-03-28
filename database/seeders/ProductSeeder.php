<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Factory
        //Product::factory(25)->create();
        ///*
        $faker = Faker::create();

        $quotaIds = DB::table('quota')->pluck('quota_id')->toArray();
        $shopIds = DB::table('shop')->pluck('shop_id')->toArray();

        if (empty($quotaIds) || empty($shopIds)) {
            return;
        }


        for ($i = 0; $i < 20; $i++) {
            $name = $faker->words(2, true);
            $withdrawalMethods = ['pickup', 'delivery', 'digital'];
            $price = $faker->randomFloat(2, 10, 500);

            DB::table('product')->insert([
                'product_name' => $name,
                'price' => $price,
                'quota_id' => $faker->randomElement($quotaIds),
                'shop_id' => $faker->randomElement($shopIds),
                'withdrawal_method' => $faker->randomElement($withdrawalMethods),
                'subsidized_price' => $faker->randomFloat(2, 5, $price * 0.7),
                'dematerialized' => $faker->boolean(30),
            ]);
        }

        $specialProducts = [
            [
                'product_name' => 'Concert Tickets',
                'price' => 89.99,
                'withdrawal_method' => 'digital',
                'subsidized_price' => 45.00,
                'dematerialized' => true,
            ],
            [
                'price' => 99.99,
                'withdrawal_method' => 'pickup',
                'product_name' => 'Cinema Pass',
                'subsidized_price' => 50.00,
                'dematerialized' => false,
            ],
            [
                'price' => 120.00,
                'withdrawal_method' => 'digital',
                'product_name' => 'Theme Park Day Pass',
                'subsidized_price' => 60.00,
                'dematerialized' => true,
            ],
            [
                'price' => 199.99,
                'withdrawal_method' => 'pickup',
                'product_name' => 'Spa Package',
                'subsidized_price' => 99.99,
                'dematerialized' => false,
            ],
            [
                'price' => 149.99,
                'withdrawal_method' => 'delivery',
                'product_name' => 'Museum Annual Pass',
                'subsidized_price' => 75.00,
                'dematerialized' => false,
            ],
        ];

        foreach ($specialProducts as $product) {
            DB::table('product')->insert([
                'price' => $product['price'],
                'quota_id' => $faker->randomElement($quotaIds),
                'shop_id' => $faker->randomElement($shopIds),
                'withdrawal_method' => $product['withdrawal_method'],
                'product_name' => $product['product_name'],
                'subsidized_price' => $product['subsidized_price'],
                'dematerialized' => $product['dematerialized'],
            ]);
        }
        //*/
    }
}
