<?php

namespace App\Http\Controllers\Products;

use App\Enums\TeamPermission;
use App\Http\Controllers\Controller;
use App\Http\Requests\Products\SaveProductRequest;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(Request $request): Response
    {
        $team = $request->user()->currentTeam;

        Gate::authorize('viewAny', [Product::class, $team]);

        $search = (string) $request->query('search', '');
        $category = (string) $request->query('category', '');

        $query = $team->products()->orderBy('name');

        if ($search) {
            $query->where('name', 'ilike', "%{$search}%");
        }

        if ($category === 'none') {
            $query->whereNull('category_id');
        } elseif (ctype_digit($category)) {
            $query->where('category_id', (int) $category);
        }

        $products = $query->get()->map(fn (Product $product) => [
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'price' => $product->price,
            'active' => $product->active,
            'thumbnail' => $product->getFirstMediaUrl('images'),
        ]);

        return Inertia::render('products/Index', [
            'products' => $products,
            'search' => $search,
            'category' => $category,
            'categories' => $team->categories()
                ->orderBy('name')
                ->get()
                ->map(fn ($c) => ['id' => $c->id, 'name' => $c->name])
                ->values(),
            'canManageProducts' => $request->user()->hasTeamPermission($team, TeamPermission::ManageProducts),
        ]);
    }

    public function create(Request $request): Response
    {
        $team = $request->user()->currentTeam;

        Gate::authorize('create', [Product::class, $team]);

        return Inertia::render('products/Create', [
            'categories' => $team->categories()
                ->where('active', true)
                ->orderBy('name')
                ->get()
                ->map(fn ($c) => ['id' => $c->id, 'name' => $c->name])
                ->values(),
        ]);
    }

    public function store(SaveProductRequest $request): RedirectResponse
    {
        $team = $request->user()->currentTeam;

        Gate::authorize('create', [Product::class, $team]);

        try {
            $product = $team->products()->create(
                $request->safe()->except(['images', 'delete_images'])
            );

            foreach ($request->file('images', []) as $file) {
                $product->addMedia($file)->toMediaCollection('images');
            }
        } catch (\Throwable $e) {
            Log::error('Product store failed', [
                'message' => $e->getMessage(),
                'team_id' => $team->id,
                'user_id' => $request->user()->id,
            ]);
            Inertia::flash('toast', ['type' => 'error', 'message' => 'Erro ao salvar o produto. Tente novamente.']);

            return back();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Produto criado.')]);

        return to_route('products.index');
    }

    public function edit(Request $request, Product $product): Response
    {
        $team = $request->user()->currentTeam;

        abort_unless($product->team_id === $team->id, 404);

        Gate::authorize('update', $product);

        return Inertia::render('products/Edit', [
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'size' => $product->size,
                'price' => $product->price,
                'active' => $product->active,
                'category_id' => $product->category_id,
                'images' => $product->getMedia('images')->map(fn ($m) => [
                    'id' => $m->id,
                    'url' => $m->getUrl(),
                ]),
            ],
            'categories' => $team->categories()
                ->where('active', true)
                ->orderBy('name')
                ->get()
                ->map(fn ($c) => ['id' => $c->id, 'name' => $c->name])
                ->values(),
        ]);
    }

    public function update(SaveProductRequest $request, Product $product): RedirectResponse
    {
        $team = $request->user()->currentTeam;

        abort_unless($product->team_id === $team->id, 404);

        Gate::authorize('update', $product);

        try {
            $product->update($request->safe()->except(['images', 'delete_images']));

            $product->media()
                ->whereIn('id', $request->input('delete_images', []))
                ->get()
                ->each(fn ($m) => $m->delete());

            foreach ($request->file('images', []) as $file) {
                $product->addMedia($file)->toMediaCollection('images');
            }
        } catch (\Throwable $e) {
            Log::error('Product update failed', [
                'message' => $e->getMessage(),
                'team_id' => $team->id,
                'product_id' => $product->id,
                'user_id' => $request->user()->id,
            ]);
            Inertia::flash('toast', ['type' => 'error', 'message' => 'Erro ao atualizar o produto. Tente novamente.']);

            return back();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Produto atualizado.')]);

        return to_route('products.index');
    }

    public function destroy(Request $request, Product $product): RedirectResponse
    {
        $team = $request->user()->currentTeam;

        abort_unless($product->team_id === $team->id, 404);

        Gate::authorize('delete', $product);

        $product->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Produto excluído.')]);

        return to_route('products.index');
    }
}
