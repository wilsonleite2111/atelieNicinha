<?php

namespace App\Policies;

use App\Enums\TeamPermission;
use App\Models\Product;
use App\Models\Team;
use App\Models\User;

class ProductPolicy
{
    public function viewAny(User $user, Team $team): bool
    {
        return $user->belongsToTeam($team);
    }

    public function view(User $user, Product $product): bool
    {
        return $user->belongsToTeam($product->team);
    }

    public function create(User $user, Team $team): bool
    {
        return $user->hasTeamPermission($team, TeamPermission::ManageProducts);
    }

    public function update(User $user, Product $product): bool
    {
        return $user->hasTeamPermission($product->team, TeamPermission::ManageProducts);
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->hasTeamPermission($product->team, TeamPermission::ManageProducts);
    }
}
