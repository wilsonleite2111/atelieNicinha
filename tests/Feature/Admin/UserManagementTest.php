<?php

namespace Tests\Feature\Admin;

use App\Enums\TeamRole;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    // --- INDEX ---

    public function test_owner_can_view_users_index(): void
    {
        [$owner, $team] = $this->ownerWithTeam();

        $this->actingAs($owner)
            ->get(route('admin.users.index', $team))
            ->assertOk();
    }

    public function test_admin_can_view_users_index(): void
    {
        [, $team, $admin] = $this->teamWithRoles();

        $this->actingAs($admin)
            ->get(route('admin.users.index', $team))
            ->assertOk();
    }

    public function test_member_is_redirected_from_users_index(): void
    {
        [, $team, , $member] = $this->teamWithRoles();

        $this->actingAs($member)
            ->get(route('admin.users.index', $team))
            ->assertRedirect(route('home'));
    }

    public function test_guest_is_redirected_from_users_index(): void
    {
        $team = Team::factory()->create();

        $this->get(route('admin.users.index', $team))
            ->assertRedirect(route('login'));
    }

    // --- TOGGLE ---

    public function test_owner_can_deactivate_a_member(): void
    {
        [$owner, $team, , $member] = $this->teamWithRoles();

        $this->assertTrue($member->active);

        $this->actingAs($owner)
            ->patch(route('admin.users.toggle', [$team, $member]))
            ->assertRedirect(route('admin.users.index', $team));

        $this->assertFalse($member->fresh()->active);
    }

    public function test_owner_can_reactivate_a_member(): void
    {
        [$owner, $team, , $member] = $this->teamWithRoles();
        $member->update(['active' => false]);

        $this->actingAs($owner)
            ->patch(route('admin.users.toggle', [$team, $member]))
            ->assertRedirect(route('admin.users.index', $team));

        $this->assertTrue($member->fresh()->active);
    }

    public function test_member_is_redirected_when_toggling_users(): void
    {
        [, $team, , $member] = $this->teamWithRoles();
        $otherUser = User::factory()->create();
        $team->members()->attach($otherUser, ['role' => TeamRole::Member->value]);

        $this->actingAs($member)
            ->patch(route('admin.users.toggle', [$team, $otherUser]))
            ->assertRedirect(route('home'));
    }

    public function test_owner_cannot_toggle_themselves(): void
    {
        [$owner, $team] = $this->ownerWithTeam();

        $this->actingAs($owner)
            ->patch(route('admin.users.toggle', [$team, $owner]))
            ->assertForbidden();
    }

    // --- INACTIVE USER LOGIN ---

    public function test_inactive_user_is_logged_out_on_request(): void
    {
        [$owner, $team] = $this->ownerWithTeam();
        $owner->update(['active' => false]);

        $this->actingAs($owner)
            ->get(route('admin.users.index', $team))
            ->assertRedirect(route('login'));
    }

    // --- HELPERS ---

    /** @return array{User, Team} */
    private function ownerWithTeam(): array
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create();
        $team->members()->attach($owner, ['role' => TeamRole::Owner->value]);
        $owner->update(['current_team_id' => $team->id]);

        return [$owner, $team];
    }

    /** @return array{User, Team, User, User} */
    private function teamWithRoles(): array
    {
        [$owner, $team] = $this->ownerWithTeam();

        $admin = User::factory()->create();
        $team->members()->attach($admin, ['role' => TeamRole::Admin->value]);

        $member = User::factory()->create();
        $team->members()->attach($member, ['role' => TeamRole::Member->value]);

        return [$owner, $team, $admin, $member];
    }
}
