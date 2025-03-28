<?php

namespace Tests\Feature\Database;

use Tests\TestCase;
use App\Models\MailTemplate;
use App\Models\Shop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Validation\ValidationException;

class MailTemplateTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private function getValidMailTemplateData(Shop $shop, array $overrides = []): array
    {
        return array_merge([
            'shop_id' => $shop->shop_id,
            'subject' => 'Test Subject',
            'content' => 'Test content for the email template.'
        ], $overrides);
    }

    #[Test]
    public function it_can_create_a_mail_template()
    {
        $shop = Shop::factory()->create();

        $mailTemplate = MailTemplate::create($this->getValidMailTemplateData($shop));

        $this->assertDatabaseHas('template_mail', [
            'mail_template_id' => $mailTemplate->mail_template_id,
            'subject' => 'Test Subject',
            'content' => 'Test content for the email template.'
        ]);
    }

    #[Test]
    public function it_can_update_a_mail_template()
    {
        $shop = Shop::factory()->create();

        $mailTemplate = MailTemplate::create($this->getValidMailTemplateData($shop));

        $mailTemplate->update([
            'subject' => 'Updated Subject',
            'content' => 'Updated content.'
        ]);

        $this->assertDatabaseHas('template_mail', [
            'mail_template_id' => $mailTemplate->mail_template_id,
            'subject' => 'Updated Subject',
            'content' => 'Updated content.'
        ]);
    }

    #[Test]
    public function it_can_delete_a_mail_template()
    {
        $shop = Shop::factory()->create();

        $mailTemplate = MailTemplate::create($this->getValidMailTemplateData($shop));

        $mailTemplate->delete();

        $this->assertDatabaseMissing('template_mail', [
            'mail_template_id' => $mailTemplate->mail_template_id
        ]);
    }

    #[Test]
    public function it_validates_required_fields()
    {
        $this->expectException(ValidationException::class);

        MailTemplate::create([
            'subject' => 'Test Subject'
            // Missing other required fields
        ]);
    }

    #[Test]
    public function it_validates_subject_length()
    {
        $shop = Shop::factory()->create();

        $this->expectException(ValidationException::class);

        MailTemplate::create($this->getValidMailTemplateData($shop, [
            'subject' => str_repeat('a', 101) // Subject longer than 100 characters
        ]));
    }

    #[Test]
    public function it_belongs_to_shop()
    {
        $shop = Shop::factory()->create();

        $mailTemplate = MailTemplate::create($this->getValidMailTemplateData($shop));

        $this->assertEquals($shop->shop_id, $mailTemplate->shop_id);
        $this->assertInstanceOf(Shop::class, $mailTemplate->shop);
    }
}
