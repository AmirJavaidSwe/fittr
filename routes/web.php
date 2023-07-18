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

use App\Http\Controllers\Shared\RoleController;
use App\Http\Controllers\Shared\StripeWebhookController;
use App\Http\Controllers\Shared\UserProfileController;

use App\Http\Controllers\Partner\BusinessSettingController;
use App\Http\Controllers\Partner\PartnerAmenityController;
use App\Http\Controllers\Partner\PartnerClassLessonController;
use App\Http\Controllers\Partner\PartnerClassTypeController;
use App\Http\Controllers\Partner\PartnerDashboardController;
use App\Http\Controllers\Partner\PartnerExportController;
use App\Http\Controllers\Partner\PartnerInstructorController;
use App\Http\Controllers\Partner\PartnerLocationController;
use App\Http\Controllers\Partner\PartnerMemberController;
use App\Http\Controllers\Partner\PartnerOnboardController;
use App\Http\Controllers\Partner\PartnerPackController;
use App\Http\Controllers\Partner\PartnerStudioController;
use App\Http\Controllers\Partner\PartnerSubscriptionController;
use App\Http\Controllers\Partner\PartnerUserController;

// Service store area, partner subdomains:
use App\Http\Controllers\Store\StorePublicController;
use App\Http\Controllers\Store\StoreClassController;
use App\Http\Controllers\Store\StoreInstructorController;
use App\Http\Controllers\Store\StoreLocationController;
use App\Http\Controllers\Store\StorePackController;
use App\Http\Controllers\Store\StorePaymentController;
use App\Http\Controllers\Store\MemberDashboardController;
use App\Http\Controllers\Store\InstructorDashboardController;
use App\Http\Controllers\Store\StoreBookingController;

Route::get('/auth/google', [UserProfileController::class, 'googleRedirect'])->middleware(['guest:'.config('fortify.guard')])->name('auth.google');
Route::get('/auth/google-callback', [UserProfileController::class, 'googleAuth']);
Route::post('/stripe/webhook', [StripeWebhookController::class, 'webhook']);

//Routes to complete partner onboarding. Accessible and auto-redirected to from 'ConnectPartnerDatabase' middleware when user has no business relation.
Route::middleware(['auth', 'auth.source:partner', 'verified'])->name('partner.onboarding.')->group(function () {
    Route::get('/onboarding', [PartnerOnboardController::class, 'index'])->name('index');
    Route::post('/onboarding', [PartnerOnboardController::class, 'update'])->name('update');
});

