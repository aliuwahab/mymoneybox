<?php

use App\Http\Controllers\ContributionController;
use App\Http\Controllers\MoneyBoxController;
use App\Http\Controllers\PiggyBoxController;
use App\Http\Controllers\PublicBoxController;
use App\Http\Controllers\TrendiPayWebhookController;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use App\Livewire\Settings\Verification;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

// Public Routes
Route::get('/', [PublicBoxController::class, 'home'])->name('home');
Route::get('/browse', [PublicBoxController::class, 'index'])->name('browse');
Route::get('/box/{slug}', [PublicBoxController::class, 'show'])->name('box.show');

// Static Pages
Route::view('/about', 'pages.about')->name('about');
Route::view('/terms', 'pages.terms')->name('terms');
Route::view('/privacy', 'pages.privacy')->name('privacy');

// Contribution Routes (Public)
Route::post('/box/{slug}/contribute', [ContributionController::class, 'store'])->name('box.contribute');

// TrendiPay-specific routes
Route::get('/contributions/trendipay/return', [TrendiPayWebhookController::class, 'callback'])->name('trendipay.return');
Route::put('/webhooks/trendipay', [TrendiPayWebhookController::class, 'handle'])->name('trendipay.webhook');

// Piggy Box Routes (Public)
Route::get('/piggy-someone', [PiggyBoxController::class, 'lookup'])->name('piggy.lookup');
Route::post('/piggy-someone/find', [PiggyBoxController::class, 'findByCode'])->name('piggy.find');
Route::get('/piggy/{code}', [PiggyBoxController::class, 'showByCode'])->name('piggy.show');
Route::post('/piggy/{user}/donate', [PiggyBoxController::class, 'donate'])->name('piggy.donate');
Route::get('/piggy/callback', [PiggyBoxController::class, 'callback'])->name('piggy.callback');

// Webhook Routes (Provider-Specific)

// Authenticated Routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [MoneyBoxController::class, 'dashboard'])->name('dashboard');

    // Piggy Box Resource Routes
    Route::resource('money-boxes', MoneyBoxController::class);

    // Additional Piggy Box Routes
    Route::get('/money-boxes/{moneyBox}/statistics', [MoneyBoxController::class, 'statistics'])
        ->name('money-boxes.statistics');
    Route::get('/money-boxes/{moneyBox}/share', [MoneyBoxController::class, 'share'])
        ->name('money-boxes.share');
    Route::post('/money-boxes/{moneyBox}/generate-qr', [MoneyBoxController::class, 'generateQrCode'])
        ->name('money-boxes.generate-qr');
    Route::post('/money-boxes/{moneyBox}/upload-media', [MoneyBoxController::class, 'uploadMedia'])
        ->name('money-boxes.upload-media');

    // Piggy Box Routes (Authenticated)
    Route::get('/my-piggy-box', [PiggyBoxController::class, 'myPiggyBox'])
        ->name('piggy.my-piggy-box');

    // Settings Routes
    Route::redirect('settings', 'settings/profile');
    Route::get('settings/profile', Profile::class)->name('profile.edit');
    Route::get('settings/password', Password::class)->name('user-password.edit');
    Route::get('settings/verification', Verification::class)->name('settings.verification');
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
