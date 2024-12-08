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
});
