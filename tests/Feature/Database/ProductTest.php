<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Quota;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Site;
use App\Models\Status;
use App\Models\Group;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Validation\ValidationException;

class ProductTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\StatusSeeder::class);
        $this->seed(\Database\Seeders\GroupSeeder::class);
    }

    private function getValidProductData(Shop $shop, Quota $quota, array $overrides = []): array
    {
        return array_merge([
            'quota_id' => $quota->quota_id,
            'shop_id' => $shop->shop_id,
            'withdrawal_method' => 'pickup',
            'product_name' => 'Test Product',
            'subsidized_price' => 50.00,
            'price' => 100.00,
            'dematerialized' => false,
        ], $overrides);
    }

    #[Test]
    public function it_can_create_a_product()
    {
        $shop = Shop::factory()->create();
        $quota = Quota::factory()->create();

        $product = Product::create($this->getValidProductData($shop, $quota));

        $this->assertDatabaseHas('product', [
            'product_id' => $product->product_id,
            'product_name' => 'Test Product',
            'price' => 100.00
        ]);
    }

    #[Test]
    public function it_can_update_a_product()
    {
        $shop = Shop::factory()->create();
        $quota = Quota::factory()->create();

        $product = Product::create($this->getValidProductData($shop, $quota));

        $product->update([
            'product_name' => 'Updated Product',
            'price' => 150.00
        ]);

        $this->assertDatabaseHas('product', [
            'product_id' => $product->product_id,
            'product_name' => 'Updated Product',
            'price' => 150.00
        ]);
    }

    #[Test]
    public function it_can_delete_a_product()
    {
        $shop = Shop::factory()->create();
        $quota = Quota::factory()->create();

        $product = Product::create($this->getValidProductData($shop, $quota));

        $product->delete();

        $this->assertDatabaseMissing('product', [
            'product_id' => $product->product_id
        ]);
    }

    #[Test]
    public function it_validates_required_fields()
    {
        $this->expectException(ValidationException::class);

        Product::create([
            'product_name' => 'Test Product'
            // Missing other required fields
        ]);
    }

    #[Test]
    public function it_validates_withdrawal_method()
    {
        $shop = Shop::factory()->create();
        $quota = Quota::factory()->create();

        $this->expectException(ValidationException::class);

        Product::create($this->getValidProductData($shop, $quota, [
            'withdrawal_method' => 'invalid_method'
        ]));
    }

    #[Test]
    public function it_validates_price_is_greater_than_subsidized_price()
    {
        $shop = Shop::factory()->create();
        $quota = Quota::factory()->create();

        $this->expectException(ValidationException::class);

        Product::create($this->getValidProductData($shop, $quota, [
            'price' => 50.00,
            'subsidized_price' => 100.00
        ]));
    }

    #[Test]
    public function it_casts_attributes_correctly()
    {
        $shop = Shop::factory()->create();
        $quota = Quota::factory()->create();

        $product = Product::create($this->getValidProductData($shop, $quota));

        $this->assertIsFloat($product->price);
        $this->assertIsFloat($product->subsidized_price);
        $this->assertIsBool($product->dematerialized);
    }

    #[Test]
    public function it_belongs_to_shop()
    {
        $shop = Shop::factory()->create();
        $quota = Quota::factory()->create();

        $product = Product::create($this->getValidProductData($shop, $quota));

        $this->assertEquals($shop->shop_id, $product->shop_id);
    }

    #[Test]
    public function it_belongs_to_quota()
    {
        $shop = Shop::factory()->create();
        $quota = Quota::factory()->create();

        $product = Product::create($this->getValidProductData($shop, $quota));

        $this->assertEquals($quota->quota_id, $product->quota_id);
    }

    #[Test]
    public function it_can_have_multiple_tickets()
    {
        $shop = Shop::factory()->create();
        $quota = Quota::factory()->create();

        $product = Product::create($this->getValidProductData($shop, $quota));

        // Create a user with valid status and group
        $user = User::factory()->create([
            'status_id' => 1,
            'group_id' => 1,
            'photo_release' => true,
            'photo_consent' => true,
            'is_admin' => true
        ]);

        // Create a site
        $site = Site::create([
            'label_site' => 'Test Site'
        ]);

        // Create multiple tickets for the product
        Ticket::factory()->count(3)->create([
            'product_id' => $product->product_id,
            'user_id' => $user->user_id,
            'site_id' => $site->site_id
        ]);

        $this->assertEquals(3, $product->tickets()->count());
    }
}
