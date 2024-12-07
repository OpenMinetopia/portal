<?php

use App\Http\Controllers\Api\MinecraftVerificationController;
use App\Http\Controllers\Api\MinecraftApiController;
use App\Http\Controllers\Api\MinetopiaController;
use Illuminate\Support\Facades\Route;

// Verification endpoints
Route::prefix('v1')->group(function () {
    Route::post('/minecraft/verify', [MinecraftVerificationController::class, 'verify'])
        ->name('api.minecraft.verify');

    Route::get('/minecraft/player/{uuid}', [MinecraftVerificationController::class, 'getPlayerData'])
        ->name('api.minecraft.player');
});
