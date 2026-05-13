<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Team;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Fortify\Features;

class WelcomeController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $storeTeam = Team::where('is_store', true)->first();

        $products = $storeTeam
            ? $storeTeam->products()
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
                ])
            : collect();

        return Inertia::render('Welcome', [
            'canRegister' => Features::enabled(Features::registration()),
            'products' => $products,
            'storeTeamSlug' => $storeTeam?->slug,
        ]);
    }
}
