<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    // เมธอดสำหรับตรวจสอบสิทธิ์การเข้าถึง (เฉพาะ Admin เท่านั้น)
    private function authorizeAdminAccess()
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'คุณไม่มีสิทธิ์เข้าถึงส่วนนี้');
        }
        return null;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if ($response = $this->authorizeAdminAccess()) {
            return $response;
        }

        $users = User::orderBy('username')->paginate(10);
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if ($response = $this->authorizeAdminAccess()) {
            return $response;
        }
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($response = $this->authorizeAdminAccess()) {
            return $response;
        }

        $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:8|confirmed',
            'fullname' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,manager,staff',
            'status' => 'required|in:active,inactive',
        ], [
            'username.required' => 'กรุณากรอกชื่อผู้ใช้งาน',
            'username.unique' => 'ชื่อผู้ใช้งานนี้มีอยู่ในระบบแล้ว',
            'password.required' => 'กรุณากรอกรหัสผ่าน',
            'password.min' => 'รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร',
            'password.confirmed' => 'การยืนยันรหัสผ่านไม่ตรงกัน',
            'fullname.required' => 'กรุณากรอกชื่อ-นามสกุล',
            'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
            'email.unique' => 'อีเมลนี้มีอยู่ในระบบแล้ว',
            'role.required' => 'กรุณาเลือกบทบาท',
            'role.in' => 'บทบาทไม่ถูกต้อง',
            'status.required' => 'กรุณาเลือกสถานะ',
            'status.in' => 'สถานะไม่ถูกต้อง',
        ]);

        User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'fullname' => $request->fullname,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
            'status' => $request->status,
        ]);

        return redirect()->route('users.index')->with('success', 'เพิ่มผู้ใช้งานใหม่เรียบร้อยแล้ว!');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        if ($response = $this->authorizeAdminAccess()) {
            return $response;
        }
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        if ($response = $this->authorizeAdminAccess()) {
            return $response;
        }
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        if ($response = $this->authorizeAdminAccess()) {
            return $response;
        }

        $request->validate([
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'fullname' => 'required|string|max:255',
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,manager,staff',
            'status' => 'required|in:active,inactive',
        ], [
            'username.required' => 'กรุณากรอกชื่อผู้ใช้งาน',
            'username.unique' => 'ชื่อผู้ใช้งานนี้มีอยู่ในระบบแล้ว',
            'password.min' => 'รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร',
            'password.confirmed' => 'การยืนยันรหัสผ่านไม่ตรงกัน',
            'fullname.required' => 'กรุณากรอกชื่อ-นามสกุล',
            'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
            'email.unique' => 'อีเมลนี้มีอยู่ในระบบแล้ว',
            'role.required' => 'กรุณาเลือกบทบาท',
            'role.in' => 'บทบาทไม่ถูกต้อง',
            'status.required' => 'กรุณาเลือกสถานะ',
            'status.in' => 'สถานะไม่ถูกต้อง',
        ]);

        $userData = $request->except('password', 'password_confirmation');
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        return redirect()->route('users.index')->with('success', 'อัปเดตข้อมูลผู้ใช้งานเรียบร้อยแล้ว!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if ($response = $this->authorizeAdminAccess()) {
            return $response;
        }

        // ป้องกันการลบ Admin ตัวเอง หรือผู้ใช้คนสุดท้ายที่เป็น Admin
        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')->with('error', 'ไม่สามารถลบผู้ใช้งานที่คุณกำลังเข้าสู่ระบบอยู่ได้!');
        }
        if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
            return redirect()->route('users.index')->with('error', 'ไม่สามารถลบผู้ใช้งาน Admin คนสุดท้ายได้!');
        }

        try {
            $user->delete();
            return redirect()->route('users.index')->with('success', 'ลบผู้ใช้งานเรียบร้อยแล้ว!');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == "23000") {
                return redirect()->route('users.index')->with('error', 'ไม่สามารถลบผู้ใช้งานนี้ได้ เนื่องจากมีข้อมูลอื่นที่เกี่ยวข้องอยู่ (เช่น ใบขอเบิก, รายการสต็อก)');
            }
            return redirect()->route('users.index')->with('error', 'เกิดข้อผิดพลาดในการลบข้อมูล: ' . $e->getMessage());
        }
    }
}
