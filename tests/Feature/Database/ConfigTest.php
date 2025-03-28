<?php

namespace Tests\Feature\Database;

use Tests\TestCase;
use App\Models\Config;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Validation\ValidationException;

class ConfigTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private function getValidConfigData(array $overrides = []): array
    {
        return array_merge([
            'config_label' => $this->faker->word,
            'config_value' => $this->faker->sentence,
        ], $overrides);
    }

    #[Test]
    public function it_can_create_a_config()
    {
        $config = Config::create($this->getValidConfigData());

        $this->assertDatabaseHas('config', [
            'config_id' => $config->config_id,
            'config_label' => $config->config_label,
            'config_value' => $config->config_value,
        ]);
    }

    #[Test]
    public function it_can_update_a_config()
    {
        $config = Config::create($this->getValidConfigData());
        $newValue = $this->faker->sentence;

        $config->update(['config_value' => $newValue]);

        $this->assertDatabaseHas('config', [
            'config_id' => $config->config_id,
            'config_value' => $newValue,
        ]);
    }

    #[Test]
    public function it_can_delete_a_config()
    {
        $config = Config::create($this->getValidConfigData());

        $config->delete();

        $this->assertDatabaseMissing('config', [
            'config_id' => $config->config_id,
        ]);
    }

    #[Test]
    public function it_validates_required_fields()
    {
        $this->expectException(ValidationException::class);

        Config::create([
            // Missing config_label and config_value
        ]);
    }

    #[Test]
    public function it_validates_config_label_max_length()
    {
        $this->expectException(ValidationException::class);

        Config::create([
            'config_label' => str_repeat('a', 51), // Max length is 50
            'config_value' => $this->faker->sentence,
        ]);
    }

    #[Test]
    public function it_validates_config_value_max_length()
    {
        $this->expectException(ValidationException::class);

        Config::create([
            'config_label' => $this->faker->word,
            'config_value' => str_repeat('a', 256), // Max length is 255
        ]);
    }

    #[Test]
    public function it_validates_config_label_is_string()
    {
        $this->expectException(ValidationException::class);

        Config::create([
            'config_label' => 123, // Should be a string
            'config_value' => $this->faker->sentence,
        ]);
    }

    #[Test]
    public function it_validates_config_value_is_string()
    {
        $this->expectException(ValidationException::class);

        Config::create([
            'config_label' => $this->faker->word,
            'config_value' => 123, // Should be a string
        ]);
    }

    #[Test]
    public function it_can_have_multiple_configs()
    {
        $initialCount = Config::count();

        Config::create($this->getValidConfigData());
        Config::create($this->getValidConfigData());

        $this->assertEquals($initialCount + 2, Config::count());
    }

    #[Test]
    public function it_can_retrieve_config_by_id()
    {
        $config = Config::create($this->getValidConfigData());

        $retrieved = Config::find($config->config_id);

        $this->assertEquals($config->config_label, $retrieved->config_label);
        $this->assertEquals($config->config_value, $retrieved->config_value);
    }
}
