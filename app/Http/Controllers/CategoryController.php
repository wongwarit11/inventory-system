<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth; // Import Auth Facade

class CategoryController extends Controller
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

        $categories = Category::orderBy('name')->paginate(10);
        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if ($response = $this->authorizeStaffAccess()) {
            return $response;
        }
        return view('categories.create');
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
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive',
        ], [
            'name.required' => 'กรุณากรอกชื่อหมวดหมู่',
            'name.unique' => 'ชื่อหมวดหมู่นี้มีอยู่ในระบบแล้ว',
            'status.required' => 'กรุณาเลือกสถานะ',
            'status.in' => 'สถานะไม่ถูกต้อง',
        ]);

        Category::create($request->all());
        return redirect()->route('categories.index')->with('success', 'เพิ่มหมวดหมู่ใหม่เรียบร้อยแล้ว!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        if ($response = $this->authorizeStaffAccess()) {
            return $response;
        }
        return view('categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        if ($response = $this->authorizeStaffAccess()) {
            return $response;
        }
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        if ($response = $this->authorizeStaffAccess()) {
            return $response;
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('categories')->ignore($category->id)],
            'description' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive',
        ], [
            'name.required' => 'กรุณากรอกชื่อหมวดหมู่',
            'name.unique' => 'ชื่อหมวดหมู่นี้มีอยู่ในระบบแล้ว',
            'status.required' => 'กรุณาเลือกสถานะ',
            'status.in' => 'สถานะไม่ถูกต้อง',
        ]);

        $category->update($request->all());
        return redirect()->route('categories.index')->with('success', 'อัปเดตหมวดหมู่เรียบร้อยแล้ว!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        if ($response = $this->authorizeStaffAccess()) {
            return $response;
        }

        try {
            $category->delete();
            return redirect()->route('categories.index')->with('success', 'ลบหมวดหมู่เรียบร้อยแล้ว!');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == "23000") {
                return redirect()->route('categories.index')->with('error', 'ไม่สามารถลบหมวดหมู่นี้ได้ เนื่องจากมีข้อมูลอื่นที่เกี่ยวข้องอยู่');
            }
            return redirect()->route('categories.index')->with('error', 'เกิดข้อผิดพลาดในการลบข้อมูล: ' . $e->getMessage());
        }
    }
}
