<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\MinecraftVerificationController;
use App\Http\Controllers\Portal\DashboardController;
use App\Http\Controllers\Portal\Admin\AdminUserController;
use App\Http\Controllers\Portal\Admin\AdminRoleController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\BrandingController;
use App\Http\Controllers\Portal\Admin\PermitTypeController;
use App\Http\Controllers\Portal\PermitsController;
use App\Http\Controllers\Portal\PermitRequestManagementController;
use App\Http\Controllers\Portal\CompaniesController;
use App\Http\Controllers\Portal\Admin\CompanyTypeController;
use App\Http\Controllers\Portal\CompanyRequestManagementController;
use App\Http\Controllers\Portal\Admin\DissolutionRequestManagementController;
use App\Http\Controllers\Portal\CompanyRegistryController;
use App\Http\Controllers\Portal\CriminalRecordController;
use App\Http\Controllers\Portal\Police\PlayerDatabaseController;
use App\Http\Controllers\Portal\BankAccountController;
use App\Http\Controllers\Portal\PlotController;
use App\Http\Controllers\Portal\PlotListingController;
use App\Http\Controllers\Portal\PlotRentalController;
use App\Http\Controllers\Portal\PlotRentalOfferController;
use App\Http\Controllers\Portal\BankTransactionController;
use App\Http\Controllers\Portal\Admin\AdminPlotController;
use App\Http\Controllers\Portal\Admin\AdminPlotListingController;
use App\Http\Controllers\Portal\Admin\AdminPlotRentalOfferController;
use App\Http\Controllers\Auth\AdminClaimController;
use App\Http\Controllers\Demo\DemoLoginController;

Route::get('branding/logo', [BrandingController::class, 'logo'])->name('branding.logo');

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisterController::class, 'create'])->name('register');
    Route::post('register', [RegisterController::class, 'store'])->middleware('throttle:register');

    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store'])->middleware('throttle:login');

    Route::get('wachtwoord-vergeten', [ForgotPasswordController::class, 'create'])->name('password.request');
    Route::post('wachtwoord-vergeten', [ForgotPasswordController::class, 'store'])
        ->middleware('throttle:password-reset')
        ->name('password.email');

    Route::get('wachtwoord-herstellen/{token}', [ResetPasswordController::class, 'create'])->name('password.reset');
    Route::post('wachtwoord-herstellen', [ResetPasswordController::class, 'store'])
        ->middleware('throttle:password-reset')
        ->name('password.update');
});

// Demo mode only (PORTAL_DEMO=true): log in as the demo admin.
Route::get('demo', DemoLoginController::class)->name('demo');

// One-time link from the OMT website that makes whoever opens it an admin of this portal.
Route::get('beheerder-worden/{token}', AdminClaimController::class)
    ->middleware('throttle:admin-claim')
    ->name('admin-claim');