// this is for fittr admin users and partner users
Route::domain('app.'.config('app.domain'))->group(function () {
    Route::get('/', function () {
        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
        ]);
    })
    ->middleware(['guest:'.config('fortify.guard')])
    ->name('root');

    Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {

        //ADMIN
        Route::middleware(['auth.source:admin'])->prefix('admin')->name('admin.')->group(function () {
            Route::get('/demo', [DemoController::class, 'index'])->name('demo');
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
            Route::controller(PartnerController::class)->name('partners.')->group(function () {
                Route::get('/partners', 'index')->name('index');
                Route::get('/partners/{id}', 'show')->name('show');
                Route::get('/partners/{id}/edit', 'edit')->name('edit');
                Route::post('/partners/login-as', 'loginAsPartner')->name('login-as');
                // Route::post('/partners/login-back', 'loginBack')->name('login-back');
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
            Route::get('/settings/add-admin', [SettingsController::class, 'addAdmins'])->name('settings.add.admins');
            Route::post('/settings/save-admin', [SettingsController::class, 'saveAdmins'])->name('settings.save.admins');
            Route::get('/settings/edit-admin/{id}', [SettingsController::class, 'editAdmins'])->name('settings.edit.admins');
            Route::put('/settings/edit-admin/{id}', [SettingsController::class, 'updateAdmins'])->name('settings.update.admins');
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
            Route::resource('roles', RoleController::class);
        });

        //PARTNER
        Route::middleware(['auth.source:partner', 'partner.connect'])->name('partner.')->group(function () {
            Route::get('/dashboard', [PartnerDashboardController::class, 'index'])->name('dashboard');
            Route::post('/login-as', [PartnerDashboardController::class, 'loginAs'])->name('login-as');

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
            Route::get('/settings/fap', [BusinessSettingController::class, 'fap'])->name('settings.fap');
            Route::put('/settings/fap', [BusinessSettingController::class, 'fapUpdate']);
            Route::get('/settings/bookings', [BusinessSettingController::class, 'bookings'])->name('settings.bookings');
            Route::put('/settings/bookings', [BusinessSettingController::class, 'bookingsUpdate']);

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
            Route::resource('amenity', PartnerAmenityController::class);
            Route::resource('classes', PartnerClassLessonController::class);
            Route::resource('classtypes', PartnerClassTypeController::class);
            Route::resource('instructors', PartnerInstructorController::class);
            Route::resource('members', PartnerMemberController::class);
            Route::resource('roles', RoleController::class);
            Route::resource('studios', PartnerStudioController::class);
            Route::resource('users', PartnerUserController::class);

            Route::resource('packs', PartnerPackController::class);
            Route::post('/packs/{pack}/duplicate', [PartnerPackController::class, 'duplicate'])->name('packs.duplicate');
            Route::post('/packs/{pack}/price', [PartnerPackController::class, 'storePrice'])->name('packs.price.store');
            Route::put('/packs/price/{price}', [PartnerPackController::class, 'updatePrice'])->name('packs.price.update');

            Route::get('/exports', [PartnerExportController::class, 'index'])->name('exports.index');
            Route::get('/exports/{export}', [PartnerExportController::class, 'show'])->name('exports.show');
            Route::get('exports/download/{token}', [PartnerExportController::class, 'download'])->name('exports.download');
            Route::get('exports/download/request/{export}', [PartnerExportController::class, 'requestToDownload'])->name('exports.request-to-download');
            Route::delete('/exports/{export}', [PartnerExportController::class, 'destroy'])->name('exports.destroy');
            Route::post('/exports', [PartnerExportController::class, 'store']);

            Route::resource('locations', PartnerLocationController::class);
            Route::delete('/locations/{location}/delete-image', [PartnerLocationController::class, 'deleteImage'])->name('locations.delete-image');

        });

    });
});

// this is for partner members and instructors, public service store or logged in area
// AuthenticateSubdomain middleware will check if such subdomain exist to prevent random access and will set proper db connection (in addition to) 'database.connections.mysql_partner'
// All routes have prefix ss. (short for service store)
Route::domain('{subdomain}.'.config('app.domain'))->middleware(['auth.subdomain'])->name('ss.')->group(function () {

    Route::get('/', [StorePublicController::class, 'index'])->name('home');

    Route::get('/classes', [StoreClassController::class, 'index'])->name('classes.index');

    Route::get('/instructors', [StoreInstructorController::class, 'index'])->name('instructors.index');
    Route::get('/locations', [StoreLocationController::class, 'index'])->name('locations.index');
    Route::get('/memberships', [StorePackController::class, 'index'])->name('memberships.index');

    Route::post('/buy/{price}', [StorePaymentController::class, 'index'])->name('payments.index');
    Route::get('/success', [StorePaymentController::class, 'success'])->name('payments.success');

    //google auth
    Route::post('/auth/google-callback', [UserProfileController::class, 'processSubdomainRequest']);

    Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {

        //MEMBER
        Route::middleware(['auth.role:member'])->name('member.')->group(function () {
            Route::get('/dashboard', [MemberDashboardController::class, 'index'])->name('dashboard');

            Route::get('/bookings', [StoreBookingController::class, 'index'])->name('bookings.index');
            Route::post('/bookings', [StoreBookingController::class, 'store'])->name('bookings.store');
            Route::post('/bookings/cancel', [StoreBookingController::class, 'cancel'])->name('bookings.cancel');
        });

        //INSTRUCTOR
        Route::middleware(['auth.role:instructor'])->prefix('instructor')->name('instructor.')->group(function () {
            Route::get('/dashboard', [InstructorDashboardController::class, 'index'])->name('dashboard');
        });
    });
});
