<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Site;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Validation\ValidationException;

class SiteTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    #[Test]
    public function it_can_create_a_site()
    {
        $site = Site::create([
            'label_site' => 'Test Site'
        ]);

        $this->assertDatabaseHas('site', [
            'site_id' => $site->site_id,
            'label_site' => 'Test Site'
        ]);
    }

    #[Test]
    public function it_can_update_a_site()
    {
        $site = Site::create([
            'label_site' => 'Original Site'
        ]);

        $site->update([
            'label_site' => 'Updated Site'
        ]);

        $this->assertDatabaseHas('site', [
            'site_id' => $site->site_id,
            'label_site' => 'Updated Site'
        ]);
    }

    #[Test]
    public function it_can_delete_a_site()
    {
        $site = Site::create([
            'label_site' => 'Test Site'
        ]);

        $site->delete();

        $this->assertDatabaseMissing('site', [
            'site_id' => $site->site_id
        ]);
    }

    #[Test]
    public function it_validates_required_fields()
    {
        $this->expectException(ValidationException::class);

        $site = new Site();
        $site->save();
    }

    #[Test]
    public function it_validates_max_length()
    {
        $this->expectException(ValidationException::class);

        Site::create([
            'label_site' => str_repeat('a', 191) // 191 characters, max is 190
        ]);
    }

    #[Test]
    public function it_can_have_multiple_sites()
    {
        $initialCount = Site::count();
        
        Site::create(['label_site' => 'Site 1']);
        Site::create(['label_site' => 'Site 2']);

        $this->assertEquals($initialCount + 2, Site::count());
    }

    #[Test]
    public function it_can_retrieve_site_by_id()
    {
        $site = Site::create([
            'label_site' => 'Test Site'
        ]);

        $retrieved = Site::find($site->site_id);

        $this->assertEquals($site->label_site, $retrieved->label_site);
    }
}
