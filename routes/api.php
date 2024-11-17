<?php

use App\Http\Controllers\Api\MinecraftVerificationController;
use Illuminate\Support\Facades\Route;

Route::post('/minecraft/verify', [MinecraftVerificationController::class, 'verify'])
    ->middleware('api.key'); 