<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ManufacturerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\StockTransactionController;
use App\Http\Controllers\RequisitionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProductTypeController; // <-- ตรวจสอบว่ามีบรรทัดนี้

use App\Models\Product;
use App\Models\Batch;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Default Route (หน้าแรก) - จะเปลี่ยนไปที่หน้า Login ถ้ายังไม่ Login
Route::get('/', function () {
    return redirect()->route('login');
});


// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes (เส้นทางที่ต้อง Login ก่อนถึงจะเข้าถึงได้)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Routes สำหรับ Master Data: Departments
    Route::resource('departments', DepartmentController::class);

    // Routes สำหรับ Master Data: Categories
    Route::resource('categories', CategoryController::class);

    // Routes สำหรับ Master Data: Suppliers
    Route::resource('suppliers', SupplierController::class);

    // Routes สำหรับ Master Data: Manufacturers
    Route::resource('manufacturers', ManufacturerController::class);

    // Routes สำหรับ Master Data: Product Types
    Route::resource('product-types', ProductTypeController::class); // <-- ตรวจสอบว่ามีบรรทัดนี้

    // Routes สำหรับ Master Data: Products
    Route::resource('products', ProductController::class);

    // Routes สำหรับ Batches
    Route::resource('batches', BatchController::class);

    // Routes สำหรับ Stock Transactions
    Route::get('/stock-transactions', [StockTransactionController::class, 'index'])->name('stock_transactions.index');
    Route::get('/stock-transactions/receive', [StockTransactionController::class, 'createReceive'])->name('stock_transactions.receive.create');
    Route::post('/stock-transactions/receive', [StockTransactionController::class, 'storeReceive'])->name('stock_transactions.receive.store');
    Route::get('/stock-transactions/issue', [StockTransactionController::class, 'createIssue'])->name('stock_transactions.issue.create');
    Route::post('/stock-transactions/issue', [StockTransactionController::class, 'storeIssue'])->name('stock_transactions.issue.store');
    Route::get('/stock-transactions/adjust', [StockTransactionController::class, 'createAdjust'])->name('stock_transactions.adjust.create');
    Route::post('/stock-transactions/adjust', [StockTransactionController::class, 'storeAdjust'])->name('stock_transactions.adjust.store');

    // Routes สำหรับ Requisitions
    Route::resource('requisitions', RequisitionController::class);
    // Route สำหรับดำเนินการเบิกสินค้า
    Route::post('/requisitions/{requisition}/process', [RequisitionController::class, 'processRequisition'])->name('requisitions.process');

    // Routes สำหรับ Users
    Route::resource('users', UserController::class);

    // Routes สำหรับ Reports
    Route::get('/reports/stock', [ReportController::class, 'stockReport'])->name('reports.stock');
    Route::get('/reports/requisition', [ReportController::class, 'requisitionReport'])->name('reports.requisition');
    Route::get('/reports/low-stock-products', [ReportController::class, 'lowStockProductsReport'])->name('reports.low_stock_products');
    Route::get('/reports/expiring-batches', [ReportController::class, 'expiringBatchesReport'])->name('reports.expiring_batches'); 
    Route::get('/reports/pending-requisitions', [ReportController::class, 'pendingRequisitionsReport'])->name('reports.pending_requisitions');

});

// API Route สำหรับดึง Batches ตาม Product ID (อยู่นอก Auth middleware เพื่อให้ AJAX เรียกได้ง่าย)
Route::get('/api/products/{product}/batches', function (Product $product) {
    return response()->json($product->batches()->orderBy('batch_number')->get());
});
