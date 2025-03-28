<?php

namespace Tests\Feature\Database;

use Tests\TestCase;
use App\Models\SiteUser;
use App\Models\Site;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Validation\ValidationException;

class SiteUserTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\StatusSeeder::class);
        $this->seed(\Database\Seeders\GroupSeeder::class);
    }

    private function getValidSiteUserData(Site $site, User $user, array $overrides = []): array
    {
        return array_merge([
            'site_id' => $site->site_id,
            'user_id' => $user->user_id,
        ], $overrides);
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
    public function it_can_create_a_site_user()
    {
        $site = Site::create(['label_site' => 'Test Site']);
        $user = $this->createUser();

        $siteUser = SiteUser::create($this->getValidSiteUserData($site, $user));

        $this->assertDatabaseHas('site_user', [
            'site_id' => $site->site_id,
            'user_id' => $user->user_id,
        ]);
    }

    #[Test]
    public function it_can_delete_a_site_user()
    {
        $site = Site::create(['label_site' => 'Test Site']);
        $user = $this->createUser();

        SiteUser::create($this->getValidSiteUserData($site, $user));

        SiteUser::where('site_id', $site->site_id)
            ->where('user_id', $user->user_id)
            ->delete();

        $this->assertDatabaseMissing('site_user', [
            'site_id' => $site->site_id,
            'user_id' => $user->user_id,
        ]);
    }

    #[Test]
    public function it_validates_required_fields()
    {
        $this->expectException(ValidationException::class);

        SiteUser::create([
            'site_id' => 1
            // Missing user_id
        ]);
    }

    #[Test]
    public function it_validates_site_exists()
    {
        $user = $this->createUser();

        $this->expectException(ValidationException::class);

        SiteUser::create([
            'site_id' => 99999, // Non-existent site
            'user_id' => $user->user_id
        ]);
    }

    #[Test]
    public function it_validates_user_exists()
    {
        $site = Site::create(['label_site' => 'Test Site']);

        $this->expectException(ValidationException::class);

        SiteUser::create([
            'site_id' => $site->site_id,
            'user_id' => 99999 // Non-existent user
        ]);
    }

    #[Test]
    public function it_belongs_to_site()
    {
        $site = Site::create(['label_site' => 'Test Site']);
        $user = $this->createUser();

        $siteUser = SiteUser::create($this->getValidSiteUserData($site, $user));

        $this->assertEquals($site->site_id, $siteUser->site_id);
        $this->assertInstanceOf(Site::class, $siteUser->site);
    }

    #[Test]
    public function it_belongs_to_user()
    {
        $site = Site::create(['label_site' => 'Test Site']);
        $user = $this->createUser();

        $siteUser = SiteUser::create($this->getValidSiteUserData($site, $user));

        $this->assertEquals($user->user_id, $siteUser->user_id);
        $this->assertInstanceOf(User::class, $siteUser->user);
    }

    #[Test]
    public function it_cannot_have_duplicate_entries()
    {
        $site = Site::create(['label_site' => 'Test Site']);
        $user = $this->createUser();

        // Create first site_user
        SiteUser::create($this->getValidSiteUserData($site, $user));

        // Try to create duplicate
        $this->expectException(\Illuminate\Database\QueryException::class);
        SiteUser::create($this->getValidSiteUserData($site, $user));
    }
}
