<?php

namespace App\Http\Controllers;

use App\Enums\TeamPermission;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $team = $request->user()->currentTeam;

        $products = $team->products()
            ->where('active', true)
            ->orderBy('name')
            ->get()
            ->map(fn (Product $product) => [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'size' => $product->size,
                'price' => $product->price,
                'thumbnail' => $product->getFirstMediaUrl('images'),
            ]);

        return Inertia::render('Dashboard', [
            'products' => $products,
            'canManageProducts' => $request->user()->hasTeamPermission($team, TeamPermission::ManageProducts),
        ]);
    }
}
