<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Socialite\Facades\Socialite;

use App\Http\Controllers\Admin\DemoController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\PartnerController;
use App\Http\Controllers\Admin\SettingsController;

use App\Http\Controllers\Shared\StripeWebhookController;
use App\Http\Controllers\Shared\UserProfileController;

use App\Http\Controllers\Partner\DashboardController as PartnerDashboardController;
use App\Http\Controllers\Partner\PricingController as PartnerPricingController;
use App\Http\Controllers\Partner\SubscriptionController as PartnerSubscriptionController;
use App\Http\Controllers\Partner\PartnerSettingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/auth/google', function () {
    return Socialite::driver('google')->redirect();
})->name('auth.google');
Route::get('/auth/google-callback', [UserProfileController::class, 'googleAuth']);
Route::post('/stripe/webhook', [StripeWebhookController::class, 'webhook']);

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
})->name('root');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    
    //ADMIN
    Route::middleware(['auth.role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/demo', [DemoController::class, 'index'])->name('demo');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::controller(PartnerController::class)->name('partners.')->group(function () {
            Route::get('/partners', 'index')->name('index');
            Route::get('/partners/{id}', 'show')->name('show');
            Route::get('/partners/{id}/edit', 'edit')->name('edit');
            Route::put('/partners/{id}', 'update')->name('update');
            Route::delete('/partners/{id}', 'destroy')->name('destroy');
            Route::post('/partners', 'store')->name('store');
        });
        Route::get('/partners-performance', [PartnerController::class, 'performanceIndex'])->name('partners.performance.index');
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
        //this can be resource controller, listing methods for clarity
        //implicit binding
        Route::controller(PackageController::class)->name('packages.')->group(function () {
            Route::get('/packages', 'index')->name('index');
            Route::get('/packages/create', 'create')->name('create');
            Route::post('/packages', 'store')->name('store');
            Route::get('/packages/{package}', 'show')->name('show');
            Route::get('/packages/{package}/edit', 'edit')->name('edit');
            Route::put('/packages/{package}', 'update')->name('update');
            Route::delete('/packages/{package}', 'destroy')->name('destroy');
        });
    });

    //PARTNER
    Route::middleware(['auth.role:partner'])->name('partner.')->group(function () {
        Route::get('/dashboard', [PartnerDashboardController::class, 'index'])->name('dashboard');
        Route::get('/pricing', [PartnerPricingController::class, 'index'])->name('pricing.index');

        Route::get('/subscriptions', [PartnerSubscriptionController::class, 'index'])->name('subscriptions.index');
        Route::put('/subscriptions/{subscription}/cancel', [PartnerSubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
        Route::post('/subscriptions/{package}/store', [PartnerSubscriptionController::class, 'store'])->name('subscriptions.store');

        Route::get('/contact-us', [PartnerDashboardController::class, 'index'])->name('contact.index');

        Route::get('/settings', [PartnerSettingController::class, 'index'])->name('settings.index');
        Route::get('/settings/general-details', [PartnerSettingController::class, 'generalDetails'])->name('settings.general-details.show');
        Route::put('/settings/general-details', [PartnerSettingController::class, 'generalDetailsUpdate'])->name('settings.general-details.update');
        Route::get('/settings/general-address', [PartnerSettingController::class, 'generalAddress'])->name('settings.general-address.show');
        Route::put('/settings/general-address', [PartnerSettingController::class, 'generalAddressUpdate'])->name('settings.general-address.update');
        Route::get('/settings/general-formats', [PartnerSettingController::class, 'generalFormats'])->name('settings.general-formats.show');
        Route::put('/settings/general-formats', [PartnerSettingController::class, 'generalFormatsUpdate'])->name('settings.general-formats.update');
    });

});
