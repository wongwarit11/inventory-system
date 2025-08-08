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

        $batches = Batch::with('product')
                        ->whereHas('product', function($query) {
                            $query->where('status', 'active');
                        })
                        ->orderBy('product_id')
                        ->orderBy('expiration_date', 'asc')
                        ->paginate(15);

        return view('reports.stock_report', compact('batches'));
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

        // ดึงสินค้าที่สต็อกต่ำกว่าจุดต่ำสุด
        $lowStockProducts = Product::where('minimum_stock_level', '>', 0)
                                    ->whereRaw('products.minimum_stock_level >= (SELECT COALESCE(SUM(batches.quantity), 0) FROM batches WHERE batches.product_id = products.id)')
                                    ->with('category', 'supplier', 'manufacturer', 'productType') // โหลดความสัมพันธ์
                                    ->orderBy('name')
                                    ->paginate(15);

        return view('reports.low_stock_products_report', compact('lowStockProducts'));
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
