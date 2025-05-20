<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\TenantAuthController;
use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\Tenant\TenantUserController;
use App\Http\Controllers\Auth\GoogleController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\VerifyTenantDomain;
use App\Http\Middleware\EnsureTenantAccess;
use App\Http\Controllers\Tenant\UserRegistrationController;
use App\Http\Controllers\Tenant\TenantAvailabilityController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\SecondaryAdminController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Tenant\TenantSettingsController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard', [
        'tenants' => \App\Models\Tenant::all()
    ]);
})->middleware(['auth'])->name('dashboard');


Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('auth.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('auth.login.submit');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('auth.logout');




Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Tenant Registration Routes
Route::get('/tenant/register', [TenantController::class, 'showRegistrationForm'])->name('tenant.register');
Route::post('/tenant/register', [TenantController::class, 'register'])->name('tenant.register');

// User Registration Routes
Route::get('/tenant/user/register', [UserRegistrationController::class, 'showRegistrationForm'])->name('auth.tenant-register-user');
Route::post('/tenant/user/register', [UserRegistrationController::class, 'register'])->name('auth.tenant-register-user.submit');

// User Authentication Routes
// Route::get('/user/login', [UserAuthController::class, 'showLoginForm'])->name('user.login');
// Route::post('/user/login', [UserAuthController::class, 'login'])->name('user.login.submit');
// User Dashboard Routes
Route::middleware(['user'])->group(function () {
    Route::get('/user/dashboard', [TenantUserController::class, 'dashboard'])->name('user.dashboard');
    Route::get('/user/profile', [TenantUserController::class, 'profile'])->name('user.profile');
    Route::get('/user/settings', [TenantUserController::class, 'settings'])->name('user.settings');
    Route::put('/user/profile', [TenantUserController::class, 'updateProfile'])->name('user.profile.update');
    Route::put('/user/settings', [TenantUserController::class, 'updateSettings'])->name('user.settings.update');
    Route::get('/user/my-booking', [TenantUserController::class, 'myBooks'])->name('user.my-booking.index');
    Route::get('/user/my-booking/create', [TenantUserController::class, 'createBooking'])->name('user.my-booking.create');
    Route::get('/user/check-availability', [TenantUserController::class, 'checkAvailability'])->name('user.check-availability');
    Route::get('/user/booking-history', [TenantUserController::class, 'bookingHistory'])->name('user.booking-history.index'); 
    Route::post('/user/my-booking', [TenantUserController::class, 'storeBooking'])->name('user.my-booking.store');
    Route::delete('/user/my-booking/{id}', [TenantUserController::class, 'deleteBooking'])->name('user.my-booking.delete');
    Route::get('/user/my-booking/{id}/edit', [TenantUserController::class, 'editBooking'])->name('user.my-booking.edit');
    Route::put('/user/my-booking/{id}', [TenantUserController::class, 'updateBooking'])->name('user.my-booking.update');
    Route::delete('/user/booking-history/{id}', [TenantUserController::class, 'deleteBookingHistory'])->name('user.booking-history.delete');
    Route::get('/user/booking-history/{id}', [TenantUserController::class, 'showBookingHistory'])->name('user.booking-history.show');
    Route::post('/user/logout', [UserAuthController::class, 'logout'])->name('user.logout');
    

});

Route::middleware(['auth'])->group(function () {
    Route::post('/tenant/{id}/accept', [TenantController::class, 'accept'])->name('tenant.accept');
    Route::post('/tenant/{id}/reject', [TenantController::class, 'reject'])->name('tenant.reject');
    Route::post('/tenant/{id}/disable', [TenantController::class, 'disable'])->name('tenant.disable');
    Route::post('/tenant/{id}/enable', [TenantController::class, 'enable'])->name('tenant.enable');
    Route::post('/tenant/{id}/premium', [TenantController::class, 'premium'])->name('tenant.premium');
    Route::post('/tenant/{id}/basic', [TenantController::class, 'basic'])->name('tenant.basic');
});

Route::get('/tenant/login', [TenantAuthController::class, 'showLoginForm'])->name('tenant.login');
Route::post('/tenant/login', [TenantAuthController::class, 'login'])->name('tenant.login.submit');
Route::post('/tenant/logout', [TenantAuthController::class, 'logout'])->name('tenant.logout');

Route::middleware(['tenant'])->group(function () {
    // Dashboard
    Route::get('/tenant/dashboard', [TenantController::class, 'dashboard'])->name('tenant.dashboard');
    
    // Profile and Settings Routes
    Route::get('/tenant/profile', [TenantController::class, 'profile'])->name('tenant.profile');
    Route::get('/tenant/settings', [TenantController::class, 'settings'])->name('tenant.settings');
    Route::put('/tenant/profile', [TenantController::class, 'updateProfile'])->name('tenant.profile.update');
    Route::put('/tenant/settings', [TenantController::class, 'updateSettings'])->name('tenant.settings.update');
    
    // Secondary Admin Routes
    Route::get('/tenant/secondary-admins', [TenantController::class, 'secondaryAdmins'])->name('tenant.secondary-admins');
    Route::get('/tenant/secondary-admins/create', [TenantController::class, 'createSecondaryAdmin'])->name('tenant.secondary-admins.create');
    Route::post('/tenant/secondary-admins', [TenantController::class, 'storeSecondaryAdmin'])->name('tenant.secondary-admins.store');
    Route::get('/tenant/secondary-admins/{id}/edit', [TenantController::class, 'editSecondaryAdmin'])->name('tenant.secondary-admins.edit');
    Route::put('/tenant/secondary-admins/{id}', [TenantController::class, 'updateSecondaryAdmin'])->name('tenant.secondary-admins.update');
    Route::delete('/tenant/secondary-admins/{id}', [TenantController::class, 'destroySecondaryAdmin'])->name('tenant.secondary-admins.destroy');

    // Bookings Routes
    Route::get('/tenant/bookings', [TenantController::class, 'bookings'])->name('tenant.bookings.index');
    Route::get('/tenant/bookings/{id}', [TenantController::class, 'showBooking'])->name('tenant.bookings.show');
    Route::get('/tenant/bookings/create', [TenantController::class, 'createBooking'])->name('tenant.bookings.create');
    Route::post('/tenant/bookings', [TenantController::class, 'storeBooking'])->name('tenant.bookings.store');
    Route::get('/tenant/bookings/{id}/edit', [TenantController::class, 'editBooking'])->name('tenant.bookings.edit');
    Route::put('/tenant/bookings/{id}', [TenantController::class, 'updateBooking'])->name('tenant.bookings.update');
    Route::delete('/tenant/bookings/{id}', [TenantController::class, 'deleteBooking'])->name('tenant.bookings.destroy');
    Route::put('/tenant/bookings/{id}/accept', [TenantController::class, 'acceptBooking'])->name('tenant.bookings.accept');
    Route::put('/tenant/bookings/{id}/reject', [TenantController::class, 'rejectBooking'])->name('tenant.bookings.reject');

    // Calendar Route
    Route::get('/tenant/calendar', [TenantController::class, 'calendar'])->name('tenant.calendar');

    // Availability Route
    Route::get('/tenant/availability', [TenantAvailabilityController::class, 'index'])->name('tenant.availability.index');
    Route::get('/tenant/availability/create', [TenantAvailabilityController::class, 'create'])->name('tenant.availability.create');
    Route::post('/tenant/availability', [TenantAvailabilityController::class, 'store'])->name('tenant.availability.store');
    Route::get('/tenant/availability/{id}/edit', [TenantAvailabilityController::class, 'editAvailability'])->name('tenant.availability.edit');
    Route::put('/tenant/availability/{id}', [TenantAvailabilityController::class, 'updateAvailability'])->name('tenant.availability.update');
    Route::delete('/tenant/availability/{id}', [TenantAvailabilityController::class, 'destroy'])->name('tenant.availability.destroy');

    // Users Routes
    Route::get('/tenant/users', [TenantController::class, 'users'])->name('tenant.users.index');

    // Plan Upgrade Route
    Route::post('/tenant/upgrade-plan', [TenantController::class, 'upgradePlan'])->name('tenant.upgrade-plan');

    // Tenant Settings Routes
    Route::get('/settings', [TenantSettingsController::class, 'index'])->name('tenant.settings');
    Route::put('/settings/theme', [TenantSettingsController::class, 'updateTheme'])->name('tenant.settings.theme.update');
    Route::put('/settings/password', [TenantSettingsController::class, 'updatePassword'])->name('tenant.settings.update');
});

Route::middleware(['secondary-admin'])->group(function () {
    Route::get('/secondary-admin/dashboard', [SecondaryAdminController::class, 'dashboard'])->name('secondary-admin.dashboard');
    Route::post('/secondary-admin/logout', [TenantAuthController::class, 'secondaryAdminLogout'])->name('secondary-admin.secondaryAdminLogout');
    Route::get('/secondary-admin/bookings', [SecondaryAdminController::class, 'bookings'])->name('secondary-admin.bookings.index');
    Route::get('/secondary-admin/calendar', [SecondaryAdminController::class, 'calendar'])->name('secondary-admin.calendar.index');
    Route::get('/secondary-admin/users', [SecondaryAdminController::class, 'users'])->name('secondary-admin.users.index');
    Route::get('/secondary-admin/availability', [SecondaryAdminController::class, 'availability'])->name('secondary-admin.availability.index');
    Route::get('/secondary-admin/availability/create', [SecondaryAdminController::class, 'createAvailability'])->name('secondary-admin.availability.create');
    Route::post('/secondary-admin/availability', [SecondaryAdminController::class, 'storeAvailability'])->name('secondary-admin.availability.store');
    Route::delete('/secondary-admin/bookings/{id}', [SecondaryAdminController::class, 'deleteBooking'])->name('secondary-admin.bookings.delete');
    Route::get('/secondary-admin/bookings/{id}', [SecondaryAdminController::class, 'showBooking'])->name('secondary-admin.bookings.show');
    Route::get('/secondary-admin/profile', [SecondaryAdminController::class, 'profile'])->name('secondary-admin.profile');
    Route::get('/secondary-admin/settings', [SecondaryAdminController::class, 'settings'])->name('secondary-admin.settings');
    Route::put('/secondary-admin/settings', [SecondaryAdminController::class, 'updateSettings'])->name('secondary-admin.settings.update');
    Route::post('/secondary-admin/bookings/{id}/approve', [SecondaryAdminController::class, 'approveBooking'])->name('secondary-admin.bookings.approve');
    Route::post('/secondary-admin/bookings/{id}/reject', [SecondaryAdminController::class, 'rejectBooking'])->name('secondary-admin.bookings.reject');
});

// Domain-based routes (for direct access to tenant's domain)
Route::domain('{domain}.localhost')->group(function () {
    Route::middleware([VerifyTenantDomain::class, EnsureTenantAccess::class])->group(function () {
        Route::get('/', function ($domain) {
            return redirect()->route('tenant.dashboard');
        });
    });
});

// Google Authentication Routes
Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// Admin Settings Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');
});

require __DIR__.'/auth.php';
