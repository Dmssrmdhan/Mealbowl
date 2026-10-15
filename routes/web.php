<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PaymentTypeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

// ---- Login ----
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ---- Semua user yang sudah login (CEO & ADMIN) ----
Route::middleware('auth')->group(function () {
    Route::get('/', fn () => redirect()->route('dashboard'));
    Route::get('/home', fn () => redirect()->route('dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Input & riwayat penjualan
    Route::get('/penjualan', [SaleController::class, 'index'])->name('sales.index');
    Route::get('/penjualan/input', [SaleController::class, 'create'])->name('sales.create');
    Route::post('/penjualan', [SaleController::class, 'store'])->name('sales.store');

    // Lihat daftar menu & laporan
    Route::get('/menu', [MenuController::class, 'index'])->name('menus.index');
    Route::get('/laporan', [ReportController::class, 'index'])->name('reports.index');

    // ---- Khusus CEO ----
    Route::middleware(RoleMiddleware::class . ':ceo')->group(function () {
        Route::delete('/penjualan/{transaction}', [SaleController::class, 'destroy'])->name('sales.destroy');

        Route::resource('menu', MenuController::class)
            ->except(['index', 'show'])
            ->parameters(['menu' => 'menu'])
            ->names('menus');

        Route::resource('jenis-bayar', PaymentTypeController::class)
            ->only(['index', 'store', 'update', 'destroy'])
            ->parameters(['jenis-bayar' => 'payment_type'])
            ->names('payment_types');

        Route::resource('akun', UserController::class)
            ->except(['show'])
            ->parameters(['akun' => 'user'])
            ->names('users');
    });
});
