<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Status;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Validation\ValidationException;
use Database\Seeders\StatusSeeder;

class StatusTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(StatusSeeder::class);
    }

    #[Test]
    public function it_can_create_a_status()
    {
        $status = Status::create([
            'status_label' => 'Test Status'
        ]);

        $this->assertDatabaseHas('status', [
            'status_id' => $status->status_id,
            'status_label' => 'Test Status'
        ]);
    }

    #[Test]
    public function it_can_update_a_status()
    {
        $status = Status::create([
            'status_label' => 'Original Status'
        ]);

        $status->update([
            'status_label' => 'Updated Status'
        ]);

        $this->assertDatabaseHas('status', [
            'status_id' => $status->status_id,
            'status_label' => 'Updated Status'
        ]);
    }

    #[Test]
    public function it_can_delete_a_status()
    {
        $status = Status::create([
            'status_label' => 'Test Status'
        ]);

        $status->delete();

        $this->assertDatabaseMissing('status', [
            'status_id' => $status->status_id
        ]);
    }

    #[Test]
    public function it_validates_required_fields()
    {
        $this->expectException(ValidationException::class);

        $status = new Status();
        $status->save();
    }

    #[Test]
    public function it_can_have_multiple_statuses()
    {
        $initialCount = Status::count();

        Status::create(['status_label' => 'Status 1']);
        Status::create(['status_label' => 'Status 2']);

        $this->assertEquals($initialCount + 2, Status::count());
    }

    #[Test]
    public function it_can_retrieve_status_by_id()
    {
        $status = Status::create([
            'status_label' => 'Test Status'
        ]);

        $retrieved = Status::find($status->status_id);

        $this->assertEquals($status->status_label, $retrieved->status_label);
    }
}
