<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\FamilyMember;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Carbon\Exceptions\InvalidFormatException;

class FamilyMemberTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\StatusSeeder::class);
        $this->seed(\Database\Seeders\GroupSeeder::class);
    }

    private function getValidUserData(array $overrides = []): array
    {
        return array_merge([
            'status_id' => 1,
            'group_id' => 1,
            'last_name' => 'Doe',
            'first_name' => 'John',
            'email' => 'john@example.com',
            'email_imt' => 'john@imt.com',
            'password' => Hash::make('password'),
            'phone_number' => '1234567890',
            'photo_release' => true,
            'photo_consent' => true,
            'is_admin' => false
        ], $overrides);
    }

    private function getValidFamilyMemberData(array $overrides = []): array
    {
        return array_merge([
            'user_id' => User::create($this->getValidUserData())->user_id,
            'name_member' => 'Smith',
            'first_name_member' => 'Jane',
            'birth_date_member' => '1990-01-01',
            'relation' => 'Sister'
        ], $overrides);
    }

    #[Test]
    public function it_can_create_a_family_member()
    {
        $familyMember = FamilyMember::create($this->getValidFamilyMemberData());

        $this->assertDatabaseHas('family_members', [
            'member_id' => $familyMember->member_id,
            'name_member' => 'Smith',
            'first_name_member' => 'Jane'
        ]);
    }

    #[Test]
    public function it_can_update_a_family_member()
    {
        $user = User::create([
            'email' => 'test7@example.com',
            'email_imt' => 'test7@imt.com',
            'password' => 'password123',
            'is_admin' => false,
            'status_id' => 1,
            'group_id' => 1,
            'last_name' => 'Doe',
            'first_name' => 'John',
            'phone_number' => '1234567890',
            'photo_release' => true,
            'photo_consent' => true
        ]);

        $familyMember = FamilyMember::create([
            'user_id' => $user->user_id,
            'name_member' => 'Doe',
            'first_name_member' => 'John',
            'birth_date_member' => '1990-01-01',
            'relation' => 'Brother'
        ]);

        $familyMember->update([
            'name_member' => 'Smith',
            'first_name_member' => 'Jane'
        ]);

        $this->assertDatabaseHas('family_members', [
            'member_id' => $familyMember->member_id,
            'name_member' => 'Smith',
            'first_name_member' => 'Jane'
        ]);
    }

    #[Test]
    public function it_can_delete_a_family_member()
    {
        $user = User::create([
            'email' => 'test8@example.com',
            'email_imt' => 'test8@imt.com',
            'password' => 'password123',
            'is_admin' => false,
            'status_id' => 1,
            'group_id' => 1,
            'last_name' => 'Doe',
            'first_name' => 'John',
            'phone_number' => '1234567890',
            'photo_release' => true,
            'photo_consent' => true
        ]);

        $familyMember = FamilyMember::create([
            'user_id' => $user->user_id,
            'name_member' => 'Doe',
            'first_name_member' => 'John',
            'birth_date_member' => '1990-01-01',
            'relation' => 'Brother'
        ]);

        $familyMember->delete();

        $this->assertDatabaseMissing('family_members', [
            'member_id' => $familyMember->member_id
        ]);
    }

    #[Test]
    public function it_validates_required_fields()
    {
        $this->expectException(ValidationException::class);

        FamilyMember::create([
            'name_member' => 'Smith'
        ]);
    }

    #[Test]
    public function it_validates_date_format()
    {
        $this->expectException(InvalidFormatException::class);

        $user = User::create([
            'email' => 'test9@example.com',
            'email_imt' => 'test9@imt.com',
            'password' => 'password123',
            'is_admin' => false,
            'status_id' => 1,
            'group_id' => 1,
            'last_name' => 'Doe',
            'first_name' => 'John',
            'phone_number' => '1234567890',
            'photo_release' => true,
            'photo_consent' => true
        ]);

        FamilyMember::create([
            'user_id' => $user->user_id,
            'name_member' => 'Doe',
            'first_name_member' => 'John',
            'birth_date_member' => 'invalid-date',
            'relation' => 'Brother'
        ]);
    }

    #[Test]
    public function it_belongs_to_user()
    {
        $user = User::create([
            'email' => 'test10@example.com',
            'email_imt' => 'test10@imt.com',
            'password' => 'password123',
            'is_admin' => false,
            'status_id' => 1,
            'group_id' => 1,
            'last_name' => 'Doe',
            'first_name' => 'John',
            'phone_number' => '1234567890',
            'photo_release' => true,
            'photo_consent' => true
        ]);

        $familyMember = FamilyMember::create([
            'user_id' => $user->user_id,
            'name_member' => 'Doe',
            'first_name_member' => 'John',
            'birth_date_member' => '1990-01-01',
            'relation' => 'Brother'
        ]);

        $this->assertEquals($user->user_id, $familyMember->user->user_id);
    }

    #[Test]
    public function it_validates_user_exists()
    {
        $this->expectException(ValidationException::class);

        FamilyMember::create($this->getValidFamilyMemberData([
            'user_id' => 999999 // Non-existent user ID
        ]));
    }
}
