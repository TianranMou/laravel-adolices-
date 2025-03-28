<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Adhesion;
use App\Models\Status;
use App\Models\Group;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Database\Seeders\StatusSeeder;
use Database\Seeders\GroupSeeder;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Carbon;

class AdhesionTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\StatusSeeder::class);
        $this->seed(\Database\Seeders\GroupSeeder::class);
    }

    #[Test]
    public function it_can_create_an_adhesion()
    {
        $user = User::factory()->create([
            'status_id' => 1,
            'group_id' => 1
        ]);

        $date = Carbon::now()->startOfDay();
        $adhesion = Adhesion::create([
            'user_id' => $user->user_id,
            'date_adhesion' => $date
        ]);

        $this->assertDatabaseHas('adhesion', [
            'adhesion_id' => $adhesion->adhesion_id,
            'user_id' => $user->user_id,
            'date_adhesion' => $date->format('Y-m-d')
        ]);
    }

    #[Test]
    public function it_can_retrieve_user_through_relationship()
    {
        $user = User::factory()->create([
            'status_id' => 1,
            'group_id' => 1
        ]);

        $adhesion = Adhesion::create([
            'user_id' => $user->user_id,
            'date_adhesion' => now()
        ]);

        $this->assertInstanceOf(User::class, $adhesion->user);
        $this->assertEquals($user->user_id, $adhesion->user->user_id);
    }

    #[Test]
    public function it_can_update_an_adhesion()
    {
        $user = User::factory()->create([
            'status_id' => 1,
            'group_id' => 1
        ]);

        $adhesion = Adhesion::create([
            'user_id' => $user->user_id,
            'date_adhesion' => now()
        ]);

        $newDate = Carbon::now()->addDays(5)->startOfDay();
        $adhesion->update(['date_adhesion' => $newDate]);

        $this->assertDatabaseHas('adhesion', [
            'adhesion_id' => $adhesion->adhesion_id,
            'date_adhesion' => $newDate->format('Y-m-d')
        ]);
    }

    #[Test]
    public function it_can_delete_an_adhesion()
    {
        $user = User::factory()->create([
            'status_id' => 1,
            'group_id' => 1
        ]);

        $adhesion = Adhesion::create([
            'user_id' => $user->user_id,
            'date_adhesion' => now()
        ]);

        $adhesion->delete();

        $this->assertDatabaseMissing('adhesion', [
            'adhesion_id' => $adhesion->adhesion_id
        ]);
    }

    #[Test]
    public function it_validates_required_fields()
    {
        $this->expectException(ValidationException::class);

        $adhesion = new Adhesion();
        $adhesion->save();
    }

    #[Test]
    public function it_validates_user_exists()
    {
        $this->expectException(ValidationException::class);

        $adhesion = new Adhesion([
            'user_id' => 999999,
            'date_adhesion' => now()
        ]);
        $adhesion->save();
    }

    #[Test]
    public function it_validates_date_format()
    {
        $user = User::factory()->create([
            'status_id' => 1,
            'group_id' => 1
        ]);

        $this->expectException(ValidationException::class);

        $adhesion = new Adhesion();
        $adhesion->fill([
            'user_id' => $user->user_id,
            'date_adhesion' => 'invalid-date'
        ]);
        $adhesion->save();
    }

    #[Test]
    public function it_can_have_multiple_adhesions_for_same_user()
    {
        $user = User::factory()->create([
            'status_id' => 1,
            'group_id' => 1
        ]);

        Adhesion::create([
            'user_id' => $user->user_id,
            'date_adhesion' => now()
        ]);

        Adhesion::create([
            'user_id' => $user->user_id,
            'date_adhesion' => now()->addDays(5)
        ]);

        $this->assertEquals(2, $user->adhesions()->count());
    }

    #[Test]
    public function it_can_handle_null_date()
    {
        $user = User::factory()->create([
            'status_id' => 1,
            'group_id' => 1
        ]);

        $adhesion = Adhesion::create([
            'user_id' => $user->user_id,
            'date_adhesion' => null
        ]);

        $this->assertDatabaseHas('adhesion', [
            'adhesion_id' => $adhesion->adhesion_id,
            'date_adhesion' => null
        ]);
    }

    #[Test]
    public function it_can_handle_future_date()
    {
        $user = User::factory()->create([
            'status_id' => 1,
            'group_id' => 1
        ]);

        $futureDate = Carbon::now()->addYear()->startOfDay();
        $adhesion = Adhesion::create([
            'user_id' => $user->user_id,
            'date_adhesion' => $futureDate
        ]);

        $this->assertDatabaseHas('adhesion', [
            'adhesion_id' => $adhesion->adhesion_id,
            'date_adhesion' => $futureDate->format('Y-m-d')
        ]);
    }

    #[Test]
    public function it_can_handle_past_date()
    {
        $user = User::factory()->create([
            'status_id' => 1,
            'group_id' => 1
        ]);

        $pastDate = Carbon::now()->subYear()->startOfDay();
        $adhesion = Adhesion::create([
            'user_id' => $user->user_id,
            'date_adhesion' => $pastDate
        ]);

        $this->assertDatabaseHas('adhesion', [
            'adhesion_id' => $adhesion->adhesion_id,
            'date_adhesion' => $pastDate->format('Y-m-d')
        ]);
    }
}
