<?php

namespace App\Http\Controllers\Categories;

use App\Enums\TeamPermission;
use App\Http\Controllers\Controller;
use App\Http\Requests\Categories\SaveCategoryRequest;
use App\Models\Category;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    public function index(Request $request, Team $current_team): Response
    {
        Gate::authorize('viewAny', [Category::class, $current_team]);

        $categories = $current_team->categories()
            ->orderBy('name')
            ->get()
            ->map(fn (Category $c) => [
                'id' => $c->id,
                'name' => $c->name,
                'active' => $c->active,
            ]);

        return Inertia::render('categories/Index', [
            'categories' => $categories,
            'canManageCategories' => $request->user()->hasTeamPermission($current_team, TeamPermission::ManageProducts),
        ]);
    }

    public function create(Request $request, Team $current_team): Response
    {
        Gate::authorize('create', [Category::class, $current_team]);

        return Inertia::render('categories/Create');
    }

    public function store(SaveCategoryRequest $request, Team $current_team): RedirectResponse
    {
        Gate::authorize('create', [Category::class, $current_team]);

        $current_team->categories()->create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Categoria criada.']);

        return to_route('categories.index', ['current_team' => $current_team->slug]);
    }

    public function edit(Request $request, Team $current_team, Category $category): Response
    {
        abort_unless($category->team_id === $current_team->id, 404);

        Gate::authorize('update', $category);

        return Inertia::render('categories/Edit', [
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'active' => $category->active,
            ],
        ]);
    }

    public function update(SaveCategoryRequest $request, Team $current_team, Category $category): RedirectResponse
    {
        abort_unless($category->team_id === $current_team->id, 404);

        Gate::authorize('update', $category);

        $category->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Categoria atualizada.']);

        return to_route('categories.index', ['current_team' => $current_team->slug]);
    }

    public function destroy(Request $request, Team $current_team, Category $category): RedirectResponse
    {
        abort_unless($category->team_id === $current_team->id, 404);

        Gate::authorize('delete', $category);

        $category->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Categoria excluída.']);

        return to_route('categories.index', ['current_team' => $current_team->slug]);
    }
}
