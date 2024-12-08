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

            // TODO: CREATE PLOT ROUTES

            Route::middleware(['auth', 'police.access'])->group(function () {
                // TODO: CREATE POLICE ROUTES
            });

            Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
                Route::resource('users', AdminUserController::class);
                Route::resource('roles', AdminRoleController::class);
                Route::post('users/{user}/roles', [AdminUserController::class, 'updateRoles'])->name('users.roles.update');
            });
        });
    });

    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');
});
