<?php

use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\SecurityController;
use App\Http\Controllers\Teams\TeamController;
use App\Http\Controllers\Teams\TeamInvitationController;
use App\Http\Controllers\Teams\TeamMemberController;
use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/configuracoes')->group(function () {
    Route::middleware(['auth'])->group(function () {
        Route::redirect('/', '/admin/configuracoes/perfil');

        Route::get('perfil', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('perfil', [ProfileController::class, 'update'])->name('profile.update');
    });

    Route::middleware(['auth', 'verified'])->group(function () {
        Route::delete('perfil', [ProfileController::class, 'destroy'])->name('profile.destroy');

        Route::get('seguranca', [SecurityController::class, 'edit'])->name('security.edit');

        Route::put('senha', [SecurityController::class, 'update'])
            ->middleware('throttle:6,1')
            ->name('user-password.update');

        Route::inertia('aparencia', 'settings/Appearance')->name('appearance.edit');

        Route::middleware(EnsureTeamMembership::class)->group(function () {
            Route::get('equipe', [TeamController::class, 'edit'])->name('teams.edit');
            Route::patch('equipe', [TeamController::class, 'update'])->name('teams.update');

            Route::patch('equipe/membros/{user}', [TeamMemberController::class, 'update'])->name('teams.members.update');
            Route::delete('equipe/membros/{user}', [TeamMemberController::class, 'destroy'])->name('teams.members.destroy');

            Route::post('equipe/convites', [TeamInvitationController::class, 'store'])->name('teams.invitations.store');
            Route::delete('equipe/convites/{invitation}', [TeamInvitationController::class, 'destroy'])->name('teams.invitations.destroy');
        });
    });
});
