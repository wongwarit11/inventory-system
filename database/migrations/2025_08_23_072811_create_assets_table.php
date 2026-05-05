<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * สร้างตาราง 'assets' เพื่อเก็บข้อมูลครุภัณฑ์
     */
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // ชื่อครุภัณฑ์
            $table->string('serial_number')->unique(); // หมายเลขครุภัณฑ์ (ไม่ซ้ำกัน)
            $table->string('description')->nullable(); // รายละเอียด
            $table->unsignedBigInteger('department_id'); // ID ของแผนก
            $table->string('status')->default('available'); // สถานะ: 'available', 'borrowed', 'damaged'
            $table->timestamps();

            // กำหนด Foreign Key สำหรับ department_id
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     * ลบตาราง 'assets' เมื่อ rollback
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
