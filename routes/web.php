<?php

use App\Http\Controllers\ContributionController;
use App\Http\Controllers\MoneyBoxController;
use App\Http\Controllers\PiggyBoxController;
use App\Http\Controllers\PiggyWebhookController;
use App\Http\Controllers\PublicBoxController;
use App\Http\Controllers\TrendiPayWebhookController;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use App\Livewire\Settings\Verification;
use App\Livewire\Settings\WithdrawalAccounts;
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

// Campaigns/Boxes webhook route (server-to-server notification)
Route::put('/webhooks/trendipay', [TrendiPayWebhookController::class, 'handle'])->name('trendipay.webhook');

// Personal Piggies gifts webhook route (server-to-server notification)
Route::put('/webhooks/piggy', [PiggyWebhookController::class, 'handle'])->name('piggy.webhook');

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
    Route::get('/money-boxes/{moneyBox}/download-qr', [MoneyBoxController::class, 'downloadQrCode'])
        ->name('money-boxes.download-qr');
    Route::post('/money-boxes/{moneyBox}/upload-media', [MoneyBoxController::class, 'uploadMedia'])
        ->name('money-boxes.upload-media');

    // Money Box Withdrawal Routes
    Route::get('/money-boxes/{moneyBox}/withdraw', [App\Http\Controllers\MoneyBoxWithdrawalController::class, 'create'])
        ->name('money-boxes.withdraw.create');
    Route::post('/money-boxes/{moneyBox}/withdraw', [App\Http\Controllers\MoneyBoxWithdrawalController::class, 'store'])
        ->name('money-boxes.withdraw.store');

    // Piggy Box Routes (Authenticated)
    Route::get('/my-piggy-box', [PiggyBoxController::class, 'myPiggyBox'])
        ->name('piggy.my-piggy-box');
    Route::post('/my-piggy-box/generate-qr', [PiggyBoxController::class, 'generateQrCode'])
        ->name('piggy.generate-qr');
    Route::get('/my-piggy-box/download-qr', [PiggyBoxController::class, 'downloadQrCode'])
        ->name('piggy.download-qr');

    // Piggy Box Withdrawal Routes
    Route::get('/my-piggy-box/withdraw', [App\Http\Controllers\PiggyBoxWithdrawalController::class, 'create'])
        ->name('piggy.withdraw.create');
    Route::post('/my-piggy-box/withdraw', [App\Http\Controllers\PiggyBoxWithdrawalController::class, 'store'])
        ->name('piggy.withdraw.store');

    // Settings Routes
    Route::redirect('settings', 'settings/profile');
    Route::get('settings/profile', Profile::class)->name('profile.edit');
    Route::get('settings/password', Password::class)->name('user-password.edit');
    Route::get('settings/verification', Verification::class)->name('settings.verification');
    Route::get('settings/withdrawal-accounts', WithdrawalAccounts::class)->name('settings.withdrawal-accounts');
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
