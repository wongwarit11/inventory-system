<?php

namespace App\Http\Controllers;

use App\Models\StockTransaction;
use App\Models\Product;
use App\Models\Batch;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockTransactionController extends Controller
{
    // เมธอดสำหรับตรวจสอบสิทธิ์การเข้าถึง (ใช้ซ้ำๆ ได้)
    private function authorizeStaffAccess()
    {
        if (Auth::user()->role === 'staff') {
            return redirect()->route('dashboard')->with('error', 'คุณไม่มีสิทธิ์เข้าถึงส่วนนี้');
        }
        return null;
    }

    /**
     * Display a listing of the resource (รายการการทำรายการสต็อกทั้งหมด).
     */
    public function index()
    {
        if ($response = $this->authorizeStaffAccess()) {
            return $response;
        }

        $transactions = StockTransaction::with(['product', 'batch', 'user', 'department'])
                                ->orderBy('transaction_date', 'desc')
                                ->paginate(10);
        return view('stock_transactions.index', compact('transactions'));
    }

    /**
     * Show the form for creating a new "Receive" transaction.
     */
    public function createReceive()
    {
        if ($response = $this->authorizeStaffAccess()) {
            return $response;
        }
        $products = Product::where('status', 'active')->orderBy('name')->get();
        $departments = Department::where('status', 'active')->orderBy('name')->get();
        return view('stock_transactions.receive', compact('products', 'departments'));
    }

    /**
     * Store a newly created "Receive" transaction in storage.
     */
    public function storeReceive(Request $request)
    {
        if ($response = $this->authorizeStaffAccess()) {
            return $response;
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'batch_id' => 'nullable|exists:batches,id',
            'quantity' => 'required|integer|min:1',
            'transaction_date' => 'required|date',
            'notes' => 'nullable|string|max:500', // ใช้ notes แทน remark
            'department_id' => 'nullable|exists:departments,id',
        ], [
            'product_id.required' => 'กรุณาเลือกสินค้า',
            'product_id.exists' => 'สินค้าไม่ถูกต้อง',
            'quantity.required' => 'กรุณากรอกจำนวนที่รับเข้า',
            'quantity.integer' => 'จำนวนต้องเป็นตัวเลขจำนวนเต็ม',
            'quantity.min' => 'จำนวนต้องมากกว่า 0',
            'transaction_date.required' => 'กรุณาเลือกวันที่ทำรายการ',
            'transaction_date.date' => 'รูปแบบวันที่ทำรายการไม่ถูกต้อง',
            'department_id.exists' => 'แผนกไม่ถูกต้อง',
        ]);

        DB::beginTransaction();

        try {
            $transaction = StockTransaction::create([
                'product_id' => $request->product_id,
                'batch_id' => $request->batch_id,
                'user_id' => Auth::id(),
                'department_id' => $request->department_id,
                'transaction_type' => 'in',
                'quantity' => $request->quantity,
                'transaction_date' => $request->transaction_date,
                'reference_doc' => null, // หากไม่มีช่องสำหรับ reference_doc ในฟอร์มนี้ ให้ใส่ null หรือค่าว่าง
                'notes' => $request->notes,
            ]);

            if ($request->batch_id) {
                $batch = Batch::find($request->batch_id);
                if ($batch) {
                    $batch->quantity += $request->quantity;
                    $batch->save();
                }
            } else {
                DB::rollBack();
                return redirect()->back()->withInput()->with('error', 'กรุณาเลือกล็อตสินค้า หรือเพิ่มล็อตสินค้าใหม่ก่อน');
            }

            DB::commit();
            return redirect()->route('stock_transactions.index')->with('success', 'บันทึกการรับเข้าสินค้าเรียบร้อยแล้ว!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'เกิดข้อผิดพลาดในการบันทึกรายการรับเข้า: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new "Issue" transaction.
     */
    public function createIssue()
    {
        if ($response = $this->authorizeStaffAccess()) {
            return $response;
        }
        $products = Product::where('status', 'active')->orderBy('name')->get();
        $departments = Department::where('status', 'active')->orderBy('name')->get();
        return view('stock_transactions.issue', compact('products', 'departments'));
    }

    /**
     * Store a newly created "Issue" transaction in storage.
     */
    public function storeIssue(Request $request)
    {
        if ($response = $this->authorizeStaffAccess()) {
            return $response;
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'batch_id' => 'required|exists:batches,id',
            'quantity' => 'required|integer|min:1',
            'transaction_date' => 'required|date',
            'notes' => 'nullable|string|max:500', // ใช้ notes แทน remark
            'department_id' => 'required|exists:departments,id',
        ], [
            'product_id.required' => 'กรุณาเลือกสินค้า',
            'product_id.exists' => 'สินค้าไม่ถูกต้อง',
            'batch_id.required' => 'กรุณาเลือกล็อตสินค้า',
            'batch_id.exists' => 'ล็อตสินค้าไม่ถูกต้อง',
            'quantity.required' => 'กรุณากรอกจำนวนที่จ่ายออก',
            'quantity.integer' => 'จำนวนต้องเป็นตัวเลขจำนวนเต็ม',
            'quantity.min' => 'จำนวนต้องมากกว่า 0',
            'transaction_date.required' => 'กรุณาเลือกวันที่ทำรายการ',
            'transaction_date.date' => 'รูปแบบวันที่ทำรายการไม่ถูกต้อง',
            'department_id.required' => 'กรุณาเลือกแผนกที่ขอเบิก',
            'department_id.exists' => 'แผนกไม่ถูกต้อง',
        ]);

        DB::beginTransaction();

        try {
            $batch = Batch::find($request->batch_id);

            if (!$batch) {
                DB::rollBack();
                return redirect()->back()->withInput()->with('error', 'ไม่พบข้อมูลล็อตสินค้าที่เลือก.');
            }

            if ($batch->quantity < $request->quantity) {
                DB::rollBack();
                return redirect()->back()->withInput()->with('error', 'จำนวนสินค้าในล็อตไม่เพียงพอสำหรับการจ่ายออก. มีในสต็อก: ' . $batch->quantity);
            }

            $transaction = StockTransaction::create([
                'product_id' => $request->product_id,
                'batch_id' => $request->batch_id,
                'user_id' => Auth::id(),
                'department_id' => $request->department_id,
                'transaction_type' => 'out',
                'quantity' => $request->quantity,
                'transaction_date' => $request->transaction_date,
                'reference_doc' => null, // หากไม่มีช่องสำหรับ reference_doc ในฟอร์มนี้ ให้ใส่ null หรือค่าว่าง
                'notes' => $request->notes,
            ]);

            $batch->quantity -= $request->quantity;
            $batch->save();

            DB::commit();
            return redirect()->route('stock_transactions.index')->with('success', 'บันทึกการจ่ายออกสินค้าเรียบร้อยแล้ว!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'เกิดข้อผิดพลาดในการบันทึกรายการจ่ายออก: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new "Adjust" transaction.
     */
    public function createAdjust()
    {
        if ($response = $this->authorizeStaffAccess()) {
            return $response;
        }
        $products = Product::where('status', 'active')->orderBy('name')->get();
        $departments = Department::where('status', 'active')->orderBy('name')->get();
        return view('stock_transactions.adjust', compact('products', 'departments'));
    }

    /**
     * Store a newly created "Adjust" transaction in storage.
     */
    public function storeAdjust(Request $request)
    {
        if ($response = $this->authorizeStaffAccess()) {
            return $response;
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'batch_id' => 'required|exists:batches,id',
            'quantity' => 'required|integer',
            'transaction_date' => 'required|date',
            'notes' => 'nullable|string|max:500', // ใช้ notes แทน remark
            'department_id' => 'nullable|exists:departments,id',
        ], [
            'product_id.required' => 'กรุณาเลือกสินค้า',
            'product_id.exists' => 'สินค้าไม่ถูกต้อง',
            'batch_id.required' => 'กรุณาเลือกล็อตสินค้า',
            'batch_id.exists' => 'ล็อตสินค้าไม่ถูกต้อง',
            'quantity.required' => 'กรุณากรอกจำนวนที่ปรับปรุง',
            'quantity.integer' => 'จำนวนต้องเป็นตัวเลขจำนวนเต็ม',
            'transaction_date.required' => 'กรุณาเลือกวันที่ทำรายการ',
            'transaction_date.date' => 'รูปแบบวันที่ทำรายการไม่ถูกต้อง',
            'department_id.exists' => 'แผนกไม่ถูกต้อง',
        ]);

        DB::beginTransaction();

        try {
            $batch = Batch::find($request->batch_id);

            if (!$batch) {
                DB::rollBack();
                return redirect()->back()->withInput()->with('error', 'ไม่พบข้อมูลล็อตสินค้าที่เลือก.');
            }

            $transactionType = 'adjustment_in';
            if ($request->quantity < 0) {
                $transactionType = 'adjustment_out';
            }

            if (($batch->quantity + $request->quantity) < 0) {
                DB::rollBack();
                return redirect()->back()->withInput()->with('error', 'ไม่สามารถปรับลดจำนวนได้ เนื่องจากสต็อกจะติดลบ. มีในสต็อก: ' . $batch->quantity);
            }

            $transaction = StockTransaction::create([
                'product_id' => $request->product_id,
                'batch_id' => $request->batch_id,
                'user_id' => Auth::id(),
                'department_id' => $request->department_id,
                'transaction_type' => $transactionType,
                'quantity' => $request->quantity,
                'transaction_date' => $request->transaction_date,
                'reference_doc' => null, // หากไม่มีช่องสำหรับ reference_doc ในฟอร์มนี้ ให้ใส่ null หรือค่าว่าง
                'notes' => $request->notes,
            ]);

            $batch->quantity += $request->quantity;
            $batch->save();

            DB::commit();
            return redirect()->route('stock_transactions.index')->with('success', 'บันทึกการปรับปรุงสต็อกเรียบร้อยแล้ว!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'เกิดข้อผิดพลาดในการบันทึกรายการปรับปรุง: ' . $e->getMessage());
        }
    }
}
