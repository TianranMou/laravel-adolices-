<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\StateSub;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Validation\ValidationException;

class StateSubTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    #[Test]
    public function it_can_create_a_state()
    {
        $state = StateSub::create([
            'label_state' => 'Test State'
        ]);

        $this->assertDatabaseHas('state_sub', [
            'state_id' => $state->state_id,
            'label_state' => 'Test State'
        ]);
    }

    #[Test]
    public function it_can_update_a_state()
    {
        $state = StateSub::create([
            'label_state' => 'Original State'
        ]);

        $state->update([
            'label_state' => 'Updated State'
        ]);

        $this->assertDatabaseHas('state_sub', [
            'state_id' => $state->state_id,
            'label_state' => 'Updated State'
        ]);
    }

    #[Test]
    public function it_can_delete_a_state()
    {
        $state = StateSub::create([
            'label_state' => 'Test State'
        ]);

        $state->delete();

        $this->assertDatabaseMissing('state_sub', [
            'state_id' => $state->state_id
        ]);
    }

    #[Test]
    public function it_validates_required_fields()
    {
        $this->expectException(ValidationException::class);

        $state = new StateSub();
        $state->save();
    }

    #[Test]
    public function it_validates_max_length()
    {
        $this->expectException(ValidationException::class);

        StateSub::create([
            'label_state' => str_repeat('a', 256) // 256 characters, max is 255
        ]);
    }

    #[Test]
    public function it_can_have_multiple_states()
    {
        $initialCount = StateSub::count();
        
        StateSub::create(['label_state' => 'State 1']);
        StateSub::create(['label_state' => 'State 2']);

        $this->assertEquals($initialCount + 2, StateSub::count());
    }

    #[Test]
    public function it_can_retrieve_state_by_id()
    {
        $state = StateSub::create([
            'label_state' => 'Test State'
        ]);

        $retrieved = StateSub::find($state->state_id);

        $this->assertEquals($state->label_state, $retrieved->label_state);
    }
}
