<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Status;
use App\Models\Group;
use App\Models\Site;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;

class UserTest extends TestCase
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

    #[Test]
    public function it_can_create_a_user()
    {
        $user = User::create([
            'email' => 'test1@example.com',
            'email_imt' => 'test1@imt.com',
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

        $this->assertDatabaseHas('users', [
            'user_id' => $user->user_id,
            'email' => 'test1@example.com',
            'is_admin' => false
        ]);
    }

    #[Test]
    public function it_can_update_a_user()
    {
        $user = User::create([
            'email' => 'test2@example.com',
            'email_imt' => 'test2@imt.com',
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

        $user->update([
            'email' => 'updated2@example.com',
            'email_imt' => 'updated2@imt.com',
            'is_admin' => true
        ]);

        $this->assertDatabaseHas('users', [
            'user_id' => $user->user_id,
            'email' => 'updated2@example.com',
            'is_admin' => true
        ]);
    }

    #[Test]
    public function it_can_delete_a_user()
    {
        $user = User::create([
            'email' => 'test3@example.com',
            'email_imt' => 'test3@imt.com',
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

        $user->delete();

        $this->assertDatabaseMissing('users', [
            'user_id' => $user->user_id
        ]);
    }

    #[Test]
    public function it_validates_required_fields()
    {
        $this->expectException(ValidationException::class);

        User::create([
            'email' => 'john@example.com'
        ]);
    }

    #[Test]
    public function it_validates_unique_email()
    {
        $this->expectException(ValidationException::class);

        User::create($this->getValidUserData());

        User::create($this->getValidUserData([
            'first_name' => 'Jane',
            'email_imt' => 'jane@imt.com'
        ]));
    }

    #[Test]
    public function it_belongs_to_status()
    {
        $status = Status::create(['status_label' => 'Test Status']);
        $user = User::create([
            'email' => 'test4@example.com',
            'email_imt' => 'test4@imt.com',
            'password' => 'password123',
            'is_admin' => false,
            'status_id' => $status->status_id,
            'group_id' => 1,
            'last_name' => 'Doe',
            'first_name' => 'John',
            'phone_number' => '1234567890',
            'photo_release' => true,
            'photo_consent' => true
        ]);

        $this->assertEquals($status->status_id, $user->status->status_id);
    }

    #[Test]
    public function it_belongs_to_group()
    {
        $group = Group::create(['group_id' => 993, 'label_group' => 'Test Group']);
        $user = User::create([
            'email' => 'test5@example.com',
            'email_imt' => 'test5@imt.com',
            'password' => 'password123',
            'is_admin' => false,
            'status_id' => 1,
            'group_id' => $group->group_id,
            'last_name' => 'Doe',
            'first_name' => 'John',
            'phone_number' => '1234567890',
            'photo_release' => true,
            'photo_consent' => true
        ]);

        $this->assertEquals($group->group_id, $user->group->group_id);
    }

    #[Test]
    public function it_can_have_multiple_sites()
    {
        $user = User::create([
            'email' => 'test6@example.com',
            'email_imt' => 'test6@imt.com',
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

        $site1 = Site::create(['label_site' => 'Site 1']);
        $site2 = Site::create(['label_site' => 'Site 2']);

        $user->sites()->attach([$site1->site_id, $site2->site_id]);

        $this->assertEquals(2, $user->sites()->count());
    }
}
