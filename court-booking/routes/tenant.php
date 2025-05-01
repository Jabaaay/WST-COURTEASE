<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use App\Http\Controllers\Tenant\UserRegistrationController;
use App\Http\Controllers\TenantController;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::get('/', function () {
        return 'This is your multi-tenant application. The id of the current tenant is ' . tenant('id');


    });
    

    Route::get('/register', [UserRegistrationController::class, 'showRegistrationForm'])->name('auth.tenant-register-user');
    Route::post('/register', [UserRegistrationController::class, 'register'])->name('auth.tenant-register-user');

    // Secondary Admin Routes
    Route::post('/tenant/store-secondary-admin', [TenantController::class, 'storeSecondaryAdmin'])->name('tenant.store-secondary-admin');
    Route::delete('/tenant/secondary-admin/{id}', [TenantController::class, 'deleteSecondaryAdmin'])->name('tenant.delete-secondary-admin');
});
