<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Socialite\Facades\Socialite;

use App\Http\Controllers\Admin\DemoController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\PartnerController;
use App\Http\Controllers\Admin\InstanceController;
use App\Http\Controllers\Admin\SettingsController;

use App\Http\Controllers\Shared\StripeWebhookController;
use App\Http\Controllers\Shared\UserProfileController;

use App\Http\Controllers\Partner\BusinessSettingController;
use App\Http\Controllers\Partner\PartnerDashboardController;
use App\Http\Controllers\Partner\PartnerSubscriptionController;
use App\Http\Controllers\Partner\PartnerMemberController;
use App\Http\Controllers\Partner\PartnerInstructorController;
use App\Http\Controllers\Partner\PartnerAmenityController;
use App\Http\Controllers\Partner\PartnerClassLessonController;
use App\Http\Controllers\Partner\PartnerStudioController;
use App\Http\Controllers\Partner\PartnerClassTypeController;
use App\Http\Controllers\Partner\PartnerExportController;

// Service store area, partner subdomains:
use App\Http\Controllers\Store\StorePublicController;

Route::get('/auth/google', function () {
    return Socialite::driver('google')->redirect();
})->name('auth.google');
Route::get('/auth/google-callback', [UserProfileController::class, 'googleAuth']);
Route::post('/stripe/webhook', [StripeWebhookController::class, 'webhook']);
// https://app.fittr.tech/stripe/connect-redirect

