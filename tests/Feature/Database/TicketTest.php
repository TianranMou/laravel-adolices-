<?php

namespace Tests\Feature\Database;

use Tests\TestCase;
use App\Models\Ticket;
use App\Models\Product;
use App\Models\User;
use App\Models\Site;
use App\Models\Shop;
use App\Models\Quota;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Validation\ValidationException;

class TicketTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\StatusSeeder::class);
        $this->seed(\Database\Seeders\GroupSeeder::class);
    }

    private function getValidTicketData(Product $product, User $user, Site $site, array $overrides = []): array
    {
        return array_merge([
            'product_id' => $product->product_id,
            'user_id' => $user->user_id,
            'site_id' => $site->site_id,
            'ticket_link' => 'https://example.com/ticket/123',
            'partner_code' => 'PARTNER123',
            'partner_id' => 'ID123',
            'validity_date' => '2024-12-31',
            'purchase_date' => '2024-03-21',
        ], $overrides);
    }

    private function createProduct(): Product
    {
        $shop = Shop::factory()->create();
        $quota = Quota::factory()->create();

        return Product::factory()->create([
            'shop_id' => $shop->shop_id,
            'quota_id' => $quota->quota_id
        ]);
    }

    private function createUser(): User
    {
        return User::factory()->create([
            'status_id' => 1,
            'group_id' => 1,
            'photo_release' => true,
            'photo_consent' => true,
            'is_admin' => true
        ]);
    }

    #[Test]
    public function it_can_create_a_ticket()
    {
        $product = $this->createProduct();
        $user = $this->createUser();
        $site = Site::create(['label_site' => 'Test Site']);

        $ticket = Ticket::create($this->getValidTicketData($product, $user, $site));

        $this->assertDatabaseHas('ticket', [
            'ticket_id' => $ticket->ticket_id,
            'product_id' => $product->product_id,
            'user_id' => $user->user_id,
            'site_id' => $site->site_id,
            'ticket_link' => 'https://example.com/ticket/123',
        ]);
    }

    #[Test]
    public function it_can_update_a_ticket()
    {
        $product = $this->createProduct();
        $user = $this->createUser();
        $site = Site::create(['label_site' => 'Test Site']);

        $ticket = Ticket::create($this->getValidTicketData($product, $user, $site));

        $ticket->update([
            'ticket_link' => 'https://example.com/ticket/456',
            'partner_code' => 'UPDATED123'
        ]);

        $this->assertDatabaseHas('ticket', [
            'ticket_id' => $ticket->ticket_id,
            'ticket_link' => 'https://example.com/ticket/456',
            'partner_code' => 'UPDATED123'
        ]);
    }

    #[Test]
    public function it_can_delete_a_ticket()
    {
        $product = $this->createProduct();
        $user = $this->createUser();
        $site = Site::create(['label_site' => 'Test Site']);

        $ticket = Ticket::create($this->getValidTicketData($product, $user, $site));

        $ticket->delete();

        $this->assertDatabaseMissing('ticket', [
            'ticket_id' => $ticket->ticket_id
        ]);
    }

    #[Test]
    public function it_validates_required_fields()
    {
        $this->expectException(ValidationException::class);

        Ticket::create([
            'ticket_link' => 'https://example.com/ticket/123'
            // Missing required fields
        ]);
    }

    #[Test]
    public function it_validates_dates()
    {
        $product = $this->createProduct();
        $user = $this->createUser();
        $site = Site::create(['label_site' => 'Test Site']);

        $this->expectException(InvalidFormatException::class);

        Ticket::create($this->getValidTicketData($product, $user, $site, [
            'validity_date' => 'invalid-date'
        ]));
    }

    #[Test]
    public function it_belongs_to_product()
    {
        $product = $this->createProduct();
        $user = $this->createUser();
        $site = Site::create(['label_site' => 'Test Site']);

        $ticket = Ticket::create($this->getValidTicketData($product, $user, $site));

        $this->assertEquals($product->product_id, $ticket->product_id);
        $this->assertInstanceOf(Product::class, $ticket->produit);
    }

    #[Test]
    public function it_belongs_to_user()
    {
        $product = $this->createProduct();
        $user = $this->createUser();
        $site = Site::create(['label_site' => 'Test Site']);

        $ticket = Ticket::create($this->getValidTicketData($product, $user, $site));

        $this->assertEquals($user->user_id, $ticket->user_id);
        $this->assertInstanceOf(User::class, $ticket->user);
    }

    #[Test]
    public function it_belongs_to_site()
    {
        $product = $this->createProduct();
        $user = $this->createUser();
        $site = Site::create(['label_site' => 'Test Site']);

        $ticket = Ticket::create($this->getValidTicketData($product, $user, $site));

        $this->assertEquals($site->site_id, $ticket->site_id);
        $this->assertInstanceOf(Site::class, $ticket->site);
    }

    #[Test]
    public function it_can_have_nullable_fields()
    {
        $product = $this->createProduct();
        $user = $this->createUser();
        $site = Site::create(['label_site' => 'Test Site']);

        $ticket = Ticket::create($this->getValidTicketData($product, $user, $site, [
            'ticket_link' => null,
            'partner_code' => null,
            'partner_id' => null,
            'validity_date' => null,
            'purchase_date' => null
        ]));

        $this->assertNull($ticket->ticket_link);
        $this->assertNull($ticket->partner_code);
        $this->assertNull($ticket->partner_id);
        $this->assertNull($ticket->validity_date);
        $this->assertNull($ticket->purchase_date);
    }

    #[Test]
    public function it_casts_dates_correctly()
    {
        $product = $this->createProduct();
        $user = $this->createUser();
        $site = Site::create(['label_site' => 'Test Site']);

        $ticket = Ticket::create($this->getValidTicketData($product, $user, $site));

        $this->assertInstanceOf(Carbon::class, $ticket->validity_date);
        $this->assertInstanceOf(Carbon::class, $ticket->purchase_date);
        $this->assertInstanceOf(Carbon::class, $ticket->created_at);
        $this->assertInstanceOf(Carbon::class, $ticket->updated_at);
    }
}
