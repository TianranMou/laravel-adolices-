<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Group;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class GroupTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    #[Test]
    public function it_can_create_a_group()
    {
        $group = Group::create([
            'group_id' => 999,
            'label_group' => 'Test Group'
        ]);

        $this->assertDatabaseHas('group', [
            'group_id' => $group->group_id,
            'label_group' => 'Test Group'
        ]);
    }

    #[Test]
    public function it_can_update_a_group()
    {
        $group = Group::create([
            'group_id' => 998,
            'label_group' => 'Original Group'
        ]);

        $group->update([
            'label_group' => 'Updated Group'
        ]);

        $this->assertDatabaseHas('group', [
            'group_id' => $group->group_id,
            'label_group' => 'Updated Group'
        ]);
    }

    #[Test]
    public function it_can_delete_a_group()
    {
        $group = Group::create([
            'group_id' => 997,
            'label_group' => 'Test Group'
        ]);

        $group->delete();

        $this->assertDatabaseMissing('group', [
            'group_id' => $group->group_id
        ]);
    }

    #[Test]
    public function it_validates_required_fields()
    {
        $this->expectException(ValidationException::class);

        $group = new Group();
        $group->save();
    }

    #[Test]
    public function it_can_have_multiple_groups()
    {
        $initialCount = Group::count();

        Group::create(['group_id' => 996, 'label_group' => 'Group 1']);
        Group::create(['group_id' => 995, 'label_group' => 'Group 2']);

        $this->assertEquals($initialCount + 2, Group::count());
    }

    #[Test]
    public function it_can_retrieve_group_by_id()
    {
        $group = Group::create([
            'group_id' => 994,
            'label_group' => 'Test Group'
        ]);

        $retrieved = Group::find($group->group_id);

        $this->assertEquals($group->label_group, $retrieved->label_group);
    }

    #[Test]
    public function it_validates_unique_group_id()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        // Create a group first
        $group = Group::create(['label_group' => 'First Group']);

        // Try to insert another group with the same ID using raw SQL
        DB::table('group')->insert([
            'group_id' => $group->group_id,
            'label_group' => 'Second Group'
        ]);
    }
}
