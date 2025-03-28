<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Quota;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Validation\ValidationException;

class QuotaTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    #[Test]
    public function it_can_create_a_quota()
    {
        $quota = Quota::create([
            'value' => 100,
            'duration' => 30
        ]);

        $this->assertDatabaseHas('quota', [
            'quota_id' => $quota->quota_id,
            'value' => 100,
            'duration' => 30
        ]);
    }

    #[Test]
    public function it_can_update_a_quota()
    {
        $quota = Quota::create([
            'value' => 100,
            'duration' => 30
        ]);

        $quota->update([
            'value' => 200,
            'duration' => 60
        ]);

        $this->assertDatabaseHas('quota', [
            'quota_id' => $quota->quota_id,
            'value' => 200,
            'duration' => 60
        ]);
    }

    #[Test]
    public function it_can_delete_a_quota()
    {
        $quota = Quota::create([
            'value' => 100,
            'duration' => 30
        ]);

        $quota->delete();

        $this->assertDatabaseMissing('quota', [
            'quota_id' => $quota->quota_id
        ]);
    }

    #[Test]
    public function it_validates_required_fields()
    {
        $this->expectException(ValidationException::class);

        $quota = new Quota();
        $quota->save();
    }

    #[Test]
    public function it_validates_numeric_fields()
    {
        $this->expectException(ValidationException::class);

        Quota::create([
            'value' => 'not-a-number',
            'duration' => 'not-a-number'
        ]);
    }

    #[Test]
    public function it_can_have_multiple_quotas()
    {
        $initialCount = Quota::count();

        Quota::create([
            'value' => 100,
            'duration' => 30
        ]);

        Quota::create([
            'value' => 200,
            'duration' => 60
        ]);

        $this->assertEquals($initialCount + 2, Quota::count());
    }

    #[Test]
    public function it_can_retrieve_quota_by_id()
    {
        $quota = Quota::create([
            'value' => 100,
            'duration' => 30
        ]);

        $retrieved = Quota::find($quota->quota_id);

        $this->assertEquals($quota->value, $retrieved->value);
        $this->assertEquals($quota->duration, $retrieved->duration);
    }
}
