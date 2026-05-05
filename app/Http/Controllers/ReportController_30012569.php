<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Batch;
use App\Models\Requisition;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Category;
use App\Models\Supplier;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LowStockExport;

class ReportController extends Controller
{
    /**
     * รายงานสต็อกคงเหลือ
     */
    public function stockReport(Request $request)
    {
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
     * ค้นหาแบบเรียลไทม์ในรายงานสต็อก
     */
    public function searchStock(Request $request)
    {
        \Log::info("SEARCH_STOCK_CALLED", ['q' => $request->query('q')]);
        
        $q = $request->query('q'); 

        $results = Batch::with('product')
            ->whereHas('product', function ($query) use ($q) {
                $query->where('name', 'LIKE', "%$q%")
                      ->orWhere('product_code', 'LIKE', "%$q%");
            })
            ->orderBy('product_id')
            ->limit(100)
            ->get();

        $formatted = $results->map(function ($batch) {
            return [
                'product_code' => $batch->product->product_code,
                'name' => $batch->product->name,
                'batch_number' => $batch->batch_number,
                'expiration_date' => $batch->expiration_date 
                    ? date('d/m/Y', strtotime($batch->expiration_date)) 
                    : '-',
                'cost_price' => $batch->product->cost_price,
                'minimum_stock_level' => $batch->product->minimum_stock_level,
                'quantity' => $batch->quantity,
                'unit' => $batch->product->unit ?? '-',
            ];
        });

        return response()->json($formatted);
    }


    /**
     * รายงานการเบิก
     */
    public function requisitionReport(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $statusFilter = $request->input('status_filter');
        $departmentFilter = $request->input('department_id');

        $query = Requisition::with(['user', 'department', 'items.product']);

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

        $departments = Department::orderBy('name')->get();

        return view('reports.requisition_report', compact(
            'requisitions',
            'startDate',
            'endDate',
            'statusFilter',
            'departments',
            'departmentFilter'
        ));
    }


    /**
     * รายงานสินค้าสต็อกต่ำกว่า Min Stock
     */
    public function lowStockReport(Request $request)
    {
        $q = $request->q;

        $query = Product::query()
            ->select('products.id', 'products.name', 'products.product_code', 'products.minimum_stock_level')
            ->selectSub(function ($sub) {
                $sub->from('batches')
                    ->selectRaw('COALESCE(SUM(quantity),0)')
                    ->whereColumn('batches.product_id', 'products.id');
            }, 'stock_sum')
            ->where('minimum_stock_level', '>', 0);

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'LIKE', "%$q%")
                    ->orWhere('product_code', 'LIKE', "%$q%");
            });
        }

        $query->whereRaw('(
            select COALESCE(sum(quantity),0)
            from batches 
            where batches.product_id = products.id
        ) <= products.minimum_stock_level');

        // ✅ จำกัดข้อมูลให้ชัวร์ เพื่อลดโหลด
        $lowStockProducts = $query->paginate(15);

        return view('reports.low_stock_products_report', compact('lowStockProducts', 'q'));
    }

public function searchLowStock(Request $request)
{
    $search = $request->get('search');

    $products = Product::with(['category', 'supplier', 'productType'])
        ->select('products.*')
        ->selectSub(function ($q) {
            $q->from('batches')
              ->selectRaw('COALESCE(SUM(quantity),0)')
              ->whereColumn('batches.product_id', 'products.id');
        }, 'stock_sum')
        ->where('minimum_stock_level', '>', 0)
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('product_code', 'like', "%{$search}%");
            });
        })
        ->whereRaw('
            (SELECT COALESCE(SUM(quantity), 0)
             FROM batches
             WHERE batches.product_id = products.id)
            <= products.minimum_stock_level
        ')
        ->orderBy('name')
        ->paginate(15);

    return response()->json([
        'table' => view('reports.partials.low_stock_table_rows', compact('products'))->render(),
        'pagination' => $products->links('pagination::bootstrap-5')->render(),
        'total' => $products->total()
    ]);
}


    /**
     * รายงานสินค้ากำลังจะหมดอายุ
     */
    public function expiringBatchesReport(Request $request)
    {
        $expirationThresholdDays = 30;
        $expirationDateLimit = Carbon::now()->addDays($expirationThresholdDays)->endOfDay();

        $expiringBatches = Batch::with('product')
            ->whereNotNull('expiration_date')
            ->where('expiration_date', '<=', $expirationDateLimit)
            ->where('quantity', '>', 0)
            ->orderBy('expiration_date', 'asc')
            ->paginate(15);

        return view('reports.expiring_batches_report', compact('expiringBatches', 'expirationThresholdDays'));
    }


    /**
     * รายงานรายการเบิกที่ยังไม่อนุมัติ
     */
    public function pendingRequisitionsReport(Request $request)
    {
        $pendingRequisitions = Requisition::with(['user', 'department', 'items.product'])
            ->where('status', 'pending')
            ->orderBy('requisition_date', 'asc')
            ->paginate(15);

        return view('reports.pending_requisitions_report', compact('pendingRequisitions'));
    }


    /**
     * Export รายงาน Low Stock
     */
    public function exportLowStock(Request $request)
    {
        // ✅ รับค่าค้นหาที่ส่งมาจากฟอร์ม export
        $q = $request->query('search');

        $query = Product::with(['supplier'])
            ->where('minimum_stock_level', '>', 0)
            ->whereRaw('
                (SELECT COALESCE(SUM(quantity), 0)
                FROM batches
                WHERE batches.product_id = products.id)
                <= products.minimum_stock_level
            ');

        // ✅ ถ้ามีคำค้นหา
        if (!empty($q)) {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'LIKE', "%{$q}%")
                    ->orWhere('product_code', 'LIKE', "%{$q}%");
            });
        }

        $filtered = $query->orderBy('name')->get();

        return Excel::download(
            new \App\Exports\LowStockExport($filtered),
            'low_stock_search_result.xlsx'
        );
    }

}

