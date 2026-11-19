<?php

use App\Http\Controllers\Admin\AssetCategoryController;
use App\Http\Controllers\Admin\AssetController;
use App\Http\Controllers\Admin\AssetMutationController;
use App\Http\Controllers\Admin\AssetTypeController;
use App\Http\Controllers\Admin\SiteController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\SuperAdmin\TenantController;
use App\Http\Controllers\User\MyAssetController;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard if logged in, otherwise login
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Scan QR Code (Bisa diakses publik / karyawan via kamera smartphone)
Route::get('/scan/{token}', [ScanController::class, 'show'])->name('assets.scan');

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Tenant Scoped Routes
    Route::middleware('tenant.scope')->group(function () {
        // Shared Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // ─── 1. Super Admin Module ─────────────────────────────────────
        Route::middleware('role:superadmin')->prefix('superadmin')->name('superadmin.')->group(function () {
            Route::resource('tenants', TenantController::class);
        });

        // ─── 2. Tenant Admin Module ────────────────────────────────────
        Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
            // Master Data (b1, b2, b3)
            Route::resource('sites', SiteController::class);
            Route::resource('users', UserController::class);
            Route::resource('categories', AssetCategoryController::class);
            Route::resource('types', AssetTypeController::class);
            Route::get('types/{type}/specs', [AssetTypeController::class, 'getSpecs'])->name('types.specs');

            // Pengelolaan Inventaris & Mutasi (c1, c2, c3, c4)
            Route::resource('assets', AssetController::class);
            Route::get('assets/{asset}/print-label', [AssetController::class, 'printLabel'])->name('assets.print-label');
            Route::resource('mutations', AssetMutationController::class)->only(['index', 'create', 'store']);
        });

        // ─── 3. Tenant User Module (Personal Asset List) ───────────────
        Route::get('/my-assets', [MyAssetController::class, 'index'])->name('user.my-assets');
    });
});
