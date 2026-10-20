<?php

namespace App\Http\Controllers\Categories;

use App\Enums\TeamPermission;
use App\Http\Controllers\Controller;
use App\Http\Requests\Categories\SaveCategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    public function index(Request $request): Response
    {
        $team = $request->user()->currentTeam;

        Gate::authorize('viewAny', [Category::class, $team]);

        $categories = $team->categories()
            ->orderBy('name')
            ->get()
            ->map(fn (Category $c) => [
                'id' => $c->id,
                'name' => $c->name,
                'active' => $c->active,
            ]);

        return Inertia::render('categories/Index', [
            'categories' => $categories,
            'canManageCategories' => $request->user()->hasTeamPermission($team, TeamPermission::ManageProducts),
        ]);
    }

    public function create(Request $request): Response
    {
        $team = $request->user()->currentTeam;

        Gate::authorize('create', [Category::class, $team]);

        return Inertia::render('categories/Create');
    }

    public function store(SaveCategoryRequest $request): RedirectResponse
    {
        $team = $request->user()->currentTeam;

        Gate::authorize('create', [Category::class, $team]);

        $team->categories()->create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Categoria criada.']);

        return to_route('categories.index');
    }

    public function edit(Request $request, Category $category): Response
    {
        $team = $request->user()->currentTeam;

        abort_unless($category->team_id === $team->id, 404);

        Gate::authorize('update', $category);

        return Inertia::render('categories/Edit', [
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'active' => $category->active,
            ],
        ]);
    }

    public function update(SaveCategoryRequest $request, Category $category): RedirectResponse
    {
        $team = $request->user()->currentTeam;

        abort_unless($category->team_id === $team->id, 404);

        Gate::authorize('update', $category);

        $category->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Categoria atualizada.']);

        return to_route('categories.index');
    }

    public function destroy(Request $request, Category $category): RedirectResponse
    {
        $team = $request->user()->currentTeam;

        abort_unless($category->team_id === $team->id, 404);

        Gate::authorize('delete', $category);

        $category->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Categoria excluída.']);

        return to_route('categories.index');
    }
}
