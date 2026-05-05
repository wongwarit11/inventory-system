<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * สร้างตาราง 'borrows' เพื่อบันทึกการยืมคืน
     */
    public function up(): void
    {
        Schema::create('borrows', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('asset_id'); // ID ครุภัณฑ์ที่ถูกยืม
            $table->unsignedBigInteger('user_id'); // ID ผู้ยืม
            $table->dateTime('borrowed_at'); // วันที่และเวลายืม
            $table->dateTime('returned_at')->nullable(); // วันที่และเวลาที่คืน (อาจเป็นค่าว่างได้)
            $table->timestamps();

            // กำหนด Foreign Key สำหรับ asset_id
            $table->foreign('asset_id')->references('id')->on('assets')->onDelete('cascade');

            // กำหนด Foreign Key สำหรับ user_id
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     * ลบตาราง 'borrows' เมื่อ rollback
     */
    public function down(): void
    {
        Schema::dropIfExists('borrows');
    }
};
