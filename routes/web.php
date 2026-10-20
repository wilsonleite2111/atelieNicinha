<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Categories\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Products\ProductController;
use App\Http\Controllers\Products\PublicProductController;
use App\Http\Controllers\Teams\TeamInvitationController;
use App\Http\Controllers\WelcomeController;
use App\Http\Middleware\EnsureTeamMembership;
use App\Http\Middleware\EnsureUserIsActive;
use Illuminate\Support\Facades\Route;

Route::get('/', WelcomeController::class)->name('home');

Route::get('produtos/{product}', [PublicProductController::class, 'show'])->name('products.public.show');

Route::prefix('admin')
    ->middleware(['auth', 'verified', EnsureUserIsActive::class, EnsureTeamMembership::class.':admin'])
    ->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');

        Route::resource('produtos', ProductController::class)
            ->except(['show'])
            ->names('products')
            ->parameters(['produtos' => 'product']);

        Route::resource('categorias', CategoryController::class)
            ->except(['show'])
            ->names('categories')
            ->parameters(['categorias' => 'category']);

        Route::name('admin.')->group(function () {
            Route::get('usuarios', [UserController::class, 'index'])->name('users.index');
            Route::patch('usuarios/{user}/alternar-status', [UserController::class, 'toggle'])->name('users.toggle');
        });
    });

Route::middleware(['auth', EnsureUserIsActive::class])->group(function () {
    Route::get('convites/{invitation}/aceitar', [TeamInvitationController::class, 'accept'])->name('invitations.accept');
});

require __DIR__.'/settings.php';
