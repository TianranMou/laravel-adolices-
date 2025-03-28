<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\StateSub;
use App\Models\Status;
use App\Models\Subvention;
use App\Models\User;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class SubventionTest extends TestCase
{
    use RefreshDatabase;

    private function getValidSubventionData(?User $user = null, ?StateSub $state = null): array
    {
        if (!$user) {
            $status = Status::factory()->create();
            $group = Group::factory()->create();
            $user = User::factory()->create([
                'status_id' => $status->status_id,
                'group_id' => $group->group_id
            ]);
        }

        if (!$state) {
            $state = StateSub::factory()->create();
        }

        return [
            'user_id' => $user->user_id,
            'state_id' => $state->state_id,
            'name_asso' => 'Test Association',
            'RIB' => 'FR7630006000011234567890189',
            'montant' => 1000.00,
            'link_attestation' => 'https://example.com/attestation.pdf',
            'motif_refus' => null,
            'payment_subvention' => '2024-03-20'
        ];
    }

    public function test_it_can_create_a_subvention()
    {
        $data = $this->getValidSubventionData();
        $subvention = Subvention::create($data);

        $this->assertDatabaseHas('subvention', [
            'subvention_id' => $subvention->subvention_id,
            'name_asso' => $data['name_asso'],
            'montant' => $data['montant']
        ]);
    }

    public function test_it_can_update_a_subvention()
    {
        $subvention = Subvention::create($this->getValidSubventionData());
        $newMontant = 2000.00;

        $subvention->update(['montant' => $newMontant]);

        $this->assertDatabaseHas('subvention', [
            'subvention_id' => $subvention->subvention_id,
            'montant' => $newMontant
        ]);
    }

    public function test_it_can_delete_a_subvention()
    {
        $subvention = Subvention::create($this->getValidSubventionData());

        $subvention->delete();

        $this->assertDatabaseMissing('subvention', [
            'subvention_id' => $subvention->subvention_id
        ]);
    }

    public function test_it_validates_required_fields()
    {
        $this->expectException(ValidationException::class);

        Subvention::create([]);
    }

    public function test_it_validates_numeric_montant()
    {
        $this->expectException(ValidationException::class);

        $data = $this->getValidSubventionData();
        $data['montant'] = 'not-a-number';

        Subvention::create($data);
    }

    public function test_it_validates_date_format()
    {
        $this->expectException(InvalidFormatException::class);

        $data = $this->getValidSubventionData();
        $data['payment_subvention'] = 'invalid-date';

        Subvention::create($data);
    }

    public function test_it_belongs_to_user()
    {
        $status = Status::factory()->create();
        $group = Group::factory()->create();
        $user = User::factory()->create([
            'status_id' => $status->status_id,
            'group_id' => $group->group_id
        ]);
        $subvention = Subvention::create($this->getValidSubventionData($user));

        $this->assertInstanceOf(User::class, $subvention->user);
        $this->assertEquals($user->user_id, $subvention->user->user_id);
    }

    public function test_it_belongs_to_state()
    {
        $state = StateSub::factory()->create();
        $subvention = Subvention::create($this->getValidSubventionData(null, $state));

        $this->assertInstanceOf(StateSub::class, $subvention->state);
        $this->assertEquals($state->state_id, $subvention->state->state_id);
    }

    public function test_it_can_have_null_payment_date()
    {
        $data = $this->getValidSubventionData();
        $data['payment_subvention'] = null;

        $subvention = Subvention::create($data);

        $this->assertNull($subvention->payment_subvention);
    }

    public function test_it_validates_user_exists()
    {
        $this->expectException(ValidationException::class);

        $data = $this->getValidSubventionData();
        $data['user_id'] = 99999; // Non-existent user ID

        Subvention::create($data);
    }

    public function test_it_validates_state_exists()
    {
        $this->expectException(ValidationException::class);

        $data = $this->getValidSubventionData();
        $data['state_id'] = 99999; // Non-existent state ID

        Subvention::create($data);
    }
}
