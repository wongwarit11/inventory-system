<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth; // Import Auth Facade

class SupplierController extends Controller
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

        $suppliers = Supplier::orderBy('name')->paginate(10);
        return view('suppliers.index', compact('suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if ($response = $this->authorizeStaffAccess()) {
            return $response;
        }
        return view('suppliers.create');
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
            'name' => 'required|string|max:255|unique:suppliers,name',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive',
        ], [
            'name.required' => 'กรุณากรอกชื่อผู้จำหน่าย',
            'name.unique' => 'ชื่อผู้จำหน่ายนี้มีอยู่ในระบบแล้ว',
            'status.required' => 'กรุณาเลือกสถานะ',
            'status.in' => 'สถานะไม่ถูกต้อง',
        ]);

        Supplier::create($request->all());
        return redirect()->route('suppliers.index')->with('success', 'เพิ่มผู้จำหน่ายสำเร็จแล้ว!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        if ($response = $this->authorizeStaffAccess()) {
            return $response;
        }
        return view('suppliers.show', compact('supplier'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        if ($response = $this->authorizeStaffAccess()) {
            return $response;
        }
        return view('suppliers.edit', compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        if ($response = $this->authorizeStaffAccess()) {
            return $response;
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('suppliers')->ignore($supplier->id)],
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive',
        ], [
            'name.required' => 'กรุณากรอกชื่อผู้จำหน่าย',
            'name.unique' => 'ชื่อผู้จำหน่ายนี้มีอยู่ในระบบแล้ว',
            'status.required' => 'กรุณาเลือกสถานะ',
            'status.in' => 'สถานะไม่ถูกต้อง',
        ]);

        $supplier->update($request->all());
        return redirect()->route('suppliers.index')->with('success', 'อัปเดตผู้จำหน่ายสำเร็จแล้ว!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        if ($response = $this->authorizeStaffAccess()) {
            return $response;
        }

        try {
            $supplier->delete();
            return redirect()->route('suppliers.index')->with('success', 'ลบผู้จำหน่ายสำเร็จแล้ว!');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == "23000") {
                return redirect()->route('suppliers.index')->with('error', 'ไม่สามารถลบผู้จำหน่ายนี้ได้ เนื่องจากมีข้อมูลอื่นที่เกี่ยวข้องอยู่ (เช่น สินค้า)');
            }
            return redirect()->route('suppliers.index')->with('error', 'เกิดข้อผิดพลาดในการลบข้อมูล: ' . $e->getMessage());
        }
    }
}
