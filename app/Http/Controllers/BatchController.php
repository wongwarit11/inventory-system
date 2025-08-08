<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Product; // Import Product Model
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
    public function index()
    {
        if ($response = $this->authorizeStaffAccess()) {
            return $response;
        }

        // โหลดความสัมพันธ์ของสินค้า
        $batches = Batch::with('product')->orderBy('batch_number')->paginate(10);
        return view('batches.index', compact('batches'));
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
        return view('batches.create', compact('products'));
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
            'production_date' => 'nullable|date',
            'expiration_date' => 'nullable|date|after_or_equal:production_date',
            'status' => 'required|in:active,inactive',
        ], [
            'product_id.required' => 'กรุณาเลือกสินค้า',
            'product_id.exists' => 'สินค้าไม่ถูกต้อง',
            'batch_number.required' => 'กรุณากรอกรหัสล็อต',
            'batch_number.unique' => 'รหัสล็อตนี้มีอยู่ในระบบแล้ว',
            'quantity.required' => 'กรุณากรอกจำนวน',
            'quantity.integer' => 'จำนวนต้องเป็นตัวเลขจำนวนเต็ม',
            'quantity.min' => 'จำนวนต้องไม่น้อยกว่า 0',
            'expiration_date.after_or_equal' => 'วันหมดอายุต้องไม่ก่อนวันผลิต',
            'status.required' => 'กรุณาเลือกสถานะ',
            'status.in' => 'สถานะไม่ถูกต้อง',
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
        return view('batches.edit', compact('batch', 'products'));
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
            'production_date' => 'nullable|date',
            'expiration_date' => 'nullable|date|after_or_equal:production_date',
            'status' => 'required|in:active,inactive',
        ], [
            'product_id.required' => 'กรุณาเลือกสินค้า',
            'product_id.exists' => 'สินค้าไม่ถูกต้อง',
            'batch_number.required' => 'กรุณากรอกรหัสล็อต',
            'batch_number.unique' => 'รหัสล็อตนี้มีอยู่ในระบบแล้ว',
            'quantity.required' => 'กรุณากรอกจำนวน',
            'quantity.integer' => 'จำนวนต้องเป็นตัวเลขจำนวนเต็ม',
            'quantity.min' => 'จำนวนต้องไม่น้อยกว่า 0',
            'expiration_date.after_or_equal' => 'วันหมดอายุต้องไม่ก่อนวันผลิต',
            'status.required' => 'กรุณาเลือกสถานะ',
            'status.in' => 'สถานะไม่ถูกต้อง',
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
