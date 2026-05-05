<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Api\BatchApiController;

Route::middleware(['auth:sanctum', 'user'])->group(function () {
    Route::get('/products/{product}/batches', [BatchApiController::class, 'getBatchesByProduct'])
        ->name('api.products.batches');

    // API สำหรับสินค้า
    Route::get('/products/search', [ProductApiController::class, 'search'])
        ->name('api.products.search');

    Route::get('/products/{id}/batches', [ProductApiController::class, 'getBatches']);

});

// กลุ่ม routes สำหรับ assets
Route::middleware(['auth', 'role:admin,manager'])->prefix('assets')->group(function () {
    Route::get('/', [AssetController::class, 'index']);
    Route::post('/', [AssetController::class, 'store']);
    Route::post('/borrow', [AssetController::class, 'borrow']);
    Route::post('/return', [AssetController::class, 'return']);
});



