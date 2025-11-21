<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Estrategy;
use App\Models\Institution;
use App\Models\User;
use App\Models\Role;
use App\Policies\EstrategyPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EstrategyPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected EstrategyPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new EstrategyPolicy();
    }

    /** @test */
    public function all_users_can_view_any_estrategies()
    {
        $role = Role::factory()->create();
        $user = User::factory()->create(['role_id' => $role->id]);

        $this->assertTrue($this->policy->viewAny($user));
    }

    /** @test */
    public function all_users_can_create_estrategies()
    {
        $role = Role::factory()->create();
        $user = User::factory()->create(['role_id' => $role->id]);

        $this->assertTrue($this->policy->create($user));
    }

    /** @test */
    public function super_admin_can_update_any_estrategy()
    {
        $role = Role::factory()->superAdmin()->create();
        $institution1 = Institution::factory()->create();
        $institution2 = Institution::factory()->create();

        $user = User::factory()->create([
            'role_id' => $role->id,
            'institution_id' => $institution1->id
        ]);

        $estrategyFromOtherInstitution = Estrategy::factory()->create([
            'institution_id' => $institution2->id
        ]);

        $this->assertTrue($this->policy->update($user, $estrategyFromOtherInstitution));
    }

    /** @test */
    public function regular_user_can_update_own_institution_estrategy()
    {
        $role = Role::factory()->create(['name' => 'institution_user']);
        $institution = Institution::factory()->create();

        $user = User::factory()->create([
            'role_id' => $role->id,
            'institution_id' => $institution->id
        ]);

        $estrategy = Estrategy::factory()->create([
            'institution_id' => $institution->id
        ]);

        $this->assertTrue($this->policy->update($user, $estrategy));
    }

    /** @test */
    public function regular_user_cannot_update_other_institution_estrategy()
    {
        $role = Role::factory()->create(['name' => 'institution_user']);
        $institution1 = Institution::factory()->create();
        $institution2 = Institution::factory()->create();

        $user = User::factory()->create([
            'role_id' => $role->id,
            'institution_id' => $institution1->id
        ]);

        $estrategyFromOtherInstitution = Estrategy::factory()->create([
            'institution_id' => $institution2->id
        ]);

        $this->assertFalse($this->policy->update($user, $estrategyFromOtherInstitution));
    }

    /** @test */
    public function only_super_admin_can_delete_estrategies()
    {
        $superAdminRole = Role::factory()->superAdmin()->create();
        $regularRole = Role::factory()->create(['name' => 'institution_user']);

        $superAdmin = User::factory()->create(['role_id' => $superAdminRole->id]);
        $regularUser = User::factory()->create(['role_id' => $regularRole->id]);

        $estrategy = Estrategy::factory()->create();

        $this->assertTrue($this->policy->delete($superAdmin, $estrategy));
        $this->assertFalse($this->policy->delete($regularUser, $estrategy));
    }

    /** @test */
    public function sector_coordinator_can_update_own_institution_estrategy()
    {
        $role = Role::factory()->sectorCoordinator()->create();
        $institution = Institution::factory()->create();

        $user = User::factory()->create([
            'role_id' => $role->id,
            'institution_id' => $institution->id
        ]);

        $estrategy = Estrategy::factory()->create([
            'institution_id' => $institution->id
        ]);

        $this->assertTrue($this->policy->update($user, $estrategy));
    }

    /** @test */
    public function sector_coordinator_cannot_update_other_institution_estrategy()
    {
        $role = Role::factory()->sectorCoordinator()->create();
        $institution1 = Institution::factory()->create();
        $institution2 = Institution::factory()->create();

        $user = User::factory()->create([
            'role_id' => $role->id,
            'institution_id' => $institution1->id
        ]);

        $estrategyFromOtherInstitution = Estrategy::factory()->create([
            'institution_id' => $institution2->id
        ]);

        $this->assertFalse($this->policy->update($user, $estrategyFromOtherInstitution));
    }

    /** @test */
    public function dgnc_user_can_update_own_institution_estrategy()
    {
        $role = Role::factory()->dgncUser()->create();
        $institution = Institution::factory()->create();

        $user = User::factory()->create([
            'role_id' => $role->id,
            'institution_id' => $institution->id
        ]);

        $estrategy = Estrategy::factory()->create([
            'institution_id' => $institution->id
        ]);

        $this->assertTrue($this->policy->update($user, $estrategy));
    }

    /** @test */
    public function dgnc_user_cannot_update_other_institution_estrategy()
    {
        $role = Role::factory()->dgncUser()->create();
        $institution1 = Institution::factory()->create();
        $institution2 = Institution::factory()->create();

        $user = User::factory()->create([
            'role_id' => $role->id,
            'institution_id' => $institution1->id
        ]);

        $estrategyFromOtherInstitution = Estrategy::factory()->create([
            'institution_id' => $institution2->id
        ]);

        $this->assertFalse($this->policy->update($user, $estrategyFromOtherInstitution));
    }

    /** @test */
    public function sector_coordinator_cannot_delete_estrategies()
    {
        $role = Role::factory()->sectorCoordinator()->create();
        $user = User::factory()->create(['role_id' => $role->id]);
        $estrategy = Estrategy::factory()->create();

        $this->assertFalse($this->policy->delete($user, $estrategy));
    }

    /** @test */
    public function dgnc_user_cannot_delete_estrategies()
    {
        $role = Role::factory()->dgncUser()->create();
        $user = User::factory()->create(['role_id' => $role->id]);
        $estrategy = Estrategy::factory()->create();

        $this->assertFalse($this->policy->delete($user, $estrategy));
    }

    /** @test */
    public function super_admin_can_delete_estrategy_from_any_institution()
    {
        $role = Role::factory()->superAdmin()->create();
        $institution1 = Institution::factory()->create();
        $institution2 = Institution::factory()->create();

        $user = User::factory()->create([
            'role_id' => $role->id,
            'institution_id' => $institution1->id
        ]);

        $estrategyFromOtherInstitution = Estrategy::factory()->create([
            'institution_id' => $institution2->id
        ]);

        $this->assertTrue($this->policy->delete($user, $estrategyFromOtherInstitution));
    }
}
