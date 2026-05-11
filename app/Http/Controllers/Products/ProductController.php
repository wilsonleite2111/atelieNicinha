<?php

namespace App\Http\Controllers\Products;

use App\Enums\TeamPermission;
use App\Http\Controllers\Controller;
use App\Http\Requests\Products\SaveProductRequest;
use App\Models\Product;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(Request $request, Team $current_team): Response
    {
        Gate::authorize('viewAny', [Product::class, $current_team]);

        $search = (string) $request->query('search', '');

        $query = $current_team->products()->orderBy('name');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $products = $query->get()->map(fn (Product $product) => [
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'price' => $product->price,
            'sku' => $product->sku,
            'active' => $product->active,
            'thumbnail' => $product->getFirstMediaUrl('images'),
        ]);

        return Inertia::render('products/Index', [
            'products' => $products,
            'search' => $search,
            'canManageProducts' => $request->user()->hasTeamPermission($current_team, TeamPermission::ManageProducts),
        ]);
    }

    public function create(Request $request, Team $current_team): Response
    {
        Gate::authorize('create', [Product::class, $current_team]);

        return Inertia::render('products/Create');
    }

    public function store(SaveProductRequest $request, Team $current_team): RedirectResponse
    {
        Gate::authorize('create', [Product::class, $current_team]);

        $product = $current_team->products()->create(
            $request->safe()->except(['images', 'delete_images'])
        );

        foreach ($request->file('images', []) as $file) {
            $product->addMedia($file)->toMediaCollection('images');
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Product created.')]);

        return to_route('products.index', ['current_team' => $current_team->slug]);
    }

    public function edit(Request $request, Team $current_team, Product $product): Response
    {
        abort_unless($product->team_id === $current_team->id, 404);

        Gate::authorize('update', $product);

        return Inertia::render('products/Edit', [
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'sku' => $product->sku,
                'active' => $product->active,
                'images' => $product->getMedia('images')->map(fn ($m) => [
                    'id' => $m->id,
                    'url' => $m->getUrl(),
                ]),
            ],
        ]);
    }

    public function update(SaveProductRequest $request, Team $current_team, Product $product): RedirectResponse
    {
        abort_unless($product->team_id === $current_team->id, 404);

        Gate::authorize('update', $product);

        $product->update($request->safe()->except(['images', 'delete_images']));

        $product->media()
            ->whereIn('id', $request->input('delete_images', []))
            ->get()
            ->each(fn ($m) => $m->delete());

        foreach ($request->file('images', []) as $file) {
            $product->addMedia($file)->toMediaCollection('images');
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Product updated.')]);

        return to_route('products.index', ['current_team' => $current_team->slug]);
    }

    public function destroy(Request $request, Team $current_team, Product $product): RedirectResponse
    {
        abort_unless($product->team_id === $current_team->id, 404);

        Gate::authorize('delete', $product);

        $product->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Product deleted.')]);

        return to_route('products.index', ['current_team' => $current_team->slug]);
    }
}
