<?php

namespace Tests\Feature\Database;

use Tests\TestCase;
use App\Models\Administrator;
use App\Models\Shop;
use App\Models\User;
use App\Models\Status;
use App\Models\Group;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Validation\ValidationException;

class AdministratorTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\StatusSeeder::class);
        $this->seed(\Database\Seeders\GroupSeeder::class);
    }

    private function getValidAdministratorData(Shop $shop, User $user, array $overrides = []): array
    {
        return array_merge([
            'shop_id' => $shop->shop_id,
            'user_id' => $user->user_id,
        ], $overrides);
    }

    #[Test]
    public function it_can_create_an_administrator()
    {
        $shop = Shop::factory()->create();
        $user = User::factory()->create([
            'status_id' => 1,
            'group_id' => 1,
            'photo_release' => true,
            'photo_consent' => true,
            'is_admin' => true
        ]);

        $administrator = Administrator::create($this->getValidAdministratorData($shop, $user));

        $this->assertDatabaseHas('administrator', [
            'shop_id' => $shop->shop_id,
            'user_id' => $user->user_id,
        ]);
    }

    #[Test]
    public function it_can_delete_an_administrator()
    {
        $shop = Shop::factory()->create();
        $user = User::factory()->create([
            'status_id' => 1,
            'group_id' => 1,
            'photo_release' => true,
            'photo_consent' => true,
            'is_admin' => true
        ]);

        Administrator::create($this->getValidAdministratorData($shop, $user));

        Administrator::where('shop_id', $shop->shop_id)
            ->where('user_id', $user->user_id)
            ->delete();

        $this->assertDatabaseMissing('administrator', [
            'shop_id' => $shop->shop_id,
            'user_id' => $user->user_id,
        ]);
    }

    #[Test]
    public function it_validates_required_fields()
    {
        $this->expectException(ValidationException::class);

        Administrator::create([
            'shop_id' => 1
            // Missing user_id
        ]);
    }

    #[Test]
    public function it_validates_shop_exists()
    {
        $user = User::factory()->create([
            'status_id' => 1,
            'group_id' => 1,
            'photo_release' => true,
            'photo_consent' => true,
            'is_admin' => true
        ]);

        $this->expectException(ValidationException::class);

        Administrator::create([
            'shop_id' => 99999, // Non-existent shop
            'user_id' => $user->user_id
        ]);
    }

    #[Test]
    public function it_validates_user_exists()
    {
        $shop = Shop::factory()->create();

        $this->expectException(ValidationException::class);

        Administrator::create([
            'shop_id' => $shop->shop_id,
            'user_id' => 99999 // Non-existent user
        ]);
    }

    #[Test]
    public function it_belongs_to_shop()
    {
        $shop = Shop::factory()->create();
        $user = User::factory()->create([
            'status_id' => 1,
            'group_id' => 1,
            'photo_release' => true,
            'photo_consent' => true,
            'is_admin' => true
        ]);

        $administrator = Administrator::create($this->getValidAdministratorData($shop, $user));

        $this->assertEquals($shop->shop_id, $administrator->shop_id);
        $this->assertInstanceOf(Shop::class, $administrator->shop);
    }

    #[Test]
    public function it_belongs_to_user()
    {
        $shop = Shop::factory()->create();
        $user = User::factory()->create([
            'status_id' => 1,
            'group_id' => 1,
            'photo_release' => true,
            'photo_consent' => true,
            'is_admin' => true
        ]);

        $administrator = Administrator::create($this->getValidAdministratorData($shop, $user));

        $this->assertEquals($user->user_id, $administrator->user_id);
        $this->assertInstanceOf(User::class, $administrator->user);
    }

    #[Test]
    public function it_cannot_have_duplicate_entries()
    {
        $shop = Shop::factory()->create();
        $user = User::factory()->create([
            'status_id' => 1,
            'group_id' => 1,
            'photo_release' => true,
            'photo_consent' => true,
            'is_admin' => true
        ]);

        // Create first administrator
        Administrator::create($this->getValidAdministratorData($shop, $user));

        // Try to create duplicate
        $this->expectException(\Illuminate\Database\QueryException::class);
        Administrator::create($this->getValidAdministratorData($shop, $user));
    }
}
