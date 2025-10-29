<?php

use App\Http\Controllers\ContributionController;
use App\Http\Controllers\MoneyBoxController;
use App\Http\Controllers\PublicBoxController;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

// Public Routes
Route::get('/', [PublicBoxController::class, 'home'])->name('home');
Route::get('/browse', [PublicBoxController::class, 'index'])->name('browse');
Route::get('/box/{slug}', [PublicBoxController::class, 'show'])->name('box.show');

// Contribution Routes (Public)
Route::post('/box/{slug}/contribute', [ContributionController::class, 'store'])->name('box.contribute');
Route::get('/contributions/callback', [ContributionController::class, 'callback'])->name('contributions.callback');
Route::post('/contributions/webhook', [ContributionController::class, 'webhook'])->name('contributions.webhook');

// Authenticated Routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [MoneyBoxController::class, 'dashboard'])->name('dashboard');

    // Money Box Resource Routes
    Route::resource('money-boxes', MoneyBoxController::class);

    // Additional Money Box Routes
    Route::get('/money-boxes/{moneyBox}/statistics', [MoneyBoxController::class, 'statistics'])
        ->name('money-boxes.statistics');
    Route::get('/money-boxes/{moneyBox}/share', [MoneyBoxController::class, 'share'])
        ->name('money-boxes.share');

    // Settings Routes
    Route::redirect('settings', 'settings/profile');
    Route::get('settings/profile', Profile::class)->name('profile.edit');
    Route::get('settings/password', Password::class)->name('user-password.edit');
    Route::get('settings/appearance', Appearance::class)->name('appearance.edit');

    Route::get('settings/two-factor', TwoFactor::class)
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});
