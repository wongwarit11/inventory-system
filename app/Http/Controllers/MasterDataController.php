<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department; // <-- Import Department Model
use Illuminate\Validation\Rule; // <-- Import Rule สำหรับ unique validation

class MasterDataController extends Controller
{
    /**
     * แสดงรายการแผนกทั้งหมด
     *
     * @return \Illuminate\View\View
     */
    public function departmentsIndex()
    {
        $departments = Department::orderBy('name')->paginate(10); // ดึงข้อมูลแผนกทั้งหมด พร้อม pagination
        return view('masterdata.departments.index', compact('departments')); // ส่งข้อมูลไปที่ View
    }

    /**
     * แสดงฟอร์มสำหรับเพิ่มแผนกใหม่
     *
     * @return \Illuminate\View\View
     */
    public function departmentsCreate()
    {
        return view('masterdata.departments.create');
    }

    /**
     * จัดเก็บแผนกใหม่ลงในฐานข้อมูล
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function departmentsStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:departments,name', // ชื่อต้องไม่ซ้ำในตาราง departments
            'description' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive',
        ], [
            'name.required' => 'กรุณากรอกชื่อแผนก',
            'name.unique' => 'ชื่อแผนกนี้มีอยู่ในระบบแล้ว',
            'status.required' => 'กรุณาเลือกสถานะ',
            'status.in' => 'สถานะไม่ถูกต้อง',
        ]);

        Department::create($request->all());

        return redirect()->route('departments.index')->with('success', 'เพิ่มแผนกใหม่เรียบร้อยแล้ว!');
    }

    /**
     * แสดงฟอร์มสำหรับแก้ไขข้อมูลแผนก
     *
     * @param  \App\Models\Department  $department
     * @return \Illuminate\View\View
     */
    public function departmentsEdit(Department $department)
    {
        return view('masterdata.departments.edit', compact('department'));
    }

    /**
     * อัปเดตข้อมูลแผนกในฐานข้อมูล
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\RedirectResponse
     */
    public function departmentsUpdate(Request $request, Department $department)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('departments')->ignore($department->id), // ชื่อต้องไม่ซ้ำยกเว้นของตัวเอง
            ],
            'description' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive',
        ], [
            'name.required' => 'กรุณากรอกชื่อแผนก',
            'name.unique' => 'ชื่อแผนกนี้มีอยู่ในระบบแล้ว',
            'status.required' => 'กรุณาเลือกสถานะ',
            'status.in' => 'สถานะไม่ถูกต้อง',
        ]);

        $department->update($request->all());

        return redirect()->route('departments.index')->with('success', 'อัปเดตข้อมูลแผนกเรียบร้อยแล้ว!');
    }

    /**
     * ลบแผนกออกจากฐานข้อมูล
     *
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\RedirectResponse
     */
    public function departmentsDestroy(Department $department)
    {
        try {
            $department->delete();
            return redirect()->route('departments.index')->with('success', 'ลบแผนกเรียบร้อยแล้ว!');
        } catch (\Illuminate\Database\QueryException $e) {
            // ตรวจสอบว่า Error เป็นเพราะ Foreign Key Constraint (ถ้ามีสินค้าผูกกับแผนกนี้)
            if ($e->getCode() == "23000") { // SQLSTATE for Integrity Constraint Violation
                return redirect()->route('departments.index')->with('error', 'ไม่สามารถลบแผนกนี้ได้ เนื่องจากมีข้อมูลอื่นที่เกี่ยวข้องอยู่');
            }
            return redirect()->route('departments.index')->with('error', 'เกิดข้อผิดพลาดในการลบข้อมูล: ' . $e->getMessage());
        }
    }
}