// this is for fittr admin users and partner users
Route::domain('app.'.config('app.domain'))->group(function () {
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
            Route::controller(InstanceController::class)->name('instances.')->group(function () {
                Route::get('/instances', 'index')->name('index');
                Route::get('/instances/{name}', 'show')->name('show');
                Route::get('/instances/{name}/{metric}', 'showMetric')->name('show_metric');
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
        // \App\Http\Middleware\ConnectPartnerDatabase::class runs on every request, see App\Http\Kernel $middlewareGroups
        // ConnectPartnerDatabase must run before SubstituteBindings in order for implicit model bindings to work
    
        Route::middleware(['auth.role:partner'])->name('partner.')->group(function () {
            Route::get('/dashboard', [PartnerDashboardController::class, 'index'])->name('dashboard');
    
            Route::get('/subscriptions', [PartnerSubscriptionController::class, 'index'])->name('subscriptions.index');
            Route::put('/subscriptions/{subscription}/cancel', [PartnerSubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
            Route::post('/subscriptions/{package}/store', [PartnerSubscriptionController::class, 'store'])->name('subscriptions.store');
    
            Route::get('/contact-us', [PartnerDashboardController::class, 'index'])->name('contact.index');
    
            Route::get('/settings', [BusinessSettingController::class, 'index'])->name('settings.index');
            Route::get('/settings/general-details', [BusinessSettingController::class, 'generalDetails'])->name('settings.general-details');
            Route::put('/settings/general-details', [BusinessSettingController::class, 'generalDetailsUpdate']);
            Route::get('/settings/general-address', [BusinessSettingController::class, 'generalAddress'])->name('settings.general-address');
            Route::put('/settings/general-address', [BusinessSettingController::class, 'generalAddressUpdate']);
            Route::get('/settings/general-formats', [BusinessSettingController::class, 'generalFormats'])->name('settings.general-formats');
            Route::put('/settings/general-formats', [BusinessSettingController::class, 'generalFormatsUpdate']);
            Route::get('/settings/integrations', [BusinessSettingController::class, 'integrations'])->name('settings.integrations');
            Route::put('/settings/integrations', [BusinessSettingController::class, 'integrationsUpdate']);
    
            Route::get('/settings/service-store-general', [BusinessSettingController::class, 'serviceStoreGeneral'])->name('settings.service-store-general');
            Route::put('/settings/service-store-general', [BusinessSettingController::class, 'serviceStoreGeneralUpdate']);
            Route::get('/settings/service-store-header', [BusinessSettingController::class, 'serviceStoreHeader'])->name('settings.service-store-header');
            Route::put('/settings/service-store-header', [BusinessSettingController::class, 'serviceStoreHeaderUpdate']);
            Route::get('/settings/service-store-seo', [BusinessSettingController::class, 'serviceStoreSeo'])->name('settings.service-store-seo');
            Route::put('/settings/service-store-seo', [BusinessSettingController::class, 'serviceStoreSeoUpdate']);
            Route::get('/settings/service-store-code', [BusinessSettingController::class, 'serviceStoreCode'])->name('settings.service-store-code');
            Route::put('/settings/service-store-code', [BusinessSettingController::class, 'serviceStoreCodeUpdate']);
    
            // Route::get('/settings/service-store-widgets', [BusinessSettingController::class, 'serviceStoreWidgets'])->name('settings.service-store-widgets');
            // Route::put('/settings/service-store-widgets', [BusinessSettingController::class, 'serviceStoreWidgetsUpdate']);
            Route::get('/settings/service-store-waivers', [BusinessSettingController::class, 'serviceStoreWaivers'])->name('settings.service-store-waivers');
            Route::put('/settings/service-store-waivers', [BusinessSettingController::class, 'serviceStoreWaiversUpdate']);
            Route::get('/settings/payments', [BusinessSettingController::class, 'payments'])->name('settings.payments');
            Route::get('/settings/payments/stripe', [BusinessSettingController::class, 'paymentsStripe'])->name('settings.payments.stripe');
            Route::post('/settings/payments/stripe', [BusinessSettingController::class, 'connectStripe']);

            //partner.members.index
            //partner.members.destroy /members/{member}
            Route::resource('members', PartnerMemberController::class);
            Route::resource('instructors', PartnerInstructorController::class);
            Route::resource('amenity', PartnerAmenityController::class);
            Route::resource('classes', PartnerClassLessonController::class);
            Route::resource('studios', PartnerStudioController::class);
            Route::resource('classtypes', PartnerClassTypeController::class);

            Route::get('/exports', [PartnerExportController::class, 'index'])->name('exports.index');
            Route::get('/exports/{export}', [PartnerExportController::class, 'show'])->name('exports.show');
            Route::delete('/exports/{export}', [PartnerExportController::class, 'destroy'])->name('exports.destroy');
            Route::post('/exports', [PartnerExportController::class, 'store']);
        });
    
    });
});

// this is for partner members and instructors, public service store or logged in area
// AuthenticateSubdomain middleware will check if such subdomain exist to prevent random access and will set proper db connection (in addition to) 'database.connections.mysql_partner'
// All routes have prefix ss. (short for service store)
Route::domain('{subdomain}.'.config('app.domain'))->middleware(['auth.subdomain'])->name('ss.')->group(function () {

    Route::get('/', [StorePublicController::class, 'index'])->name('home');

    // Route::get('/', function ($subdomain) {
    //     //temp demo
    //     // dump(Config::get('database.connections.mysql_partner'));
    //     dump('Partner classes dump:');
    //     $partner_classes = \App\Models\Partner\ClassLesson::all();
    //     dump($partner_classes);
    //     return "THIS IS CLIENT PARTNER PUBLIC FACING SERVICE STORE PAGE. Subdomain name is $subdomain";
    // });

    Route::middleware([
        'auth:sanctum',
        config('jetstream.auth_session'),
        'verified',
    ])->group(function () {

        //MEMBER
        Route::middleware(['auth.role:member'])->name('member.')->group(function () {
            // Route::get('/dashboard', [MemberDashboardController::class, 'index'])->name('dashboard');
            // Route::get('/memberships', [MembershipController::class, 'index'])->name('memberships');
        });

        //INSTRUCTOR
        Route::middleware(['auth.role:instructor'])->prefix('instructor')->name('instructor.')->group(function () {
            Route::get('/dashboard', function () {
                return 'Hello World';
            })->name('dashboard');
        });
    });
});
