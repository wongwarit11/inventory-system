<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Batch;
use App\Models\Requisition;
use App\Models\Department;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Manufacturer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index()
    {
        // สถิติสินค้าคงคลัง
        $totalProducts = Product::where('status', 'active')->count();
        $totalBatches = Batch::count();
        $totalStockQuantity = Batch::sum('quantity');

        // สินค้าที่สต็อกต่ำกว่าจุดต่ำสุด
        // แก้ไข Query เพื่อหลีกเลี่ยงข้อผิดพลาด "Non-grouping field used in HAVING clause" และ TypeError
        $lowStockProductsCount = Product::where('minimum_stock_level', '>', 0)
                                        ->whereRaw('products.minimum_stock_level >= (SELECT COALESCE(SUM(batches.quantity), 0) FROM batches WHERE batches.product_id = products.id)')
                                        ->count();

        // สินค้าใกล้หมดอายุ (ภายใน 30 วัน)
        $expirationThresholdDays = 30;
        $expirationDateLimit = Carbon::now()->addDays($expirationThresholdDays)->endOfDay();
        $expiringBatchesCount = Batch::whereNotNull('expiration_date')
                                    ->where('expiration_date', '<=', $expirationDateLimit)
                                    ->where('quantity', '>', 0)
                                    ->count();

        // รายการใบขอเบิกที่รอการอนุมัติ (Pending Requisitions)
        $pendingRequisitionsCount = Requisition::where('status', 'pending')->count();

        // สถิติอื่นๆ (ตัวอย่าง)
        $totalDepartments = Department::where('status', 'active')->count();
        $totalSuppliers = Supplier::where('status', 'active')->count();
        $totalManufacturers = Manufacturer::where('status', 'active')->count();
        $totalUsers = User::where('status', 'active')->count();

        return view('dashboard', compact(
            'totalProducts',
            'totalBatches',
            'totalStockQuantity',
            'lowStockProductsCount',
            'expiringBatchesCount',
            'pendingRequisitionsCount',
            'totalDepartments',
            'totalSuppliers',
            'totalManufacturers',
            'totalUsers'
        ));
    }
}
