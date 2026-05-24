<?php

use App\Http\Controllers\ContributionController;
use App\Http\Controllers\EventBoxController;
use App\Http\Controllers\EventBoxValidationController;
use App\Http\Controllers\MoneyBoxController;
use App\Http\Controllers\PiggyBoxController;
use App\Http\Controllers\PiggyWebhookController;
use App\Http\Controllers\PublicBoxController;
use App\Http\Controllers\TrendiPayWebhookController;
use App\Http\Controllers\UserWithdrawalController;
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
Route::get('/embed/box/{slug}', [PublicBoxController::class, 'embed'])
    ->name('box.embed')
    ->withoutMiddleware(\Illuminate\Http\Middleware\FrameGuard::class);

// Static Pages
Route::view('/about', 'pages.about')->name('about');
Route::view('/terms', 'pages.terms')->name('terms');
Route::view('/privacy', 'pages.privacy')->name('privacy');
Route::view('/careers', 'pages.careers')->name('careers');
Route::view('/press', 'pages.press')->name('press');
Route::view('/contact', 'pages.contact')->name('contact');
Route::view('/security', 'pages.security')->name('security');
Route::view('/status', 'pages.status')->name('status');
Route::view('/cookies', 'pages.cookies')->name('cookies');

// EventBox public routes
Route::get('/events', [EventBoxController::class, 'publicIndex'])->name('events.public.index');
Route::get('/events/{slug}', [EventBoxController::class, 'publicShow'])->name('events.show')
    ->where('slug', '^(?!create$|edit$|dashboard$)[^/]+$');
Route::post('/events/{slug}/purchase', [EventBoxController::class, 'purchase'])->name('events.purchase');
Route::get('/events/{slug}/confirmation/{reference}', [EventBoxController::class, 'confirmation'])->name('events.confirmation');

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

    // EventBox (authenticated owner) routes
    Route::get('/my-events', [EventBoxController::class, 'index'])->name('events.index');
    Route::resource('events', EventBoxController::class)->parameters(['events' => 'eventBox'])->except(['show', 'index']);
    Route::get('/events/{eventBox}/dashboard', [EventBoxController::class, 'eventDashboard'])->name('events.dashboard');
    Route::post('/events/{eventBox}/status', [EventBoxController::class, 'updateStatus'])->name('events.status');
    Route::post('/events/{eventBox}/tickets/validate', [EventBoxValidationController::class, 'validate'])->name('events.tickets.validate');
    Route::post('/events/{eventBox}/tickets/{ticket}/redeem', [EventBoxValidationController::class, 'redeem'])->name('events.tickets.redeem');
    Route::delete('/events/{eventBox}/gallery/{mediaId}', [EventBoxController::class, 'removeGalleryImage'])->name('events.gallery.remove');

    // Contributors & Analytics
    Route::get('/contributors', [MoneyBoxController::class, 'contributors'])->name('contributors.index');
    Route::get('/analytics', [MoneyBoxController::class, 'analytics'])->name('analytics.index');

    // User Withdrawal History
    Route::get('/withdrawals', [UserWithdrawalController::class, 'index'])->name('withdrawals.index');
    Route::get('/withdrawals/{type}/{withdrawal}', [UserWithdrawalController::class, 'show'])->name('withdrawals.show');
    Route::post('/withdrawals/{type}/{withdrawal}/notes', [UserWithdrawalController::class, 'storeNote'])->name('withdrawals.notes.store');

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

    // Piggy Wallet Routes (Authenticated)
    Route::redirect('/my-piggy-box', '/my-piggy-wallet', 301);
    Route::redirect('/my-piggy-box/withdraw', '/my-piggy-wallet/withdraw', 301);

    Route::get('/my-piggy-wallet', [PiggyBoxController::class, 'myPiggyBox'])
        ->name('piggy.my-piggy-box');
    Route::post('/my-piggy-wallet/generate-qr', [PiggyBoxController::class, 'generateQrCode'])
        ->name('piggy.generate-qr');
    Route::get('/my-piggy-wallet/download-qr', [PiggyBoxController::class, 'downloadQrCode'])
        ->name('piggy.download-qr');

    // Piggy Wallet Withdrawal Routes
    Route::get('/my-piggy-wallet/withdraw', [App\Http\Controllers\PiggyBoxWithdrawalController::class, 'create'])
        ->name('piggy.withdraw.create');
    Route::post('/my-piggy-wallet/withdraw', [App\Http\Controllers\PiggyBoxWithdrawalController::class, 'store'])
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
