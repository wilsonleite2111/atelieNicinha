<?php

namespace App\Http\Controllers;

use App\Enums\TeamPermission;
use App\Models\Product;
use App\Models\Team;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request, Team $current_team): Response
    {
        $products = $current_team->products()
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
            'canManageProducts' => $request->user()->hasTeamPermission($current_team, TeamPermission::ManageProducts),
        ]);
    }
}
