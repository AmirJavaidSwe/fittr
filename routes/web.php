<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Admin\DemoController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PartnerController;
use App\Http\Controllers\Admin\SettingsController;

use App\Http\Controllers\Partner\DashboardController as PartnerDashboardController;

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

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
});

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
            Route::delete('/partners/{id}', 'update')->name('destroy');
            Route::post('/partners', 'store')->name('store');
        });
        Route::get('/partners-performance', [PartnerController::class, 'performanceIndex'])->name('partners.performance.index');
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    });

    //PARTNER
    Route::middleware(['auth.role:partner'])->name('partner.')->group(function () {
        Route::get('/dashboard', [PartnerDashboardController::class, 'index'])->name('dashboard');
    });

});
