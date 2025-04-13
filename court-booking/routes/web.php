<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\TenantAuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\VerifyTenantDomain;
use App\Http\Middleware\EnsureTenantAccess;



Route::get('/', function () {
    return view('welcome');
});


Route::get('/dashboard', function () {
    return view('dashboard', [
        'tenants' => \App\Models\Tenant::all()
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::post('/tenant/register', [TenantController::class, 'register'])->name('tenant.register');
Route::middleware(['auth'])->group(function () {
    Route::post('/tenant/{id}/accept', [TenantController::class, 'accept'])->name('tenant.accept');
    Route::post('/tenant/{id}/reject', [TenantController::class, 'reject'])->name('tenant.reject');
    Route::post('/tenant/{id}/disable', [TenantController::class, 'disable'])->name('tenant.disable');
    Route::post('/tenant/{id}/enable', [TenantController::class, 'enable'])->name('tenant.enable');
    Route::post('/tenant/{id}/premium', [TenantController::class, 'premium'])->name('tenant.premium');
});

Route::get('/tenant/login', [TenantAuthController::class, 'showLoginForm'])->name('tenant.login');
Route::post('/tenant/login', [TenantAuthController::class, 'login'])->name('tenant.login.submit');
Route::post('/tenant/logout', [TenantAuthController::class, 'logout'])->name('tenant.logout');

// Protected tenant routes
Route::middleware([EnsureTenantAccess::class])->group(function () {
    Route::get('/tenant/dashboard', function () {
        return view('tenant.dashboard');
    })->name('tenant.dashboard');
});

// Domain-based routes (for direct access to tenant's domain)
Route::domain('{domain}.localhost')->group(function () {
    Route::middleware([VerifyTenantDomain::class, EnsureTenantAccess::class])->group(function () {
        Route::get('/', function ($domain) {
            return redirect()->route('tenant.dashboard');
        });
    });
});

require __DIR__.'/auth.php';
