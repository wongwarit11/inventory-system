<?php

namespace App\Http\Controllers;

use App\Models\Manufacturer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth; // Import Auth Facade

class ManufacturerController extends Controller
{
    // เมธอดสำหรับตรวจสอบสิทธิ์การเข้าถึง (ใช้ซ้ำๆ ได้)
    private function authorizeStaffAccess()
    {
        if (Auth::user()->role === 'staff') {
            return redirect()->route('dashboard')->with('error', 'คุณไม่มีสิทธิ์เข้าถึงส่วนนี้');
        }
        return null; // ไม่มีข้อผิดพลาดด้านสิทธิ์
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if ($response = $this->authorizeStaffAccess()) {
            return $response;
        }

        $manufacturers = Manufacturer::orderBy('name')->paginate(10);
        return view('manufacturers.index', compact('manufacturers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if ($response = $this->authorizeStaffAccess()) {
            return $response;
        }
        return view('manufacturers.create');
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
            'name' => 'required|string|max:255|unique:manufacturers,name',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive',
        ], [
            'name.required' => 'กรุณากรอกชื่อผู้ผลิต',
            'name.unique' => 'ชื่อผู้ผลิตนี้มีอยู่ในระบบแล้ว',
            'status.required' => 'กรุณาเลือกสถานะ',
            'status.in' => 'สถานะไม่ถูกต้อง',
        ]);

        Manufacturer::create($request->all());
        return redirect()->route('manufacturers.index')->with('success', 'เพิ่มผู้ผลิตสำเร็จแล้ว!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Manufacturer $manufacturer)
    {
        if ($response = $this->authorizeStaffAccess()) {
            return $response;
        }
        return view('manufacturers.show', compact('manufacturer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Manufacturer $manufacturer)
    {
        if ($response = $this->authorizeStaffAccess()) {
            return $response;
        }
        return view('manufacturers.edit', compact('manufacturer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Manufacturer $manufacturer)
    {
        if ($response = $this->authorizeStaffAccess()) {
            return $response;
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('manufacturers')->ignore($manufacturer->id)],
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive',
        ], [
            'name.required' => 'กรุณากรอกชื่อผู้ผลิต',
            'name.unique' => 'ชื่อผู้ผลิตนี้มีอยู่ในระบบแล้ว',
            'status.required' => 'กรุณาเลือกสถานะ',
            'status.in' => 'สถานะไม่ถูกต้อง',
        ]);

        $manufacturer->update($request->all());
        return redirect()->route('manufacturers.index')->with('success', 'อัปเดตผู้ผลิตสำเร็จแล้ว!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Manufacturer $manufacturer)
    {
        if ($response = $this->authorizeStaffAccess()) {
            return $response;
        }

        try {
            $manufacturer->delete();
            return redirect()->route('manufacturers.index')->with('success', 'ลบผู้ผลิตสำเร็จแล้ว!');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == "23000") {
                return redirect()->route('manufacturers.index')->with('error', 'ไม่สามารถลบผู้ผลิตนี้ได้ เนื่องจากมีข้อมูลอื่นที่เกี่ยวข้องอยู่ (เช่น สินค้า)');
            }
            return redirect()->route('manufacturers.index')->with('error', 'เกิดข้อผิดพลาดในการลบข้อมูล: ' . $e->getMessage());
        }
    }
}
