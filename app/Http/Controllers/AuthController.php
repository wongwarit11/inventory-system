<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // ใช้สำหรับจัดการการ Login/Logout
use Illuminate\Support\Facades\Hash; // ใช้สำหรับ hash รหัสผ่าน
use App\Models\User; // Import User Model

class AuthController extends Controller
{
    /**
     * แสดงฟอร์ม Login
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        // ตรวจสอบว่าผู้ใช้ Login อยู่แล้วหรือไม่
        if (Auth::check()) {
            return redirect()->route('dashboard'); // ถ้า Login แล้ว ไปที่ Dashboard เลย
        }
        return view('auth.login'); // แสดงหน้า login.blade.php
    }

    /**
     * ดำเนินการ Login ผู้ใช้
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        // 1. ตรวจสอบข้อมูลที่ผู้ใช้กรอก (Validation)
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'username.required' => 'กรุณากรอกชื่อผู้ใช้งาน',
            'password.required' => 'กรุณากรอกรหัสผ่าน',
        ]);

        // 2. พยายาม Login
        // Auth::attempt() จะตรวจสอบ username/password และสร้าง session ให้
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate(); // สร้าง Session ID ใหม่เพื่อความปลอดภัย

            // ตรวจสอบสถานะผู้ใช้ (active/inactive) และ role
            $user = Auth::user();
            if ($user->status === 'inactive') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors([
                    'username' => 'บัญชีผู้ใช้งานนี้ไม่เปิดใช้งานแล้ว',
                ])->onlyInput('username');
            }

            // Login สำเร็จ ไปที่ Dashboard
            return redirect()->intended(route('dashboard'));
        }

        // 3. Login ไม่สำเร็จ
        return back()->withErrors([
            'username' => 'ชื่อผู้ใช้งานหรือรหัสผ่านไม่ถูกต้อง',
        ])->onlyInput('username');
    }

    /**
     * ดำเนินการ Logout ผู้ใช้
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout(); // ทำการ Logout

        $request->session()->invalidate(); // ลบข้อมูล Session ทั้งหมด
        $request->session()->regenerateToken(); // สร้าง CSRF token ใหม่

        return redirect()->route('login')->with('success', 'คุณได้ออกจากระบบเรียบร้อยแล้ว'); // ไปที่หน้า Login พร้อมข้อความสำเร็จ
    }

    
}