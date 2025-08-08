<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth; // Import Auth Facade

class DepartmentController extends Controller
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
     * แสดงรายการแผนกทั้งหมด
     */
    public function index()
    {
        if ($response = $this->authorizeStaffAccess()) {
            return $response;
        }

        $departments = Department::orderBy('name')->paginate(10);
        return view('departments.index', compact('departments'));
    }

    /**
     * Show the form for creating a new resource.
     * แสดงฟอร์มสำหรับเพิ่มแผนกใหม่
     */
    public function create()
    {
        if ($response = $this->authorizeStaffAccess()) {
            return $response;
        }
        return view('departments.create');
    }

    /**
     * Store a newly created resource in storage.
     * บันทึกข้อมูลแผนกใหม่ลงในฐานข้อมูล
     */
    public function store(Request $request)
    {
        if ($response = $this->authorizeStaffAccess()) {
            return $response;
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:departments,name',
            'description' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive',
        ], [
            'name.required' => 'กรุณากรอกชื่อแผนก',
            'name.unique' => 'ชื่อแผนกนี้มีอยู่ในระบบแล้ว',
            'name.max' => 'ชื่อแผนกต้องไม่เกิน 255 ตัวอักษร',
            'status.required' => 'กรุณาเลือกสถานะ',
            'status.in' => 'สถานะไม่ถูกต้อง',
        ]);

        Department::create($request->all());
        return redirect()->route('departments.index')->with('success', 'เพิ่มแผนกใหม่เรียบรC:\xampp\htdocs\inventory_system\app\Http\Controllers\DepartmentController.phpบร้อยแล้ว!');
    }

    /**
     * Display the specified resource.
     * แสดงรายละเอียดของแผนก (ไม่ค่อยได้ใช้โดยตรงสำหรับ CRUD ทั่วไป แต่มีไว้สำหรับ Resource Route)
     */
    public function show(Department $department)
    {
        if ($response = $this->authorizeStaffAccess()) {
            return $response;
        }
        return view('departments.show', compact('department'));
    }

    /**
     * Show the form for editing the specified resource.
     * แสดงฟอร์มสำหรับแก้ไขข้อมูลแผนกที่มีอยู่
     */
    public function edit(Department $department)
    {
        if ($response = $this->authorizeStaffAccess()) {
            return $response;
        }
        return view('departments.edit', compact('department'));
    }

    /**
     * Update the specified resource in storage.
     * อัปเดตข้อมูลแผนกที่มีอยู่ลงในฐานข้อมูล
     */
    public function update(Request $request, Department $department)
    {
        if ($response = $this->authorizeStaffAccess()) {
            return $response;
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('departments')->ignore($department->id)],
            'description' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive',
        ], [
            'name.required' => 'กรุณากรอกชื่อแผนก',
            'name.unique' => 'ชื่อแผนกนี้มีอยู่ในระบบแล้ว',
            'name.max' => 'ชื่อแผนกต้องไม่เกิน 255 ตัวอักษร',
            'status.required' => 'กรุณาเลือกสถานะ',
            'status.in' => 'สถานะไม่ถูกต้อง',
        ]);

        $department->update($request->all());
        return redirect()->route('departments.index')->with('success', 'อัปเดตข้อมูลแผนกเรียบร้อยแล้ว!');
    }

    /**
     * Remove the specified resource from storage.
     * ลบข้อมูลแผนกออกจากฐานข้อมูล
     */
    public function destroy(Department $department)
    {
        if ($response = $this->authorizeStaffAccess()) {
            return $response;
        }

        try {
            $department->delete();
            return redirect()->route('departments.index')->with('success', 'ลบแผนกเรียบร้อยแล้ว!');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == "23000") {
                return redirect()->route('departments.index')->with('error', 'ไม่สามารถลบแผนกนี้ได้ เนื่องจากมีข้อมูลอื่นที่เกี่ยวข้องอยู่');
            }
            return redirect()->route('departments.index')->with('error', 'เกิดข้อผิดพลาดในการลบข้อมูล: ' . $e->getMessage());
        }
    }
}
