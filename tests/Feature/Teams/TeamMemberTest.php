<?php

namespace Tests\Feature\Teams;

use App\Enums\TeamRole;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamMemberTest extends TestCase
{
    use RefreshDatabase;

    public function test_team_member_roles_can_be_updated_by_owners()
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $team = Team::factory()->create();

        $team->members()->attach($owner, ['role' => TeamRole::Owner->value]);
        $owner->switchTeam($team);
        $team->members()->attach($member, ['role' => TeamRole::Member->value]);
        $member->switchTeam($team);

        $response = $this
            ->actingAs($owner)
            ->patch(route('teams.members.update', $member), [
                'role' => TeamRole::Admin->value,
            ]);

        $response->assertRedirect(route('teams.edit'));

        $this->assertEquals(
            TeamRole::Admin->value,
            $team->members()->where('user_id', $member->id)->first()->pivot->role->value,
        );
    }

    public function test_team_member_roles_can_be_updated_by_admins()
    {
        $owner = User::factory()->create();
        $admin = User::factory()->create();
        $member = User::factory()->create();
        $team = Team::factory()->create();

        $team->members()->attach($owner, ['role' => TeamRole::Owner->value]);
        $owner->switchTeam($team);
        $team->members()->attach($admin, ['role' => TeamRole::Admin->value]);
        $admin->switchTeam($team);
        $team->members()->attach($member, ['role' => TeamRole::Member->value]);
        $member->switchTeam($team);

        $response = $this
            ->actingAs($admin)
            ->patch(route('teams.members.update', $member), [
                'role' => TeamRole::Admin->value,
            ]);

        $response->assertRedirect(route('teams.edit'));
    }

    public function test_team_member_roles_cannot_be_updated_by_members()
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $otherMember = User::factory()->create();
        $team = Team::factory()->create();

        $team->members()->attach($owner, ['role' => TeamRole::Owner->value]);
        $owner->switchTeam($team);
        $team->members()->attach($member, ['role' => TeamRole::Member->value]);
        $member->switchTeam($team);
        $team->members()->attach($otherMember, ['role' => TeamRole::Member->value]);
        $otherMember->switchTeam($team);

        $response = $this
            ->actingAs($member)
            ->patch(route('teams.members.update', $otherMember), [
                'role' => TeamRole::Admin->value,
            ]);

        $response->assertForbidden();
    }

    public function test_team_members_can_be_removed_by_owners()
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $team = Team::factory()->create();

        $team->members()->attach($owner, ['role' => TeamRole::Owner->value]);
        $owner->switchTeam($team);
        $team->members()->attach($member, ['role' => TeamRole::Member->value]);
        $member->switchTeam($team);

        $response = $this
            ->actingAs($owner)
            ->delete(route('teams.members.destroy', $member));

        $response->assertRedirect(route('teams.edit'));

        $this->assertFalse($member->fresh()->belongsToTeam($team));
    }

    public function test_team_members_can_be_removed_by_admins()
    {
        $owner = User::factory()->create();
        $admin = User::factory()->create();
        $member = User::factory()->create();
        $team = Team::factory()->create();

        $team->members()->attach($owner, ['role' => TeamRole::Owner->value]);
        $owner->switchTeam($team);
        $team->members()->attach($admin, ['role' => TeamRole::Admin->value]);
        $admin->switchTeam($team);
        $team->members()->attach($member, ['role' => TeamRole::Member->value]);
        $member->switchTeam($team);

        $response = $this
            ->actingAs($admin)
            ->delete(route('teams.members.destroy', $member));

        $response->assertRedirect(route('teams.edit'));

        $this->assertFalse($member->fresh()->belongsToTeam($team));
    }

    public function test_team_members_cannot_be_removed_by_members()
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $otherMember = User::factory()->create();
        $team = Team::factory()->create();

        $team->members()->attach($owner, ['role' => TeamRole::Owner->value]);
        $owner->switchTeam($team);
        $team->members()->attach($member, ['role' => TeamRole::Member->value]);
        $member->switchTeam($team);
        $team->members()->attach($otherMember, ['role' => TeamRole::Member->value]);
        $otherMember->switchTeam($team);

        $response = $this
            ->actingAs($member)
            ->delete(route('teams.members.destroy', $otherMember));

        $response->assertForbidden();
    }

    public function test_team_owner_cannot_be_removed()
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create();

        $team->members()->attach($owner, ['role' => TeamRole::Owner->value]);
        $owner->switchTeam($team);

        $response = $this
            ->actingAs($owner)
            ->delete(route('teams.members.destroy', $owner));

        $response->assertForbidden();

        $this->assertTrue($owner->fresh()->belongsToTeam($team));
    }

    public function test_team_member_role_cannot_be_set_to_owner()
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $team = Team::factory()->create();

        $team->members()->attach($owner, ['role' => TeamRole::Owner->value]);
        $owner->switchTeam($team);
        $team->members()->attach($member, ['role' => TeamRole::Member->value]);
        $member->switchTeam($team);

        $response = $this
            ->actingAs($owner)
            ->patch(route('teams.members.update', $member), [
                'role' => TeamRole::Owner->value,
            ]);

        $response->assertSessionHasErrors('role');

        $this->assertEquals(
            TeamRole::Member->value,
            $team->members()->where('user_id', $member->id)->first()->pivot->role->value,
        );
    }

    public function test_removed_member_current_team_is_set_to_personal_team()
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $personalTeam = $member->personalTeam();
        $team = Team::factory()->create();

        $team->members()->attach($owner, ['role' => TeamRole::Owner->value]);
        $owner->switchTeam($team);
        $team->members()->attach($member, ['role' => TeamRole::Member->value]);
        $member->switchTeam($team);

        $member->update(['current_team_id' => $team->id]);

        $this
            ->actingAs($owner)
            ->delete(route('teams.members.destroy', $member));

        $this->assertEquals($personalTeam->id, $member->fresh()->current_team_id);
    }
}
