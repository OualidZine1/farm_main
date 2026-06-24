<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FieldController;
use App\Http\Controllers\InventoryTransactionController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
| */
Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->middleware('auth')->name('home');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::controller(LoginController::class)->group(function () {
        Route::get('/login', 'showLoginForm')->name('login');
        Route::post('/login', 'login');
    });

    Route::get('/forgot-password', [PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
});

// Logout Route (must be separate from guest group)
Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Protected Routes (require authentication)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile/password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::post('/profile/manager', [App\Http\Controllers\ProfileController::class, 'createManager'])
        ->middleware('role:administrator')
        ->name('profile.manager.create');
    Route::get('/audit-logs', [AuditLogController::class, 'index'])
        ->middleware('role:administrator')
        ->name('audit-logs.index');

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::middleware('role:administrator')->group(function () {
        Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    });

    // Product CRUD Routes with 'products' prefix
    Route::prefix('products')
        ->controller(ProductController::class)
        ->name('products.')
        ->group(function () {
            // Standard CRUD Routes
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{product}', 'show')->name('show');
            Route::get('/{product}/edit', 'edit')->name('edit');
            Route::put('/{product}', 'update')->name('update');
            Route::delete('/{product}', 'destroy')->middleware('role:administrator')->name('destroy');

            // Stock Management Routes
            Route::prefix('/{product}')->group(function () {
                Route::get('/add-stock', 'showAddStockForm')->name('add-stock.form');
                Route::post('/add-stock', 'addStock')->name('add-stock');
                Route::get('/use-stock', 'showUseStockForm')->name('use-stock.form');
                Route::post('/use-stock', 'useStock')->name('use-stock');
            });
        });

    // Transactions Routes
    Route::prefix('transactions')
        ->controller(InventoryTransactionController::class)
        ->name('transactions.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/export', 'export')->name('export');
            Route::get('/dashboard', 'dashboard')->name('dashboard');
            Route::get('/product-usage-report', 'productUsageReport')->name('product-usage-report');

            Route::get('/{transaction}', 'show')->name('show');
        });

    // Field Management Routes
    Route::get('/fields', [FieldController::class, 'index'])->name('fields.index');
    Route::middleware('role:administrator')->group(function () {
        Route::get('/fields/create', [FieldController::class, 'create'])->name('fields.create');
        Route::post('/fields', [FieldController::class, 'store'])->name('fields.store');
        Route::get('/fields/{field}/edit', [FieldController::class, 'edit'])->name('fields.edit');
        Route::put('/fields/{field}', [FieldController::class, 'update'])->name('fields.update');
        Route::delete('/fields/{field}', [FieldController::class, 'destroy'])->name('fields.destroy');
    });
});
