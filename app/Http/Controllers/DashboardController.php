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
use App\Models\StockTransaction;

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

        // รายการใบขอเบิกที่รอการอนุมัติ
        $pendingRequisitionsCount = Requisition::where('status', 'pending')->count();

        // สถิติอื่นๆ
        $totalDepartments = Department::where('status', 'active')->count();
        $totalSuppliers = Supplier::where('status', 'active')->count();
        $totalManufacturers = Manufacturer::where('status', 'active')->count();
        $totalUsers = User::where('status', 'active')->count();

        // ข้อมูลกราฟ 7 วันล่าสุด
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $chartData[] = [
                'date' => $date->format('d/m'),
                'out' => StockTransaction::where('transaction_type', 'out')
                            ->whereDate('created_at', $date)
                            ->sum('quantity'),
                'in' => StockTransaction::where('transaction_type', 'in')
                            ->whereDate('created_at', $date)
                            ->sum('quantity'),
            ];
        }

        // สินค้าสต็อกต่ำ 5 รายการ
        $lowStockProducts = Product::where('minimum_stock_level', '>', 0)
                                    ->whereRaw('products.minimum_stock_level >= (SELECT COALESCE(SUM(batches.quantity), 0) FROM batches WHERE batches.product_id = products.id)')
                                    ->with('batches')
                                    ->orderBy('name')
                                    ->take(5)
                                    ->get();

        // การเคลื่อนไหวสต็อกล่าสุด 5 รายการ
        $recentTransactions = StockTransaction::with(['product', 'batch', 'user', 'department'])
                                            ->orderBy('created_at', 'desc')
                                            ->take(5)
                                            ->get();
        
        // ข้อมูลการเบิกแยกตามแผนก
        $departmentStats = \App\Models\StockTransaction::where('transaction_type', 'out')
            ->whereNotNull('department_id')
            ->with('department')
            ->selectRaw('department_id, SUM(quantity) as total_out')
            ->groupBy('department_id')
            ->orderByDesc('total_out')
            ->take(8)
            ->get()
            ->map(function($item) {
                return [
                    'name' => $item->department->name ?? '-',
                    'total' => (int) $item->total_out,
                ];
            });

        return view('dashboard', compact(
            'totalProducts', 'totalBatches', 'totalStockQuantity',
            'lowStockProductsCount', 'expiringBatchesCount', 'pendingRequisitionsCount',
            'totalDepartments', 'totalSuppliers', 'totalManufacturers', 'totalUsers',
            'chartData', 'lowStockProducts', 'recentTransactions', 'departmentStats'
        ));
    }
}
