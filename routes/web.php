<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Products\ProductController;
use App\Http\Controllers\Products\PublicProductController;
use App\Http\Controllers\Teams\TeamInvitationController;
use App\Http\Controllers\WelcomeController;
use App\Http\Middleware\EnsureTeamMembership;
use App\Http\Middleware\EnsureUserIsActive;
use Illuminate\Support\Facades\Route;

Route::get('/', WelcomeController::class)->name('home');

Route::get('products/{product}', [PublicProductController::class, 'show'])->name('products.public.show');

Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureUserIsActive::class, EnsureTeamMembership::class.':admin'])
    ->group(function () {
        Route::get('dashboard', DashboardController::class)->name('dashboard');

        Route::resource('products', ProductController::class)->except(['show']);

        Route::prefix('admin')->name('admin.')->group(function () {
            Route::get('users', [UserController::class, 'index'])->name('users.index');
            Route::patch('users/{user}/toggle', [UserController::class, 'toggle'])->name('users.toggle');
        });
    });

Route::middleware(['auth', EnsureUserIsActive::class])->group(function () {
    Route::get('invitations/{invitation}/accept', [TeamInvitationController::class, 'accept'])->name('invitations.accept');
});

require __DIR__.'/settings.php';