Route::middleware('auth')->group(function () {
    Route::get('/verify-minecraft', [MinecraftVerificationController::class, 'show'])
        ->name('minecraft.verify');
    Route::post('/verify-minecraft', [MinecraftVerificationController::class, 'verify']);

    // Every page checks its feature (404 while it is off) and a permission (see App\Support\Permissions).
    Route::middleware('minecraft.verified')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/portal/criminal-records', [CriminalRecordController::class, 'index'])
            ->middleware(['feature:criminal_records', 'can:view-criminal-record'])
            ->name('portal.criminal-records.index');

        Route::middleware(['feature:bank', 'can:use-bank'])->group(function () {
            Route::get('/portal/bank-accounts', [BankAccountController::class, 'index'])
                ->name('portal.bank-accounts.index');

            Route::get('/portal/bank-accounts/{uuid}', [BankAccountController::class, 'show'])
                ->name('portal.bank-accounts.show');
        });

        Route::middleware(['feature:plots', 'can:use-plots'])->group(function () {
            Route::get('/portal/plots', [PlotController::class, 'index'])->name('portal.plots.index');
            Route::get('/portal/plots/{name}', [PlotController::class, 'show'])->name('portal.plots.show');
            Route::post('/portal/plots/{name}/owners', [PlotController::class, 'addOwner'])->name('portal.plots.owners.add');
            Route::delete('/portal/plots/{name}/owners', [PlotController::class, 'removeOwner'])->name('portal.plots.owners.remove');
            Route::post('/portal/plots/{name}/members', [PlotController::class, 'addMember'])->name('portal.plots.members.add');
            Route::delete('/portal/plots/{name}/members', [PlotController::class, 'removeMember'])->name('portal.plots.members.remove');
        });

        Route::middleware('feature:broker')->group(function () {
            Route::middleware('can:use-broker')->group(function () {
                Route::get('/plots/te-koop', [PlotListingController::class, 'index'])
                    ->name('portal.plots.listings.index');

                Route::get('/plots/te-koop/{listing}/kopen', [PlotListingController::class, 'showBuyForm'])
                    ->name('portal.plots.listings.buy.show');
                Route::post('/plots/te-koop/{listing}/kopen', [PlotListingController::class, 'buy'])
                    ->name('portal.plots.listings.buy');

                Route::get('/plots/{plot}/verkopen', [PlotListingController::class, 'create'])
                    ->name('portal.plots.listings.create');
                Route::post('/plots/{plot}/verkopen', [PlotListingController::class, 'store'])
                    ->name('portal.plots.listings.store');

                Route::get('/plots/te-huur/{offer}/huren', [PlotRentalOfferController::class, 'showRentForm'])
                    ->name('portal.plots.rentals.rent.show');
                Route::post('/plots/te-huur/{offer}/huren', [PlotRentalOfferController::class, 'rent'])
                    ->name('portal.plots.rentals.rent');
                Route::get('/plots/{plot}/verhuren', [PlotRentalOfferController::class, 'create'])
                    ->name('portal.plots.rentals.create');
                Route::post('/plots/{plot}/verhuren', [PlotRentalOfferController::class, 'store'])
                    ->name('portal.plots.rentals.store');
            });

            // The seller or landlord (use-broker) or a manager (manage-broker); checked in the controller.
            Route::delete('/plots/te-koop/{listing}', [PlotListingController::class, 'destroy'])
                ->name('portal.plots.listings.destroy');
            Route::delete('/plots/te-huur/{offer}', [PlotRentalOfferController::class, 'destroy'])
                ->name('portal.plots.rentals.offers.destroy');
        });

        // Outside the broker feature: a running rental can always be seen and ended.
        Route::get('/plots/huur', [PlotRentalController::class, 'index'])->name('portal.plots.rentals.index');
        Route::post('/plots/huur/{rental}/opzeggen', [PlotRentalController::class, 'cancel'])->name('portal.plots.rentals.cancel');
        Route::post('/plots/huur/{rental}/betalen', [PlotRentalController::class, 'pay'])->name('portal.plots.rentals.pay');

        Route::prefix('portal')->name('portal.')->group(function () {
            Route::prefix('admin')->name('admin.')->group(function () {
                Route::middleware('can:manage-users')->group(function () {
                    Route::resource('users', AdminUserController::class)->except(['create', 'store', 'destroy']);
                    Route::post('users/{user}/roles', [AdminUserController::class, 'updateRoles'])->name('users.roles.update');
                });

                Route::resource('roles', AdminRoleController::class)->except('show')->middleware('can:manage-roles');

                Route::middleware('can:manage-settings')->group(function () {
                    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
                    Route::put('settings/features', [SettingsController::class, 'updateFeatures'])->name('settings.update-features');
                    Route::put('settings/permit-settings', [SettingsController::class, 'updatePermitSettings'])
                        ->middleware('feature:permits')
                        ->name('settings.update-permit-settings');
                    Route::put('settings/company-settings', [SettingsController::class, 'updateCompanySettings'])
                        ->middleware('feature:companies')
                        ->name('settings.update-company-settings');
                });

                // Appearance: accent colour and logo
                Route::middleware('can:manage-appearance')->group(function () {
                    Route::get('appearance', [BrandingController::class, 'edit'])->name('branding.edit');
                    Route::put('appearance', [BrandingController::class, 'update'])->name('branding.update');
                    Route::delete('appearance', [BrandingController::class, 'reset'])->name('branding.reset');
                });

                Route::prefix('permits')->name('permits.')->middleware(['feature:permits', 'can:manage-permit-types'])->group(function () {
                    Route::get('/types', [PermitTypeController::class, 'index'])->name('types.index');
                    Route::get('/types/create', [PermitTypeController::class, 'create'])->name('types.create');
                    Route::post('/types', [PermitTypeController::class, 'store'])->name('types.store');
                    Route::get('/types/{permitType}/edit', [PermitTypeController::class, 'edit'])->name('types.edit');
                    Route::put('/types/{permitType}', [PermitTypeController::class, 'update'])->name('types.update');
                    Route::delete('/types/{permitType}', [PermitTypeController::class, 'destroy'])->name('types.destroy');
                });

                Route::prefix('companies')->name('companies.')->middleware(['feature:companies', 'can:manage-company-types'])->group(function () {
                    Route::get('/types', [CompanyTypeController::class, 'index'])->name('types.index');
                    Route::get('/types/create', [CompanyTypeController::class, 'create'])->name('types.create');
                    Route::post('/types', [CompanyTypeController::class, 'store'])->name('types.store');
                    Route::get('/types/{companyType}/edit', [CompanyTypeController::class, 'edit'])->name('types.edit');
                    Route::put('/types/{companyType}', [CompanyTypeController::class, 'update'])->name('types.update');
                    Route::delete('/types/{companyType}', [CompanyTypeController::class, 'destroy'])->name('types.destroy');
                });

                Route::middleware(['feature:plots', 'can:manage-plots'])->group(function () {
                    Route::get('/plots', [AdminPlotController::class, 'index'])->name('plots.index');
                    Route::get('/plots/{name}', [AdminPlotController::class, 'show'])->name('plots.show');
                    Route::post('/plots/{name}/owners', [AdminPlotController::class, 'addOwner'])->name('plots.owners.add');
                    Route::delete('/plots/{name}/owners', [AdminPlotController::class, 'removeOwner'])->name('plots.owners.remove');
                    Route::post('/plots/{name}/members', [AdminPlotController::class, 'addMember'])->name('plots.members.add');
                    Route::delete('/plots/{name}/members', [AdminPlotController::class, 'removeMember'])->name('plots.members.remove');

                    Route::middleware(['feature:broker', 'can:manage-broker'])->group(function () {
                        Route::get('/plots/{name}/verkopen', [AdminPlotListingController::class, 'create'])->name('plots.listings.create');
                        Route::post('/plots/{name}/verkopen', [AdminPlotListingController::class, 'store'])->name('plots.listings.store');
                        Route::get('/plots/{name}/verhuren', [AdminPlotRentalOfferController::class, 'create'])->name('plots.rentals.create');
                        Route::post('/plots/{name}/verhuren', [AdminPlotRentalOfferController::class, 'store'])->name('plots.rentals.store');
                    });
                });
            });

            Route::middleware('feature:permits')->group(function () {
                // Before /permits/{permitType}/..., and per permit type limited to its roles.
                Route::middleware('can:manage-permits')->group(function () {
                    Route::get('/permits/manage', [PermitRequestManagementController::class, 'index'])->name('permits.manage.index');
                    Route::get('/permits/manage/{permitRequest}', [PermitRequestManagementController::class, 'show'])->name('permits.manage.show');
                    Route::post('/permits/manage/{permitRequest}/handle', [PermitRequestManagementController::class, 'handle'])->name('permits.manage.handle');
                });

                Route::middleware('can:request-permits')->group(function () {
                    Route::get('/permits', [PermitsController::class, 'index'])->name('permits.index');
                    Route::get('/permits/{permitType}/request', [PermitsController::class, 'request'])->name('permits.request');
                    Route::post('/permits/{permitType}/request', [PermitsController::class, 'store'])->name('permits.store');
                    Route::get('/permits/requests/{permitRequest}', [PermitsController::class, 'show'])->name('permits.show');
                });
            });

            Route::middleware('feature:companies')->prefix('companies')->name('companies.')->group(function () {
                // Before /companies/{company}, which would otherwise catch /manage and /dissolutions.
                Route::middleware('can:manage-companies')->group(function () {
                    Route::get('/manage', [CompanyRequestManagementController::class, 'index'])->name('manage.index');
                    Route::get('/manage/{companyRequest}', [CompanyRequestManagementController::class, 'show'])->name('manage.show');
                    Route::post('/manage/{companyRequest}/handle', [CompanyRequestManagementController::class, 'handle'])->name('manage.handle');

                    Route::get('/dissolutions', [DissolutionRequestManagementController::class, 'index'])->name('dissolutions.index');
                    Route::get('/dissolutions/{dissolutionRequest}', [DissolutionRequestManagementController::class, 'show'])->name('dissolutions.show');
                    Route::post('/dissolutions/{dissolutionRequest}/handle', [DissolutionRequestManagementController::class, 'handle'])->name('dissolutions.handle');
                });

                Route::middleware('can:view-company-registry')->group(function () {
                    Route::get('/register/search', [CompanyRegistryController::class, 'index'])->name('registry');
                    Route::get('/registry/search', [CompanyRegistryController::class, 'search'])->name('registry.search');
                    Route::get('/registry/{company}', [CompanyRegistryController::class, 'show'])->name('registry.show');
                });

                Route::middleware('can:use-companies')->group(function () {
                    Route::get('/', [CompaniesController::class, 'index'])->name('index');
                    Route::get('/register', [CompaniesController::class, 'register'])->name('register');
                    Route::get('/lookup', [CompaniesController::class, 'lookup'])->name('lookup');
                    Route::get('/requests/{companyRequest}', [CompaniesController::class, 'showRequest'])->name('request-details');
                    Route::get('/{companyType}/request', [CompaniesController::class, 'request'])->name('request');
                    Route::post('/{companyType}/request', [CompaniesController::class, 'store'])->name('store');
                    Route::post('/{company}/dissolve', [CompaniesController::class, 'dissolve'])->name('dissolve');
                    Route::get('/{company}', [CompaniesController::class, 'show'])->name('show');
                });
            });

            Route::middleware(['feature:police', 'can:manage-police'])->prefix('police')->name('police.')->group(function () {
                Route::get('/players', [PlayerDatabaseController::class, 'index'])->name('players.index');
                Route::get('/players/{user}', [PlayerDatabaseController::class, 'show'])->name('players.show');
            });

            Route::prefix('bank-accounts')->name('bank-accounts.')->middleware(['feature:transactions', 'can:use-transactions'])->group(function () {
                Route::get('/{accountUuid}/transfer', [BankTransactionController::class, 'create'])->name('transactions.create');
                Route::post('/{accountUuid}/transfer', [BankTransactionController::class, 'store'])->name('transactions.store');
                Route::get('/search/users', [BankTransactionController::class, 'search'])->name('transactions.search-users');
                Route::get('/accounts/{userId}', [BankTransactionController::class, 'getUserAccounts'])
                    ->name('transactions.get-accounts');
            });
        });
    });

    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');

    Route::post('notifications/mark-all-read', function() {
        auth()->user()->unreadNotifications->markAsRead();
        return back();
    })->name('notifications.mark-all-read');
});
