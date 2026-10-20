<?php

namespace Tests\Feature\Teams;

use App\Enums\TeamRole;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamTest extends TestCase
{
    use RefreshDatabase;

    public function test_team_slug_uses_next_available_suffix()
    {
        Team::factory()->create(['name' => 'Acme', 'slug' => 'acme']);
        Team::factory()->create(['name' => 'Acme One', 'slug' => 'acme-1']);
        Team::factory()->create(['name' => 'Acme Ten', 'slug' => 'acme-10']);

        $team = Team::create(['name' => 'Acme']);

        $this->assertSame('acme-11', $team->slug);
    }

    public function test_the_team_edit_page_can_be_rendered()
    {
        $user = User::factory()->create();
        $team = Team::factory()->create();

        $team->members()->attach($user, ['role' => TeamRole::Owner->value]);
        $user->switchTeam($team);

        $response = $this
            ->actingAs($user)
            ->get(route('teams.edit'));

        $response->assertOk();
    }

    public function test_teams_can_be_updated_by_owners()
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['name' => 'Original Name']);

        $team->members()->attach($user, ['role' => TeamRole::Owner->value]);
        $user->switchTeam($team);

        $response = $this
            ->actingAs($user)
            ->patch(route('teams.update'), [
                'name' => 'Updated Name',
            ]);

        $response->assertRedirect(route('teams.edit'));

        $this->assertDatabaseHas('teams', [
            'id' => $team->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_team_whatsapp_is_normalized_to_digits_with_country_code()
    {
        $user = User::factory()->create();
        $team = Team::factory()->create();

        $team->members()->attach($user, ['role' => TeamRole::Owner->value]);
        $user->switchTeam($team);

        $this->actingAs($user)
            ->patch(route('teams.update'), [
                'name' => $team->name,
                'whatsapp' => '(41) 99529-1049',
            ])
            ->assertRedirect(route('teams.edit'));

        $this->assertSame('5541995291049', $team->fresh()->whatsapp);
    }

    public function test_team_whatsapp_can_be_cleared()
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['whatsapp' => '5541995291049']);

        $team->members()->attach($user, ['role' => TeamRole::Owner->value]);
        $user->switchTeam($team);

        $this->actingAs($user)
            ->patch(route('teams.update'), [
                'name' => $team->name,
                'whatsapp' => '',
            ]);

        $this->assertNull($team->fresh()->whatsapp);
    }

    public function test_team_whatsapp_must_be_valid()
    {
        $user = User::factory()->create();
        $team = Team::factory()->create();

        $team->members()->attach($user, ['role' => TeamRole::Owner->value]);
        $user->switchTeam($team);

        $this->actingAs($user)
            ->patch(route('teams.update'), [
                'name' => $team->name,
                'whatsapp' => '1234',
            ])
            ->assertSessionHasErrors('whatsapp');
    }

    public function test_teams_cannot_be_updated_by_members()
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $team = Team::factory()->create();

        $team->members()->attach($owner, ['role' => TeamRole::Owner->value]);
        $owner->switchTeam($team);
        $team->members()->attach($member, ['role' => TeamRole::Member->value]);
        $member->switchTeam($team);

        $response = $this
            ->actingAs($member)
            ->patch(route('teams.update'), [
                'name' => 'Updated Name',
            ]);

        $response->assertForbidden();
    }

    public function test_guests_cannot_access_teams()
    {
        $response = $this->get(route('teams.edit'));

        $response->assertRedirect(route('login'));
    }
}
