<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Product; // Import Product Model
use App\Models\Location; // Import Location Model
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth; // Import Auth Facade
use Picqer\Barcode\BarcodeGeneratorPNG;
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

        // โหลดความสัมพันธ์ของสินค้า
        $query = Batch::with('product');
        
        // ค้นหาตามเลขล็อตหรือชื่อสินค้า
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('batch_number', 'like', '%' . $search . '%')
                  ->orWhereHas('product', function($q) use ($search) {
                      $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('product_code', 'like', '%' . $search . '%');
                  });
            });
        }
        
        $batches = $query->orderBy('created_at', 'desc')->paginate(15);
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
        $locations = Location::where('is_active', true)->orderBy('zone')->get();
        return view('batches.create', compact('products', 'locations'));
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
            'expiration_date' => 'nullable|date',
            'status' => 'required|in:active,inactive',
            'location_id' => 'nullable|exists:locations,id',
        ], [
            'product_id.required' => 'กรุณาเลือกสินค้า',
            'product_id.exists' => 'สินค้าไม่ถูกต้อง',
            'batch_number.required' => 'กรุณากรอกรหัสล็อต',
            'batch_number.unique' => 'รหัสล็อตนี้มีอยู่ในระบบแล้ว',
            'quantity.required' => 'กรุณากรอกจำนวน',
            'quantity.integer' => 'จำนวนต้องเป็นตัวเลขจำนวนเต็ม',
            'quantity.min' => 'จำนวนต้องไม่น้อยกว่า 0',

            'status.required' => 'กรุณาเลือกสถานะ',
            'status.in' => 'สถานะไม่ถูกต้อง',
            'location_id.exists' => 'ตำแหน่งเก็บสินค้าไม่ถูกต้อง',
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
     * Show the barcode label for the specified batch.
     */
    public function barcode(Batch $batch)
    {
        if ($response = $this->authorizeStaffAccess()) {
            return $response;
        }

        $generator = new BarcodeGeneratorPNG();
        $barcodeData = $generator->getBarcode($batch->batch_number, $generator::TYPE_CODE_128, 2, 60);
        $barcode = 'data:image/png;base64,' . base64_encode($barcodeData);

        return view('barcodes.label', [
            'type' => 'batch',
            'batch' => $batch,
            'barcode' => $barcode,
        ]);
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
        $locations = Location::where('is_active', true)->orderBy('zone')->get();
        return view('batches.edit', compact('batch', 'products', 'locations'));
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
            'expiration_date' => 'nullable|date',
            'status' => 'required|in:active,inactive',
            'location_id' => 'nullable|exists:locations,id',
        ], [
            'product_id.required' => 'กรุณาเลือกสินค้า',
            'product_id.exists' => 'สินค้าไม่ถูกต้อง',
            'batch_number.required' => 'กรุณากรอกรหัสล็อต',
            'batch_number.unique' => 'รหัสล็อตนี้มีอยู่ในระบบแล้ว',
            'quantity.required' => 'กรุณากรอกจำนวน',
            'quantity.integer' => 'จำนวนต้องเป็นตัวเลขจำนวนเต็ม',
            'quantity.min' => 'จำนวนต้องไม่น้อยกว่า 0',
            'status.required' => 'กรุณาเลือกสถานะ',
            'status.in' => 'สถานะไม่ถูกต้อง',
            'location_id.exists' => 'ตำแหน่งเก็บสินค้าไม่ถูกต้อง',
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
