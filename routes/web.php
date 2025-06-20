<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\MinecraftVerificationController;
use App\Http\Controllers\Portal\DashboardController;
use App\Http\Controllers\Portal\Admin\AdminUserController;
use App\Http\Controllers\Portal\Admin\AdminRoleController;
use App\Http\Controllers\Admin\SettingsController;
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
use App\Http\Controllers\Portal\BankTransactionController;
use App\Http\Controllers\Portal\Admin\AdminPlotController;
use App\Http\Controllers\Portal\Admin\SystemDebugController;

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisterController::class, 'create'])->name('register');
    Route::post('register', [RegisterController::class, 'store']);

    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::get('/verify-minecraft', [MinecraftVerificationController::class, 'show'])
        ->name('minecraft.verify');
    Route::post('/verify-minecraft', [MinecraftVerificationController::class, 'verify']);

    Route::middleware('minecraft.verified')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/portal/criminal-records', [CriminalRecordController::class, 'index'])
            ->name('portal.criminal-records.index');

        Route::get('/portal/bank-accounts', [BankAccountController::class, 'index'])
            ->name('portal.bank-accounts.index');

        Route::get('/portal/bank-accounts/{uuid}', [BankAccountController::class, 'show'])
            ->name('portal.bank-accounts.show');

        Route::get('/portal/plots', [PlotController::class, 'index'])->name('portal.plots.index');
        Route::get('/portal/plots/{name}', [PlotController::class, 'show'])->name('portal.plots.show');
        Route::post('/portal/plots/{name}/owners', [PlotController::class, 'addOwner'])->name('portal.plots.owners.add');
        Route::delete('/portal/plots/{name}/owners', [PlotController::class, 'removeOwner'])->name('portal.plots.owners.remove');
        Route::post('/portal/plots/{name}/members', [PlotController::class, 'addMember'])->name('portal.plots.members.add');
        Route::delete('/portal/plots/{name}/members', [PlotController::class, 'removeMember'])->name('portal.plots.members.remove');

        Route::middleware('broker.enabled')->group(function () {
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
            Route::delete('/plots/te-koop/{listing}', [PlotListingController::class, 'destroy'])
                ->name('portal.plots.listings.destroy');
        });

        Route::prefix('portal')->name('portal.')->group(function () {
                    Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
            // User & Role Management
            Route::resource('users', AdminUserController::class);
            Route::post('users/{user}/roles', [AdminUserController::class, 'updateRoles'])->name('users.roles.update');
            Route::resource('roles', AdminRoleController::class);

            // Settings
            Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
            Route::put('settings/features', [SettingsController::class, 'updateFeatures'])->name('settings.update-features');
            Route::put('settings/permit-settings', [SettingsController::class, 'updatePermitSettings'])->name('settings.update-permit-settings');
            Route::put('settings/company-settings', [SettingsController::class, 'updateCompanySettings'])->name('settings.update-company-settings');

            // Debug Information (only in non-production)
            Route::get('debug', [SystemDebugController::class, 'index'])->name('debug.index');

                // Admin permit type management
                Route::prefix('permits')->name('permits.')->group(function () {
                    Route::get('/types', [PermitTypeController::class, 'index'])->name('types.index');
                    Route::get('/types/create', [PermitTypeController::class, 'create'])->name('types.create');
                    Route::post('/types', [PermitTypeController::class, 'store'])->name('types.store');
                    Route::get('/types/{permitType}/edit', [PermitTypeController::class, 'edit'])->name('types.edit');
                    Route::put('/types/{permitType}', [PermitTypeController::class, 'update'])->name('types.update');
                    Route::delete('/types/{permitType}', [PermitTypeController::class, 'destroy'])->name('types.destroy');
                });

                // Admin overview routes
                Route::get('/plots', [AdminPlotController::class, 'index'])->name('plots.index');
                Route::get('/plots/{name}', [AdminPlotController::class, 'show'])->name('plots.show');
                Route::post('/plots/{name}/owners', [AdminPlotController::class, 'addOwner'])->name('plots.owners.add');
                Route::delete('/plots/{name}/owners', [AdminPlotController::class, 'removeOwner'])->name('plots.owners.remove');
                Route::post('/plots/{name}/members', [AdminPlotController::class, 'addMember'])->name('plots.members.add');
                Route::delete('/plots/{name}/members', [AdminPlotController::class, 'removeMember'])->name('plots.members.remove');
            });

            // Company Management Routes
            Route::prefix('companies')->name('companies.')->middleware(['companies.manage', 'companies.enabled'])->group(function () {
                Route::get('/requests', [CompanyRequestManagementController::class, 'index'])->name('requests.index');
                Route::get('/requests/{companyRequest}', [CompanyRequestManagementController::class, 'show'])->name('requests.show');
                Route::post('/requests/{companyRequest}/handle', [CompanyRequestManagementController::class, 'handle'])->name('requests.handle');

                Route::get('/dissolutions', [DissolutionRequestManagementController::class, 'index'])->name('dissolutions.index');
                Route::get('/dissolutions/{dissolutionRequest}', [DissolutionRequestManagementController::class, 'show'])->name('dissolutions.show');
                Route::post('/dissolutions/{dissolutionRequest}/handle', [DissolutionRequestManagementController::class, 'handle'])->name('dissolutions.handle');
            });


            // Regular portal routes
            Route::middleware('permits.enabled')->group(function () {
                Route::get('/permits', [PermitsController::class, 'index'])->name('permits.index');
                Route::get('/permits/{permitType}/request', [PermitsController::class, 'request'])->name('permits.request');
                Route::post('/permits/{permitType}/request', [PermitsController::class, 'store'])->name('permits.store');
                Route::get('/permits/requests/{permitRequest}', [PermitsController::class, 'show'])->name('permits.show');

                // Permit management routes (for authorized roles)
                Route::middleware('permit.manage')->group(function () {
                    Route::get('/permits/manage', [PermitRequestManagementController::class, 'index'])->name('permits.manage.index');
                    Route::get('/permits/manage/{permitRequest}', [PermitRequestManagementController::class, 'show'])->name('permits.manage.show');
                    Route::post('/permits/manage/{permitRequest}/handle', [PermitRequestManagementController::class, 'handle'])->name('permits.manage.handle');
                });
            });

            // Company routes for users
            Route::middleware('companies.enabled')->group(function () {
                Route::get('/companies/register/search', [CompanyRegistryController::class, 'index'])
                    ->name('companies.registry');
                Route::get('/companies/registry/search', [CompanyRegistryController::class, 'search'])
                    ->name('companies.registry.search');
                Route::get('/companies/registry/{company}', [CompanyRegistryController::class, 'show'])
                    ->name('companies.registry.show');

                Route::prefix('companies')->name('companies.')->group(function () {
                    Route::get('/', [CompaniesController::class, 'index'])->name('index');
                    Route::get('/register', [CompaniesController::class, 'register'])->name('register');
                    Route::get('/{companyType}/request', [CompaniesController::class, 'request'])->name('request');
                    Route::post('/{companyType}/request', [CompaniesController::class, 'store'])->name('store');
                    Route::get('/lookup', [CompaniesController::class, 'lookup'])->name('lookup');
                    Route::post('/{company}/dissolve', [CompaniesController::class, 'dissolve'])->name('dissolve');
                    Route::get('/requests/{companyRequest}', [CompaniesController::class, 'showRequest'])->name('request-details');
                    Route::get('/{company}', [CompaniesController::class, 'show'])->name('show');

                    // Company management routes (for authorized roles)
                    Route::middleware('companies.manage')->group(function () {
                        Route::get('/manage', [CompanyRequestManagementController::class, 'index'])->name('manage.index');
                        Route::get('/manage/{companyRequest}', [CompanyRequestManagementController::class, 'show'])->name('manage.show');
                        Route::post('/manage/{companyRequest}/handle', [CompanyRequestManagementController::class, 'handle'])->name('manage.handle');
                    });
                });

                // Admin company type management
                Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
                    Route::prefix('companies')->name('companies.')->group(function () {
                        Route::get('/types', [CompanyTypeController::class, 'index'])->name('types.index');
                        Route::get('/types/create', [CompanyTypeController::class, 'create'])->name('types.create');
                        Route::post('/types', [CompanyTypeController::class, 'store'])->name('types.store');
                        Route::get('/types/{companyType}/edit', [CompanyTypeController::class, 'edit'])->name('types.edit');
                        Route::put('/types/{companyType}', [CompanyTypeController::class, 'update'])->name('types.update');
                        Route::delete('/types/{companyType}', [CompanyTypeController::class, 'destroy'])->name('types.destroy');
                    });
                });
            });

            // Police routes
            Route::middleware(['police.access'])->prefix('police')->name('police.')->group(function () {
                Route::get('/players', [PlayerDatabaseController::class, 'index'])->name('players.index');
                Route::get('/players/{user}', [PlayerDatabaseController::class, 'show'])->name('players.show');
            });

            Route::prefix('bank-accounts')->name('bank-accounts.')->group(function () {
                Route::middleware('transactions.enabled')->group(function () {
                    Route::get('/{accountUuid}/transfer', [BankTransactionController::class, 'create'])->name('transactions.create');
                    Route::post('/{accountUuid}/transfer', [BankTransactionController::class, 'store'])->name('transactions.store');
                    Route::get('/search/users', [BankTransactionController::class, 'search'])->name('transactions.search-users');
                    Route::get('/accounts/{userId}', [BankTransactionController::class, 'getUserAccounts'])
                        ->name('transactions.get-accounts');
                });
            });

        });
    });

    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');

    Route::post('notifications/mark-all-read', function() {
        auth()->user()->unreadNotifications->markAsRead();
        return back();
    })->name('notifications.mark-all-read');
});
