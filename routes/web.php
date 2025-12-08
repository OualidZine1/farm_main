<?php

use App\Http\Controllers\Auth\LoginController;
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
Route::middleware('guest')->group(function() {
    Route::controller(LoginController::class)->group(function() {
        Route::get('/login', 'showLoginForm')->name('login');
        Route::post('/login', 'login');
    });
});

// Logout Route (must be separate from guest group)
Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Protected Routes (require authentication)
Route::middleware('auth')->group(function() {
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile/password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::post('/profile/manager', [App\Http\Controllers\ProfileController::class, 'createManager'])->name('profile.manager.create');
    // Category CRUD Routes
    Route::resource('categories', CategoryController::class);

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
            Route::delete('/{product}', 'destroy')->name('destroy');

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

            // Add show, edit, update, destroy for transactions
            Route::get('/{transaction}', 'show')->name('show');
            Route::get('/{transaction}/edit', 'edit')->name('edit');
            Route::put('/{transaction}', 'update')->name('update');
            Route::delete('/{transaction}', 'destroy')->name('destroy');
        });

    // Field Management Routes
    Route::resource('fields', FieldController::class);
});
