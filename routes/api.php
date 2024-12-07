<?php

use App\Http\Controllers\Api\MinecraftApiController;
use App\Http\Controllers\Api\MinecraftVerificationController;
use App\Http\Controllers\Api\MinetopiaController;
use App\Http\Controllers\Api\ArrestController;
use App\Http\Controllers\Api\EmergencyController;
use App\Http\Controllers\Api\SecurityController;
use App\Http\Controllers\Api\PlotController;
use App\Http\Controllers\Api\LevelController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\FineController;
use App\Http\Controllers\Api\VehicleController;
use App\Http\Controllers\Api\WalkieTalkieController;
use App\Http\Controllers\Api\DetectionGateController;
use App\Http\Controllers\Api\TeleporterController;
use App\Http\Controllers\Api\FitnessController;
use Illuminate\Support\Facades\Route;

// Public endpoints (no API key required)
Route::get('/minecraft/player/{username}', [MinecraftApiController::class, 'getPlayer'])
    ->middleware('throttle:60,1')
    ->name('api.minecraft.player');

// Protected endpoints (require API key)
Route::middleware('api.key')->group(function () {
    // Verification endpoint
    Route::post('/minecraft/verify', [MinecraftVerificationController::class, 'verify'])
        ->name('api.minecraft.verify');

    // Player status endpoints
    Route::post('/player/status/online', [MinecraftApiController::class, 'setOnline']);
    Route::post('/player/status/offline', [MinecraftApiController::class, 'setOffline']);
    
    // Police & Security endpoints
    Route::prefix('police')->group(function () {
        // Arrests
        Route::post('/arrests', [ArrestController::class, 'create']);
        Route::post('/arrests/{id}/release', [ArrestController::class, 'release']);
        
        // Fines
        Route::post('/fines', [FineController::class, 'create']);
        Route::post('/fines/{id}/pay', [FineController::class, 'pay']);
        
        // Security items
        Route::post('/security/use', [SecurityController::class, 'useItem']);
        Route::post('/security/remove', [SecurityController::class, 'removeEffect']);

        // Detection gates
        Route::post('/gates', [DetectionGateController::class, 'create']);
        Route::post('/gates/{id}/toggle', [DetectionGateController::class, 'toggleActive']);
        Route::post('/gates/log', [DetectionGateController::class, 'logDetection']);
    });

    // Emergency system endpoints
    Route::prefix('emergency')->group(function () {
        Route::post('/calls', [EmergencyController::class, 'create']);
        Route::post('/calls/{id}/respond', [EmergencyController::class, 'respond']);
        Route::post('/calls/{id}/close', [EmergencyController::class, 'close']);
    });

    // Plot system endpoints
    Route::prefix('plots')->group(function () {
        Route::post('/', [PlotController::class, 'create']);
        Route::put('/{id}', [PlotController::class, 'update']);
        Route::delete('/{id}', [PlotController::class, 'delete']);
        Route::post('/{id}/members', [PlotController::class, 'addMember']);
        Route::delete('/{id}/members', [PlotController::class, 'removeMember']);
    });

    // Level & Progress endpoints
    Route::prefix('level')->group(function () {
        Route::post('/progress', [LevelController::class, 'updateProgress']);
        Route::post('/calculate', [LevelController::class, 'calculateLevel']);
    });

    // Chat system endpoints
    Route::prefix('chat')->group(function () {
        Route::post('/message', [ChatController::class, 'store']);
        Route::post('/prefix', [ChatController::class, 'updatePrefix']);
    });

    // Bank & Economy endpoints
    Route::prefix('economy')->group(function () {
        Route::post('/balance', [MinetopiaController::class, 'updateBalance']);
        Route::get('/balance/{minecraft_uuid}', [MinetopiaController::class, 'getBalance']);
    });

    // Vehicle endpoints
    Route::prefix('vehicles')->group(function () {
        Route::post('/', [VehicleController::class, 'create']);
        Route::put('/{id}', [VehicleController::class, 'update']);
        Route::delete('/{id}', [VehicleController::class, 'delete']);
    });

    // Walkie-talkie endpoints
    Route::prefix('walkie-talkie')->group(function () {
        Route::post('/channel', [WalkieTalkieController::class, 'setChannel']);
        Route::post('/emergency', [WalkieTalkieController::class, 'useEmergency']);
    });

    // Teleporter endpoints
    Route::prefix('teleporters')->group(function () {
        Route::post('/', [TeleporterController::class, 'create']);
        Route::put('/{id}', [TeleporterController::class, 'update']);
        Route::delete('/{id}', [TeleporterController::class, 'delete']);
    });

    // Fitness endpoints
    Route::prefix('fitness')->group(function () {
        Route::post('/update', [FitnessController::class, 'update']);
        Route::get('/{minecraft_uuid}', [FitnessController::class, 'get']);
    });
});
