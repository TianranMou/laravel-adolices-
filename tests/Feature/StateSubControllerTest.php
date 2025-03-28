<?php

namespace Tests\Feature;

use App\Models\StateSub;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StateSubControllerTest extends TestCase
{
    use WithFaker;

    /** @test */
    public function it_can_create_a_statesub()
    {
        $stateSub = StateSub::first();
        $this->assertNotNull($stateSub, 'No state subs found after seeding. Check your seeders.');

        $data = ['label_state' => $this->faker->word];
        $response = $this->postJson('/state-subs', $data);
        $response->assertStatus(201)->assertJsonFragment($data);
        $this->assertDatabaseHas('state_sub', $data);
    }

    /** @test */
    public function it_validates_required_fields_on_creation()
    {
        $response = $this->postJson('/state-subs', []);
        $response->assertStatus(422)->assertJsonValidationErrors(['label_state']);
    }

    /** @test */
    public function it_can_show_a_statesub()
    {
        $stateSub = StateSub::first();
        $this->assertNotNull($stateSub, 'No state subs found after seeding. Check your seeders.');

        $response = $this->getJson("/state-subs/{$stateSub->id}");
        $response->assertStatus(200)->assertJsonFragment(['label_state' => $stateSub->label_state]);
    }

    /** @test */
    public function it_returns_404_if_statesub_not_found()
    {
        $response = $this->getJson('/state-subs/999'); // Non-existent ID
        $response->assertStatus(404);
    }

    /** @test */
    public function it_can_update_a_statesub()
    {
        $stateSub = StateSub::first();
        $this->assertNotNull($stateSub, 'No state subs found after seeding. Check your seeders.');

        $newData = ['label_state' => 'Updated State'];
        $response = $this->putJson("/state-subs/{$stateSub->state_id}", $newData);
        $response->assertStatus(200)->assertJsonFragment($newData);
        $this->assertDatabaseHas('state_sub', $newData + ['state_id' => $stateSub->state_id]);
    }

    /** @test */
    public function it_validates_fields_on_update()
    {
        $stateSub = StateSub::first();
        $this->assertNotNull($stateSub, 'No state subs found after seeding. Check your seeders.');

        $response = $this->putJson("/state-subs/{$stateSub->state_id}", ['label_state' => '']);
        $response->assertStatus(422)->assertJsonValidationErrors(['label_state']);
    }

    /** @test */
    public function it_returns_404_if_statesub_not_found_on_update()
    {
        $response = $this->putJson('/state-subs/999', ['label_state' => 'Updated State']); // Non-existent ID
        $response->assertStatus(404);
    }

    /** @test */
    public function it_can_delete_a_statesub()
    {
        $stateSub = StateSub::orderBy('state_id', 'desc')->first();
        $this->assertNotNull($stateSub, 'No state subs found after seeding. Check your seeders.');

        $response = $this->deleteJson("/state-subs/{$stateSub->state_id}");
        $response->assertStatus(204);
        $this->assertDatabaseMissing('state_sub', ['state_id' => $stateSub->state_id]);
    }

    /** @test */
    public function it_returns_404_if_statesub_not_found_on_delete()
    {
        $response = $this->deleteJson('/state-subs/999'); // Non-existent ID
        $response->assertStatus(404);
    }
}
