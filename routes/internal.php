<?php

use App\Http\Controllers\Internal\TenantController;
use Illuminate\Support\Facades\Route;

/*
 * Provisioning API, on the central domains only, signed by the OMT website.
 * See App\Http\Middleware\VerifyProvisioningSignature.
 */
Route::prefix('tenants/{tenantId}')->whereUuid('tenantId')->controller(TenantController::class)->group(function () {
    Route::put('/', 'upsert');
    Route::get('/', 'show');
    Route::delete('/', 'destroy');
    Route::patch('status', 'status');
    Route::post('plugin-check', 'pluginCheck');
    Route::post('admin-claim', 'adminClaim');
});
