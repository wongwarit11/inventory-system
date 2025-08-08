<?php

namespace App\Http\Controllers;

use App\Models\ProductType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth; // Import Auth Facade

class ProductTypeController extends Controller
{
    // เมธอดสำหรับตรวจสอบสิทธิ์การเข้าถึง (Admin/Manager เท่านั้น)
    private function authorizeStaffAccess()
    {
        if (Auth::user()->role === 'staff') {
            return redirect()->route('dashboard')->with('error', 'คุณไม่มีสิทธิ์เข้าถึงส่วนนี้');
        }
        return null;
    }

    /**
     * Display a listing of the resource.
     * แสดงรายการประเภทสินค้าทั้งหมด
     */
    public function index()
    {
        if ($response = $this->authorizeStaffAccess()) {
            return $response;
        }

        $productTypes = ProductType::orderBy('name')->paginate(10);
        return view('product_types.index', compact('productTypes'));
    }

    /**
     * Show the form for creating a new resource.
     * แสดงฟอร์มสำหรับเพิ่มประเภทสินค้าใหม่
     */
    public function create()
    {
        if ($response = $this->authorizeStaffAccess()) {
            return $response;
        }
        return view('product_types.create');
    }

    /**
     * Store a newly created resource in storage.
     * บันทึกข้อมูลประเภทสินค้าใหม่ลงในฐานข้อมูล
     */
    public function store(Request $request)
    {
        if ($response = $this->authorizeStaffAccess()) {
            return $response;
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:product_types,name',
            'description' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive',
        ], [
            'name.required' => 'กรุณากรอกชื่อประเภทสินค้า',
            'name.unique' => 'ชื่อประเภทสินค้านี้มีอยู่ในระบบแล้ว',
            'status.required' => 'กรุณาเลือกสถานะ',
            'status.in' => 'สถานะไม่ถูกต้อง',
        ]);

        ProductType::create($request->all());
        return redirect()->route('product-types.index')->with('success', 'เพิ่มประเภทสินค้าใหม่เรียบร้อยแล้ว!');
    }

    /**
     * Display the specified resource.
     * แสดงรายละเอียดของประเภทสินค้า (ไม่ค่อยได้ใช้โดยตรงสำหรับ CRUD ทั่วไป)
     */
    public function show(ProductType $productType)
    {
        if ($response = $this->authorizeStaffAccess()) {
            return $response;
        }
        return view('product_types.show', compact('productType'));
    }

    /**
     * Show the form for editing the specified resource.
     * แสดงฟอร์มสำหรับแก้ไขข้อมูลประเภทสินค้าที่มีอยู่
     */
    public function edit(ProductType $productType)
    {
        if ($response = $this->authorizeStaffAccess()) {
            return $response;
        }
        return view('product_types.edit', compact('productType'));
    }

    /**
     * Update the specified resource in storage.
     * อัปเดตข้อมูลประเภทสินค้าที่มีอยู่ลงในฐานข้อมูล
     */
    public function update(Request $request, ProductType $productType)
    {
        if ($response = $this->authorizeStaffAccess()) {
            return $response;
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('product_types')->ignore($productType->id)],
            'description' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive',
        ], [
            'name.required' => 'กรุณากรอกชื่อประเภทสินค้า',
            'name.unique' => 'ชื่อประเภทสินค้านี้มีอยู่ในระบบแล้ว',
            'status.required' => 'กรุณาเลือกสถานะ',
            'status.in' => 'สถานะไม่ถูกต้อง',
        ]);

        $productType->update($request->all());
        return redirect()->route('product-types.index')->with('success', 'อัปเดตข้อมูลประเภทสินค้าเรียบร้อยแล้ว!');
    }

    /**
     * Remove the specified resource from storage.
     * ลบข้อมูลประเภทสินค้าออกจากฐานข้อมูล
     */
    public function destroy(ProductType $productType)
    {
        if ($response = $this->authorizeStaffAccess()) {
            return $response;
        }

        try {
            $productType->delete();
            return redirect()->route('product-types.index')->with('success', 'ลบประเภทสินค้าเรียบร้อยแล้ว!');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == "23000") {
                return redirect()->route('product-types.index')->with('error', 'ไม่สามารถลบประเภทสินค้านี้ได้ เนื่องจากมีข้อมูลอื่นที่เกี่ยวข้องอยู่');
            }
            return redirect()->route('product-types.index')->with('error', 'เกิดข้อผิดพลาดในการลบข้อมูล: ' . $e->getMessage());
        }
    }
}
