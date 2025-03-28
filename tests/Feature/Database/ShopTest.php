<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Shop;
use App\Models\User;
use App\Models\Quota;
use App\Models\Status;
use App\Models\Group;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;

class ShopTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\StatusSeeder::class);
        $this->seed(\Database\Seeders\GroupSeeder::class);
    }

    private function getValidShopData(array $overrides = []): array
    {
        return array_merge([
            'shop_name' => 'Test Shop',
            'short_description' => 'A short description',
            'long_description' => 'A longer description of the shop',
            'min_limit' => 100,
            'end_date' => '2024-12-31',
            'is_active' => true,
            'thumbnail' => 'https://example.com/thumbnail.jpg',
            'doc_link' => 'https://example.com/doc.pdf',
            'bc_link' => 'https://example.com/bc',
            'ha_link' => 'https://example.com/ha',
            'photo_link' => 'https://example.com/photo.jpg',
        ], $overrides);
    }

    #[Test]
    public function it_can_create_a_shop()
    {
        $shop = Shop::create($this->getValidShopData());

        $this->assertDatabaseHas('shop', [
            'shop_id' => $shop->shop_id,
            'shop_name' => 'Test Shop',
            'short_description' => 'A short description'
        ]);
    }

    #[Test]
    public function it_can_update_a_shop()
    {
        $shop = Shop::create($this->getValidShopData());

        $shop->update([
            'shop_name' => 'Updated Shop',
            'short_description' => 'Updated description'
        ]);

        $this->assertDatabaseHas('shop', [
            'shop_id' => $shop->shop_id,
            'shop_name' => 'Updated Shop',
            'short_description' => 'Updated description'
        ]);
    }

    #[Test]
    public function it_can_delete_a_shop()
    {
        $shop = Shop::create($this->getValidShopData());

        $shop->delete();

        $this->assertDatabaseMissing('shop', [
            'shop_id' => $shop->shop_id
        ]);
    }

    #[Test]
    public function it_validates_required_fields()
    {
        $this->expectException(ValidationException::class);

        Shop::create([
            'shop_name' => 'Test Shop'
            // Missing other required fields
        ]);
    }

    #[Test]
    public function it_validates_string_max_length()
    {
        $this->expectException(ValidationException::class);

        Shop::create($this->getValidShopData([
            'shop_name' => str_repeat('a', 101) // 101 characters, max is 100
        ]));
    }

    #[Test]
    public function it_validates_date_format()
    {
        $this->expectException(InvalidFormatException::class);

        Shop::create($this->getValidShopData([
            'end_date' => 'invalid-date'
        ]));
    }

    #[Test]
    public function it_can_have_nullable_fields()
    {
        $shop = Shop::create($this->getValidShopData([
            'long_description' => null,
            'min_limit' => null,
            'end_date' => null
        ]));

        $this->assertNull($shop->long_description);
        $this->assertNull($shop->min_limit);
        $this->assertNull($shop->end_date);
    }

    #[Test]
    public function it_casts_attributes_correctly()
    {
        $shop = Shop::create($this->getValidShopData());

        $this->assertInstanceOf(Carbon::class, $shop->end_date);
        $this->assertIsBool($shop->is_active);
        $this->assertIsInt($shop->min_limit);
    }

    #[Test]
    public function it_can_have_multiple_administrators()
    {
        $shop = Shop::create($this->getValidShopData());

        // Create users with valid status and group
        $users = User::factory()->count(3)->create([
            'status_id' => 1,
            'group_id' => 1,
            'photo_release' => true,
            'photo_consent' => true,
            'is_admin' => true
        ]);

        $shop->administrators()->attach($users->pluck('user_id'));

        $this->assertEquals(3, $shop->administrators()->count());
    }

    #[Test]
    public function it_can_have_multiple_products()
    {
        $shop = Shop::create($this->getValidShopData());

        // Create a quota first
        $quota = Quota::create([
            'value' => 100,
            'duration' => 30
        ]);

        // Create products associated with the shop and quota
        Product::factory()->count(3)->create([
            'shop_id' => $shop->shop_id,
            'quota_id' => $quota->quota_id
        ]);

        $this->assertEquals(3, $shop->products()->count());
    }
}
