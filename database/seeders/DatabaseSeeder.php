<?php

namespace Database\Seeders;

use App\Models\User; // Import User Model
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; // Import Hash Facade

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // สร้าง Admin User เพื่อให้เข้าสู่ระบบได้
        User::create([
            'username' => 'admin', // ตรงกับคอลัมน์ 'username' ใน Migration และ $fillable
            'password' => Hash::make('password'), // Hash รหัสผ่าน
            'email' => 'admin@example.com',
            'phone' => '0812345678', // ตัวอย่าง
            'role' => 'admin', // กำหนด role เป็น admin
            'status' => 'active', // กำหนด status เป็น active
            'fullname' => 'Admin System', // ตรงกับคอลัมน์ 'fullname' ใน Migration และ $fillable
        ]);

        // ถ้าคุณต้องการสร้าง User อื่นๆ ด้วย Factory ก็สามารถทำได้
        // User::factory(10)->create();
    }
}
