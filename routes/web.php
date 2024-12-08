<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\MinecraftVerificationController;
use App\Http\Controllers\ApiDocumentationController;
use App\Http\Controllers\Portal\PlotController;
use App\Http\Controllers\Portal\DashboardController;
use App\Http\Controllers\Portal\BankController;
use App\Http\Controllers\Portal\VehicleController;
use App\Http\Controllers\Portal\EmergencyCallController;
use App\Http\Controllers\Portal\DetectionGateController;
use App\Http\Controllers\Portal\FineController;
use App\Http\Controllers\Portal\ArrestController;
use App\Http\Controllers\Portal\WalkieTalkieController;
use App\Http\Controllers\Portal\Admin\AdminUserController;
use App\Http\Controllers\Portal\Admin\AdminRoleController;
use App\Http\Controllers\Portal\Admin\AdminTeleporterController;

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

        Route::prefix('portal')->name('portal.')->group(function () {
            Route::resource('plots', PlotController::class);
            Route::get('/bank', [BankController::class, 'index'])->name('bank.index');
            Route::resource('vehicles', VehicleController::class);

            // Police Routes (protected by middleware)
            Route::middleware(['auth', 'police.access'])->group(function () {
                Route::resource('emergency-calls', EmergencyCallController::class);
                Route::resource('detection-gates', DetectionGateController::class);
                Route::resource('fines', FineController::class);
                Route::resource('arrests', ArrestController::class);
                Route::resource('walkie-talkies', WalkieTalkieController::class);
            });

            Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
                Route::resource('users', AdminUserController::class);
                Route::resource('roles', AdminRoleController::class);
                Route::resource('teleporters', AdminTeleporterController::class);
                
                // Additional user management routes
                Route::post('users/{user}/roles', [AdminUserController::class, 'updateRoles'])->name('users.roles.update');
            });
        });
    });

    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');

    Route::middleware(['auth', 'verified'])->group(function () {
        Route::prefix('dashboard')->name('dashboard.')->group(function () {
            Route::resource('plots', PlotController::class);
        });
    });
});

Route::prefix('api-docs')->group(function () {
    Route::get('/', [ApiDocumentationController::class, 'index'])->name('api-docs.index');
    Route::get('/authentication', [ApiDocumentationController::class, 'authentication'])->name('api-docs.authentication');
    Route::get('/player', [ApiDocumentationController::class, 'player'])->name('api-docs.player');
    Route::get('/police', [ApiDocumentationController::class, 'police'])->name('api-docs.police');
    Route::get('/emergency', [ApiDocumentationController::class, 'emergency'])->name('api-docs.emergency');
    Route::get('/plots', [ApiDocumentationController::class, 'plots'])->name('api-docs.plots');
    Route::get('/economy', [ApiDocumentationController::class, 'economy'])->name('api-docs.economy');
    Route::get('/vehicles', [ApiDocumentationController::class, 'vehicles'])->name('api-docs.vehicles');
    Route::get('/chat', [ApiDocumentationController::class, 'chat'])->name('api-docs.chat');
    Route::get('/fitness', [ApiDocumentationController::class, 'fitness'])->name('api-docs.fitness');
    Route::get('/teleporters', [ApiDocumentationController::class, 'teleporters'])->name('api-docs.teleporters');
    Route::get('/detection-gates', [ApiDocumentationController::class, 'detectionGates'])->name('api-docs.detection-gates');
    Route::get('/level', [ApiDocumentationController::class, 'level'])->name('api-docs.level');
    Route::get('/walkie-talkie', [ApiDocumentationController::class, 'walkieTalkie'])->name('api-docs.walkie-talkie');
    Route::get('/bank', [ApiDocumentationController::class, 'bank'])->name('api-docs.bank');
});
