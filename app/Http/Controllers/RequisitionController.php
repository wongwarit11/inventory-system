<?php

namespace App\Http\Controllers;

use App\Models\Requisition;
use App\Models\RequisitionItem;
use App\Models\Product;
use App\Models\Department;
use App\Models\Batch;
use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon; // เพิ่ม Carbon

class RequisitionController extends Controller
{
    /**
     * Display a listing of the resource.
     * แสดงรายการใบขอเบิกทั้งหมด หรือเฉพาะของผู้ใช้ปัจจุบัน (สำหรับ Staff)
     */
    public function index()
    {
        $query = Requisition::with(['user', 'department']);

        // ถ้าบทบาทเป็น Staff ให้แสดงเฉพาะใบขอเบิกของตัวเอง
        if (Auth::user()->role === 'staff') {
            $query->where('user_id', Auth::id());
        }

        $requisitions = $query->orderBy('requisition_date', 'desc')->paginate(10);
        return view('requisitions.index', compact('requisitions'));
    }

    /**
     * Show the form for creating a new resource.
     * แสดงฟอร์มสำหรับสร้างใบขอเบิกใหม่ (ทุกคนสามารถสร้างได้)
     */
    public function create()
    {
        $departments = Department::where('status', 'active')->orderBy('name')->get();
        $products = Product::where('status', 'active')->orderBy('name')->get();
        return view('requisitions.create', compact('departments', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     * บันทึกข้อมูลใบขอเบิกใหม่ (ทุกคนสามารถสร้างได้)
     */
    public function store(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'requisition_date' => 'required|date|before_or_equal:today',
            'notes' => 'nullable|string|max:500',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.requested_quantity' => 'required|integer|min:1',
            'products.*.notes' => 'nullable|string|max:255', // เพิ่ม validation สำหรับ notes ในรายการสินค้า
        ], [
            'department_id.required' => 'กรุณาเลือกแผนกที่ขอเบิก',
            'requisition_date.required' => 'กรุณาเลือกวันที่ขอเบิก',
            'requisition_date.before_or_equal' => 'วันที่ขอเบิกต้องไม่เกินวันนี้',
            'products.required' => 'กรุณาเพิ่มรายการสินค้าที่ต้องการเบิกอย่างน้อย 1 รายการ',
            'products.*.product_id.required' => 'กรุณาเลือกสินค้าสำหรับทุกรายการ',
            'products.*.product_id.exists' => 'สินค้าที่เลือกไม่ถูกต้อง',
            'products.*.requested_quantity.required' => 'กรุณากรอกจำนวนที่ต้องการเบิกสำหรับทุกรายการ',
            'products.*.requested_quantity.integer' => 'จำนวนที่ต้องการเบิกต้องเป็นตัวเลขจำนวนเต็ม',
            'products.*.requested_quantity.min' => 'จำนวนที่ต้องการเบิกต้องมากกว่า 0',
        ]);

        DB::beginTransaction();

        try {
            // สร้างเลขที่ใบขอเบิกอัตโนมัติ
            $datePart = Carbon::parse($request->requisition_date)->format('Ymd');
            $lastRequisition = Requisition::where('requisition_number', 'like', "REQ-{$datePart}-%")
                                        ->orderBy('requisition_number', 'desc')
                                        ->first();
            $nextNumber = 1;
            if ($lastRequisition) {
                $lastNum = (int) substr($lastRequisition->requisition_number, -4);
                $nextNumber = $lastNum + 1;
            }
            $requisitionNumber = "REQ-{$datePart}-" . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);


            $requisition = Requisition::create([
                'requisition_number' => $requisitionNumber,
                'user_id' => Auth::id(),
                'department_id' => $request->department_id,
                'requisition_date' => $request->requisition_date,
                'status' => 'pending', // สถานะเริ่มต้นเป็น 'pending'
                'notes' => $request->notes,
            ]);

            foreach ($request->products as $item) {
                RequisitionItem::create([
                    'requisition_id' => $requisition->id,
                    'product_id' => $item['product_id'],
                    'requested_quantity' => $item['requested_quantity'],
                    'issued_quantity' => 0, // เริ่มต้นเป็น 0 หรือ null
                    'notes' => $item['notes'] ?? null, // หมายเหตุสำหรับแต่ละรายการ
                ]);
            }

            DB::commit();
            return redirect()->route('requisitions.index')->with('success', 'สร้างใบขอเบิกเรียบร้อยแล้ว!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'เกิดข้อผิดพลาดในการสร้างใบขอเบิก: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     * แสดงรายละเอียดของใบขอเบิก
     */
    public function show(Requisition $requisition)
    {
        // ตรวจสอบสิทธิ์การเข้าถึง: Staff ดูได้เฉพาะของตัวเอง
        if (Auth::user()->role === 'staff' && $requisition->user_id !== Auth::id()) {
            return redirect()->route('requisitions.index')->with('error', 'คุณไม่มีสิทธิ์เข้าถึงใบขอเบิกนี้');
        }

        // โหลดรายการสินค้าที่ขอเบิกพร้อมข้อมูลสินค้า, ผู้ใช้, แผนก
        $requisition->load(['items.product', 'user', 'department']);
        return view('requisitions.show', compact('requisition'));
    }

    /**
     * Show the form for editing the specified resource.
     * แสดงฟอร์มสำหรับแก้ไขใบขอเบิก
     */
    public function edit(Requisition $requisition)
    {
        // ตรวจสอบสิทธิ์การแก้ไข: Admin/Manager แก้ไขได้ทั้งหมด, Staff แก้ไขได้เฉพาะของตัวเอง
        if (Auth::user()->role === 'staff' && $requisition->user_id !== Auth::id()) {
            return redirect()->route('requisitions.index')->with('error', 'คุณไม่มีสิทธิ์แก้ไขใบขอเบิกของผู้อื่น');
        }

        // ตรวจสอบสถานะ: แก้ไขได้เฉพาะสถานะ 'pending' เท่านั้น
        if ($requisition->status !== 'pending') {
            return redirect()->route('requisitions.index')->with('error', 'ไม่สามารถแก้ไขใบขอเบิกที่สถานะไม่ใช่ Pending ได้');
        }

        $departments = Department::where('status', 'active')->orderBy('name')->get();
        $products = Product::where('status', 'active')->orderBy('name')->get();
        $requisition->load('items'); // โหลดรายการสินค้าที่ขอเบิก
        return view('requisitions.edit', compact('requisition', 'departments', 'products'));
    }

    /**
     * Update the specified resource in storage.
     * อัปเดตข้อมูลใบขอเบิก
     */
    public function update(Request $request, Requisition $requisition)
    {
        // ตรวจสอบสิทธิ์การแก้ไข: Admin/Manager แก้ไขได้ทั้งหมด, Staff แก้ไขได้เฉพาะของตัวเอง
        if (Auth::user()->role === 'staff' && $requisition->user_id !== Auth::id()) {
            return redirect()->back()->withInput()->with('error', 'คุณไม่มีสิทธิ์แก้ไขใบขอเบิกของผู้อื่น');
        }

        // ตรวจสอบสถานะ: อัปเดตได้เฉพาะสถานะ 'pending' เท่านั้น
        if ($requisition->status !== 'pending') {
            return redirect()->back()->withInput()->with('error', 'ไม่สามารถอัปเดตใบขอเบิกที่สถานะไม่ใช่ Pending ได้');
        }

        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'requisition_date' => 'required|date|before_or_equal:today',
            'status' => 'required|in:pending,approved,issued,cancelled', // สถานะที่สามารถอัปเดตได้
            'notes' => 'nullable|string|max:500',
            'products' => 'required|array|min:1',
            'products.*.item_id' => 'nullable|exists:requisition_items,id',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.requested_quantity' => 'required|integer|min:1',
            'products.*.notes' => 'nullable|string|max:255', // เพิ่ม validation สำหรับ notes ในรายการสินค้า
        ], [
            'department_id.required' => 'กรุณาเลือกแผนกที่ขอเบิก',
            'requisition_date.required' => 'กรุณาเลือกวันที่ขอเบิก',
            'requisition_date.before_or_equal' => 'วันที่ขอเบิกต้องไม่เกินวันนี้',
            'status.required' => 'กรุณาเลือกสถานะ',
            'products.required' => 'กรุณาเพิ่มรายการสินค้าที่ต้องการเบิกอย่างน้อย 1 รายการ',
            'products.*.product_id.required' => 'กรุณาเลือกสินค้าสำหรับทุกรายการ',
            'products.*.product_id.exists' => 'สินค้าที่เลือกไม่ถูกต้อง',
            'products.*.requested_quantity.required' => 'กรุณากรอกจำนวนที่ต้องการเบิกสำหรับทุกรายการ',
            'products.*.requested_quantity.integer' => 'จำนวนที่ต้องการเบิกต้องเป็นตัวเลขจำนวนเต็ม',
            'products.*.requested_quantity.min' => 'จำนวนที่ต้องการเบิกต้องมากกว่า 0',
        ]);

        DB::beginTransaction();

        try {
            $requisition->update([
                'department_id' => $request->department_id,
                'requisition_date' => $request->requisition_date,
                'status' => $request->status,
                'notes' => $request->notes,
            ]);

            // จัดการรายการสินค้าที่ขอเบิก
            $existingItemIds = $requisition->items->pluck('id')->toArray();
            $updatedItemIds = [];

            foreach ($request->products as $itemData) {
                if (isset($itemData['item_id']) && $itemData['item_id']) {
                    // อัปเดตรายการที่มีอยู่
                    $item = RequisitionItem::find($itemData['item_id']);
                    if ($item) {
                        $item->update([
                            'product_id' => $itemData['product_id'],
                            'requested_quantity' => $itemData['requested_quantity'],
                            'notes' => $itemData['notes'] ?? null,
                        ]);
                        $updatedItemIds[] = $item->id;
                    }
                } else {
                    // เพิ่มรายการใหม่
                    $newItem = RequisitionItem::create([
                        'requisition_id' => $requisition->id,
                        'product_id' => $itemData['product_id'],
                        'requested_quantity' => $itemData['requested_quantity'],
                        'issued_quantity' => 0, // ตั้งค่าเริ่มต้นเป็น 0
                        'notes' => $itemData['notes'] ?? null,
                    ]);
                    $updatedItemIds[] = $newItem->id;
                }
            }

            // ลบรายการที่ถูกลบออกจากฟอร์ม
            RequisitionItem::where('requisition_id', $requisition->id)
                            ->whereNotIn('id', $updatedItemIds)
                            ->delete();

            DB::commit();
            return redirect()->route('requisitions.index')->with('success', 'อัปเดตใบขอเบิกเรียบร้อยแล้ว!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'เกิดข้อผิดพลาดในการอัปเดตใบขอเบิก: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     * ลบใบขอเบิก
     */
    public function destroy(Requisition $requisition)
    {
        // ตรวจสอบสิทธิ์การลบ: Admin เท่านั้นที่ลบได้
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('requisitions.index')->with('error', 'คุณไม่มีสิทธิ์ลบใบขอเบิก');
        }

        // ตรวจสอบสถานะ: ลบได้เฉพาะสถานะ 'pending' เท่านั้น
        if ($requisition->status !== 'pending') {
            return redirect()->route('requisitions.index')->with('error', 'ไม่สามารถลบใบขอเบิกที่สถานะไม่ใช่ Pending ได้');
        }

        DB::beginTransaction();
        try {
            // ลบรายการสินค้าในใบขอเบิกก่อน
            $requisition->items()->delete();
            // แล้วค่อยลบใบขอเบิก
            $requisition->delete();

            DB::commit();
            return redirect()->route('requisitions.index')->with('success', 'ลบใบขอเบิกเรียบร้อยแล้ว!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('requisitions.index')->with('error', 'ไม่สามารถลบใบขอเบิกได้: ' . $e->getMessage());
        }
    }

    /**
     * Process a requisition (e.g., approve and issue stock)
     * ดำเนินการเบิกสินค้า
     */
    public function processRequisition(Request $request, Requisition $requisition)
    {
        // ตรวจสอบสิทธิ์การดำเนินการ: Admin/Manager เท่านั้น
        if (Auth::user()->role === 'staff') {
            return redirect()->back()->withInput()->with('error', 'คุณไม่มีสิทธิ์ดำเนินการเบิกสินค้า');
        }

        // ตรวจสอบสถานะ: ดำเนินการได้เฉพาะสถานะ 'pending' หรือ 'approved' เท่านั้น
        if (!in_array($requisition->status, ['pending', 'approved'])) {
            return redirect()->back()->withInput()->with('error', 'ไม่สามารถดำเนินการเบิกใบขอเบิกที่สถานะไม่ใช่ Pending หรือ Approved ได้');
        }

        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:requisition_items,id',
            'items.*.issued_quantity' => 'required|integer|min:0',
            'items.*.batch_id' => 'nullable|exists:batches,id', // ต้องระบุ batch_id เมื่อมีการเบิกจริง
        ], [
            'items.required' => 'กรุณาเพิ่มรายการที่ต้องการดำเนินการเบิก',
            'items.*.item_id.required' => 'ไม่พบ ID รายการขอเบิก',
            'items.*.issued_quantity.required' => 'กรุณากรอกจำนวนที่เบิกให้จริง',
            'items.*.issued_quantity.integer' => 'จำนวนที่เบิกให้จริงต้องเป็นตัวเลขจำนวนเต็ม',
            'items.*.issued_quantity.min' => 'จำนวนที่เบิกให้จริงต้องไม่น้อยกว่า 0',
            'items.*.batch_id.exists' => 'ล็อตสินค้าที่เลือกไม่ถูกต้อง',
        ]);

        DB::beginTransaction();
        try {
            $allIssued = true; // Flag เพื่อตรวจสอบว่าทุกรายการถูกเบิกครบหรือไม่

            foreach ($request->items as $itemData) {
                $reqItem = RequisitionItem::find($itemData['item_id']);
                if (!$reqItem) {
                    DB::rollBack();
                    return redirect()->back()->with('error', 'ไม่พบรายการขอเบิก.');
                }

                $issuedQuantity = $itemData['issued_quantity'];
                $batchId = $itemData['batch_id'];

                // ตรวจสอบว่าจำนวนที่เบิกให้จริงไม่เกินจำนวนที่ขอเบิก
                if ($issuedQuantity > $reqItem->requested_quantity) {
                    DB::rollBack();
                    return redirect()->back()->with('error', 'จำนวนที่เบิกให้จริงสำหรับ ' . ($reqItem->product->name ?? 'สินค้า') . ' เกินกว่าที่ขอเบิก.');
                }

                // ถ้ามีการเบิกให้จริง (issuedQuantity > 0)
                if ($issuedQuantity > 0) {
                    if (!$batchId) {
                        DB::rollBack();
                        return redirect()->back()->with('error', 'กรุณาเลือกล็อตสินค้าสำหรับ ' . ($reqItem->product->name ?? 'สินค้า') . ' ที่มีการเบิกให้จริง.');
                    }

                    $batch = Batch::find($batchId);
                    if (!$batch || $batch->quantity < $issuedQuantity) {
                        DB::rollBack();
                        return redirect()->back()->with('error', 'สต็อกไม่เพียงพอสำหรับ ' . ($reqItem->product->name ?? 'สินค้า') . ' ล็อต ' . ($batch->batch_number ?? '') . '. มีในสต็อก: ' . ($batch->quantity ?? 0));
                    }

                    // ลดจำนวนในล็อต
                    $batch->quantity -= $issuedQuantity;
                    $batch->save();

                    // สร้างรายการ Stock Transaction (ประเภท 'out')
                    StockTransaction::create([
                        'product_id' => $reqItem->product_id,
                        'batch_id' => $batchId,
                        'user_id' => Auth::id(),
                        'department_id' => $requisition->department_id,
                        'transaction_type' => 'out',
                        'quantity' => $issuedQuantity,
                        'transaction_date' => now(),
                        'reference_doc' => 'REQ-' . $requisition->requisition_number, // อ้างอิงถึงใบขอเบิก
                        'notes' => 'เบิกตามใบขอเบิก #' . $requisition->requisition_number . ' รายการ: ' . ($reqItem->product->name ?? ''),
                    ]);
                }

                // อัปเดต issued_quantity ใน RequisitionItem
                $reqItem->issued_quantity = $issuedQuantity;
                $reqItem->save();

                // ตรวจสอบว่าทุกรายการถูกเบิกครบหรือไม่
                if ($reqItem->issued_quantity < $reqItem->requested_quantity) {
                    $allIssued = false;
                }
            }

            // อัปเดตสถานะใบขอเบิก
            // ใช้ 'issued' หากเบิกครบทุกรายการ หรือ 'approved' หากเบิกบางส่วน
            if ($allIssued) {
                $requisition->status = 'issued';
            } else {
                $requisition->status = 'approved';
            }
            $requisition->save();

            DB::commit();
            return redirect()->route('requisitions.show', $requisition->id)->with('success', 'ดำเนินการเบิกสินค้าเรียบร้อยแล้ว!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'เกิดข้อผิดพลาดในการดำเนินการเบิกสินค้า: ' . $e->getMessage());
        }
    }
}
