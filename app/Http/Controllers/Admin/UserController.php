<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TeamPermission;
use App\Enums\TeamRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        $team = $request->user()->currentTeam;

        abort_unless(
            $request->user()->hasTeamPermission($team, TeamPermission::UpdateMember),
            403
        );

        $users = $team->members()
            ->orderBy('name')
            ->get()
            ->map(function (User $user) {
                /** @var TeamRole $role */
                $role = $user->pivot->role;

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'active' => $user->active,
                    'role' => $role->value,
                    'role_label' => $role->label(),
                    'email_verified_at' => $user->email_verified_at?->toIso8601String(),
                    'created_at' => $user->created_at->toIso8601String(),
                ];
            });

        return Inertia::render('admin/users/Index', [
            'users' => $users,
        ]);
    }

    public function toggle(Request $request, User $user): RedirectResponse
    {
        $team = $request->user()->currentTeam;

        abort_unless(
            $request->user()->hasTeamPermission($team, TeamPermission::UpdateMember),
            403
        );

        abort_if($user->id === $request->user()->id, 403);

        $user->update(['active' => ! $user->active]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => $user->active
                ? __('Usuário ativado com sucesso.')
                : __('Usuário desativado com sucesso.'),
        ]);

        return to_route('admin.users.index');
    }
}
