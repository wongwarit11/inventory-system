<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Batch;
use App\Models\Requisition;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; // Import Carbon for date handling

class ReportController extends Controller
{
    // เมธอดสำหรับตรวจสอบสิทธิ์การเข้าถึงรายงาน (Admin/Manager เท่านั้น)
    private function authorizeReportAccess()
    {
        if (Auth::user()->role === 'staff') {
            return redirect()->route('dashboard')->with('error', 'คุณไม่มีสิทธิ์เข้าถึงส่วนรายงาน');
        }
        return null;
    }

    /**
     * Display the Stock Report.
     */
    public function stockReport(Request $request)
    {
        if ($response = $this->authorizeReportAccess()) {
            return $response;
        }

        $query = Batch::with(['product.category', 'product.productType', 'product.manufacturer', 'location'])
                        ->whereHas('product', function($q) {
                            $q->where('status', 'active');
                        });

        // กรองรหัส/ชื่อสินค้า
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('product', function($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                ->orWhere('product_code', 'like', '%'.$search.'%');
            });
        }

        // กรองหมวดหมู่
        if ($request->filled('category_id')) {
            $query->whereHas('product', function($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        // กรองประเภทสินค้า
        if ($request->filled('product_type_id')) {
            $query->whereHas('product', function($q) use ($request) {
                $q->where('product_type_id', $request->product_type_id);
            });
        }

        // กรองผู้ผลิต
        if ($request->filled('manufacturer_id')) {
            $query->whereHas('product', function($q) use ($request) {
                $q->where('manufacturer_id', $request->manufacturer_id);
            });
        }

        $batches = $query->orderBy('product_id')
                        ->orderBy('expiration_date', 'asc')
                        ->paginate(15);

        $categories = \App\Models\Category::orderBy('name')->get();
        $productTypes = \App\Models\ProductType::orderBy('name')->get();
        $manufacturers = \App\Models\Manufacturer::orderBy('name')->get();

        return view('reports.stock_report', compact('batches', 'categories', 'productTypes', 'manufacturers'));
    }

    /**
     * Display the Requisition Report.
     */
    public function requisitionReport(Request $request)
    {
        if ($response = $this->authorizeReportAccess()) {
            return $response;
        }

        // ดึงค่า filter จาก request
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $statusFilter = $request->input('status_filter');
        $departmentFilter = $request->input('department_id');

        $query = Requisition::with(['user', 'department', 'items.product']);

        // Apply filters
        if ($startDate) {
            $query->whereDate('requisition_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('requisition_date', '<=', $endDate);
        }
        if ($statusFilter && $statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }
        if ($departmentFilter && $departmentFilter !== 'all') {
            $query->where('department_id', $departmentFilter);
        }

        $requisitions = $query->orderBy('requisition_date', 'desc')->paginate(15);

        // ดึงข้อมูลแผนกทั้งหมดสำหรับ dropdown filter
        $departments = \App\Models\Department::orderBy('name')->get();

        return view('reports.requisition_report', compact('requisitions', 'startDate', 'endDate', 'statusFilter', 'departments', 'departmentFilter'));
    }

    /**
     * Display the Low Stock Products Report.
     */
    public function lowStockProductsReport(Request $request)
    {
        if ($response = $this->authorizeReportAccess()) {
            return $response;
        }

        $query = Product::where('minimum_stock_level', '>', 0)
                        ->whereRaw('products.minimum_stock_level >= (SELECT COALESCE(SUM(batches.quantity), 0) FROM batches WHERE batches.product_id = products.id)')
                        ->with('category', 'supplier', 'manufacturer', 'productType');

        // กรองชื่อสินค้า
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('product_code', 'like', '%' . $search . '%');
        }

        // กรองผู้ผลิต
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        $lowStockProducts = $query->orderBy('name')->paginate(15);
        $suppliers = \App\Models\Supplier::orderBy('name')->get();

        return view('reports.low_stock_products_report', compact('lowStockProducts', 'suppliers'));
    }

    public function exportLowStock(Request $request)
    {
        if ($response = $this->authorizeReportAccess()) {
            return $response;
        }

        $query = Product::where('minimum_stock_level', '>', 0)
                        ->whereRaw('products.minimum_stock_level >= (SELECT COALESCE(SUM(batches.quantity), 0) FROM batches WHERE batches.product_id = products.id)')
                        ->with('category', 'supplier', 'manufacturer', 'productType');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                ->orWhere('product_code', 'like', '%'.$search.'%');
            });
        }

        if ($request->filled('manufacturer_id')) {
            $query->where('manufacturer_id', $request->manufacturer_id);
        }

        $products = $query->orderBy('name')->get();

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\LowStockExport($products),
            'low_stock_products_' . now()->format('Ymd_His') . '.xlsx'
        );
    }

    /**
     * Display the Expiring Batches Report.
     */
    public function expiringBatchesReport(Request $request)
    {
        if ($response = $this->authorizeReportAccess()) {
            return $response;
        }

        $expirationThresholdDays = 30; // กำหนดจำนวนวันที่ต้องการแจ้งเตือนล่วงหน้า
        $expirationDateLimit = Carbon::now()->addDays($expirationThresholdDays)->endOfDay();

        $expiringBatches = Batch::whereNotNull('expiration_date')
                                ->where('expiration_date', '<=', $expirationDateLimit)
                                ->where('quantity', '>', 0) // เฉพาะล็อตที่มีสต็อก
                                ->with('product') // โหลดข้อมูลสินค้าที่เกี่ยวข้อง
                                ->orderBy('expiration_date', 'asc')
                                ->paginate(15);

        return view('reports.expiring_batches_report', compact('expiringBatches', 'expirationThresholdDays'));
    }

    /**
     * Display the Pending Requisitions Report.
     */
    public function pendingRequisitionsReport(Request $request)
    {
        if ($response = $this->authorizeReportAccess()) {
            return $response;
        }

        $pendingRequisitions = Requisition::where('status', 'pending')
                                        ->with(['user', 'department', 'items.product'])
                                        ->orderBy('requisition_date', 'asc')
                                        ->paginate(15);

        return view('reports.pending_requisitions_report', compact('pendingRequisitions'));
    }
}
