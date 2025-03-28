<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('shop')->insert([
            [
                'shop_name' => 'Shop 1',
                'short_description' => 'Short description for Shop 1',
                'long_description' => 'Long description for Shop 1',
                'min_limit' => 10,
                'end_date' => '2025-12-31',
                'is_active' => true,
                'thumbnail' => 'thumbnail1.jpg',
                'doc_link' => 'http://example.com/doc1',
                'bc_link' => 'http://example.com/bc1',
                'ha_link' => 'http://example.com/ha1',
                'photo_link' => 'http://example.com/photo1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'shop_name' => 'Shop 2',
                'short_description' => 'Short description for Shop 2',
                'long_description' => 'Long description for Shop 2',
                'min_limit' => 5,
                'end_date' => '2025-12-31',
                'is_active' => true,
                'thumbnail' => 'thumbnail2.jpg',
                'doc_link' => 'http://example.com/doc2',
                'bc_link' => 'http://example.com/bc2',
                'ha_link' => 'http://example.com/ha2',
                'photo_link' => 'http://example.com/photo2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'shop_name' => 'Shop 3',
                'short_description' => 'Short description for Shop 3',
                'long_description' => 'Long description for Shop 3',
                'min_limit' => 15,
                'end_date' => '2025-12-31',
                'is_active' => true,
                'thumbnail' => 'thumbnail3.jpg',
                'doc_link' => 'http://example.com/doc3',
                'bc_link' => 'http://example.com/bc3',
                'ha_link' => 'http://example.com/ha3',
                'photo_link' => 'http://example.com/photo3',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
