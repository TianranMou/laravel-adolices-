<?php

namespace Tests\Feature;

use App\Models\FamilyMember;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FamilyMemberControllerTest extends TestCase
{
    use WithFaker;

    /** @test */
    public function it_can_create_a_family_member()
    {
        // Arrange
        $user = User::where('user_id', 1)->first(); // Assuming user with user_id 1 exists after seeding

        $this->assertNotNull($user, 'User with user_id 1 not found after seeding. Check your seeders.');

        $data = [
            'user_id' => $user->user_id,
            'name_member' => $this->faker->lastName,
            'birth_date_member' => $this->faker->date(),
            'first_name_member' => $this->faker->firstName,
            'relation' => $this->faker->word,
        ];

        // Act
        $response = $this->postJson('/family-members', $data);

        // Assert
        $response->assertStatus(201)
            ->assertJsonFragment($data);

        $this->assertDatabaseHas('family_members', $data);
    }

    /** @test */
    public function it_validates_required_fields_on_creation()
    {
        $data = []; // Empty data to trigger validation errors
        $response = $this->postJson('/family-members', $data);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['user_id', 'name_member', 'birth_date_member', 'first_name_member', 'relation']);
    }

    /** @test */
    public function it_can_update_a_family_member()
    {
        $familyMember = FamilyMember::first(); // Get the first family member (assuming one exists after seeding)
        $this->assertNotNull($familyMember, 'No family members found after seeding. Check your seeders.');

        $newData = [
            'name_member' => 'Updated Name',
            'relation' => 'Updated Relation',
        ];

        $response = $this->putJson("/family-members/{$familyMember->member_id}", $newData);
        $response->assertStatus(200)
            ->assertJsonFragment($newData);

        $this->assertDatabaseHas('family_members', [
            'member_id' => $familyMember->member_id,
            'name_member' => 'Updated Name',
            'relation' => 'Updated Relation',
        ]);
    }

    /** @test */
    public function it_validates_fields_on_update()
    {
        $familyMember = FamilyMember::first();
        $this->assertNotNull($familyMember, 'No family members found after seeding. Check your seeders.');

        $newData = [
            'name_member' => '', // Empty name to trigger validation error
        ];

        $response = $this->putJson("/family-members/{$familyMember->member_id}", $newData);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name_member']);
    }

    /** @test */
    public function it_returns_404_if_family_member_not_found_on_update()
    {
        $newData = [
            'name_member' => 'Updated Name',
        ];

        $response = $this->putJson("/family-members/999", $newData);
        $response->assertStatus(404)
            ->assertJson(['message' => 'Family member not found']);
    }
}
