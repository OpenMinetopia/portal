<?php

use App\Http\Controllers\Api\MinecraftApiController;
use App\Http\Controllers\Api\MinecraftVerificationController;
use Illuminate\Support\Facades\Route;

// Public endpoints (no API key required)
Route::get('/minecraft/player/{username}', [MinecraftApiController::class, 'getPlayer'])
    ->middleware('throttle:60,1')
    ->name('api.minecraft.player');

// Protected endpoints (require API key)
Route::middleware('api.key')->group(function () {
    Route::post('/minecraft/verify', [MinecraftVerificationController::class, 'verify'])
        ->name('api.minecraft.verify');
});
