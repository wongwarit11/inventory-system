<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Product; // Import Product Model
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth; // Import Auth Facade
use Carbon\Carbon; // Import Carbon for date handling

class BatchController extends Controller
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
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($response = $this->authorizeStaffAccess()) {
            return $response;
        }
            // สร้าง query builder โดยใช้ Model ของล็อตสินค้า
         $query = \App\Models\Batch::query(); 

        // ตรวจสอบว่ามีคำค้นหาใน request หรือไม่
        if ($request->has('search')) {
            $search = $request->input('search');
        
            $query->where(function ($q) use ($search) {
                // ค้นหาจากชื่อสินค้า (Product) ที่มีความสัมพันธ์กับ Batch
                $q->whereHas('product', function ($q_product) use ($search) {
                    $q_product->where('name', 'like', '%' . $search . '%');
                });

                // ค้นหาจากชื่อผู้จัดจำหน่าย (Supplier) ที่มีความสัมพันธ์กับ Batch
                $q->orWhereHas('supplier', function ($q_supplier) use ($search) {
                    $q_supplier->where('name', 'like', '%' . $search . '%');
                });

                // ค้นหาจากรหัสล็อตในตาราง batches
                $q->orWhere('batch_number', 'like', '%' . $search . '%');
            });
    }

    // โหลดความสัมพันธ์ 'product' และ 'supplier' มาพร้อมกันเพื่อหลีกเลี่ยง N+1 query problem
    $batches = $query->with(['product', 'supplier'])->paginate(15); 

    return view('batches.index', compact('batches'));
        // โหลดความสัมพันธ์ของสินค้า
        //$batches = Batch::with('product', 'supplier')->orderBy('batch_number')->paginate(15);
        // return view('batches.index', compact('batches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if ($response = $this->authorizeStaffAccess()) {
            return $response;
        }
        $products = Product::where('status', 'active')->orderBy('name')->get();

        // ดึงข้อมูลผู้จำหน่ายทั้งหมด
        $suppliers = Supplier::all();

        // ส่งข้อมูล products และ suppliers ไปยัง view
        return view('batches.create', compact('products', 'suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($response = $this->authorizeStaffAccess()) {
            return $response;
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'batch_number' => 'required|string|max:255|unique:batches,batch_number',
            'quantity' => 'required|integer|min:0',
            'supplier_id' => 'required|exists:suppliers,id',
            'manufacture_date' => 'nullable|date',
            'expiration_date' => 'nullable|date|after_or_equal:production_date',
            'notes' => 'nullable|string',
            /** 'status' => 'required|in:active,inactive', */
        ], [
            'product_id.required' => 'กรุณาเลือกสินค้า',
            'product_id.exists' => 'สินค้าไม่ถูกต้อง',
            'batch_number.required' => 'กรุณากรอกรหัสล็อต',
            'batch_number.unique' => 'รหัสล็อตนี้มีอยู่ในระบบแล้ว',
            'quantity.required' => 'กรุณากรอกจำนวน',
            'quantity.integer' => 'จำนวนต้องเป็นตัวเลขจำนวนเต็ม',
            'quantity.min' => 'จำนวนต้องไม่น้อยกว่า 0',
            'supplier_id.required' => 'กรุณาเลือกผู้จำหน่าย',
            'supplier_id.exists' => 'ผู้จำหน่ายไม่ถูกต้อง',
            'expiration_date.after_or_equal' => 'วันหมดอายุต้องไม่ก่อนวันผลิต',
            /**'status.required' => 'กรุณาเลือกสถานะ', */
            /**'status.in' => 'สถานะไม่ถูกต้อง', */
        ]);

        Batch::create($request->all());
        return redirect()->route('batches.index')->with('success', 'เพิ่มล็อตสินค้าใหม่เรียบร้อยแล้ว!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Batch $batch)
    {
        if ($response = $this->authorizeStaffAccess()) {
            return $response;
        }
        return view('batches.show', compact('batch'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Batch $batch)
    {
        if ($response = $this->authorizeStaffAccess()) {
            return $response;
        }
        $products = Product::where('status', 'active')->orderBy('name')->get();

        // ดึงข้อมูลผู้จำหน่ายทั้งหมด
        $suppliers = Supplier::all();

        // ส่งข้อมูล products และ suppliers ไปยัง view
        return view('batches.edit', compact('batch','products', 'suppliers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Batch $batch)
    {
        if ($response = $this->authorizeStaffAccess()) {
            return $response;
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'batch_number' => ['required', 'string', 'max:255', Rule::unique('batches')->ignore($batch->id)],
            'quantity' => 'required|integer|min:0',
            'manufacture_date' => 'nullable|date',
            'expiration_date' => 'nullable|date|after_or_equal:production_date',
            'notes' => 'nullable|string',
            /**'status' => 'required|in:active,inactive', */
        ], [
            'product_id.required' => 'กรุณาเลือกสินค้า',
            'product_id.exists' => 'สินค้าไม่ถูกต้อง',
            'batch_number.required' => 'กรุณากรอกรหัสล็อต',
            'batch_number.unique' => 'รหัสล็อตนี้มีอยู่ในระบบแล้ว',
            'quantity.required' => 'กรุณากรอกจำนวน',
            'quantity.integer' => 'จำนวนต้องเป็นตัวเลขจำนวนเต็ม',
            'quantity.min' => 'จำนวนต้องไม่น้อยกว่า 0',
            'expiration_date.after_or_equal' => 'วันหมดอายุต้องไม่ก่อนวันผลิต',
            /**'status.required' => 'กรุณาเลือกสถานะ', */
            /**'status.in' => 'สถานะไม่ถูกต้อง',*/
        ]);

        $batch->update($request->all());
        return redirect()->route('batches.index')->with('success', 'อัปเดตล็อตสินค้าเรียบร้อยแล้ว!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Batch $batch)
    {
        if ($response = $this->authorizeStaffAccess()) {
            return $response;
        }

        try {
            $batch->delete();
            return redirect()->route('batches.index')->with('success', 'ลบล็อตสินค้าเรียบร้อยแล้ว!');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == "23000") {
                return redirect()->route('batches.index')->with('error', 'ไม่สามารถลบล็อตสินค้านี้ได้ เนื่องจากมีข้อมูลอื่นที่เกี่ยวข้องอยู่ (เช่น รายการสต็อก)');
            }
            return redirect()->route('batches.index')->with('error', 'เกิดข้อผิดพลาดในการลบข้อมูล: ' . $e->getMessage());
        }
    }
}
