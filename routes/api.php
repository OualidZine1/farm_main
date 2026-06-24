<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Dashboard API Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/dashboard-stats', [\App\Http\Controllers\DashboardController::class, 'getStats']);
    Route::get('/stock-history', [\App\Http\Controllers\DashboardController::class, 'getStockHistory']);
    Route::get('/category-distribution', [\App\Http\Controllers\DashboardController::class, 'getCategoryDistribution']);
    Route::get('/low-stock', [\App\Http\Controllers\ProductController::class, 'getLowStock']);
    Route::get('/transactions', [\App\Http\Controllers\InventoryTransactionController::class, 'getRecentTransactions']);
    Route::post('/products/{product}/stock', [\App\Http\Controllers\ProductController::class, 'addStock']);
});
