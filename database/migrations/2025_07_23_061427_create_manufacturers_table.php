<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('manufacturers', function (Blueprint $table) {
            $table->id(); // Primary Key, Auto-increment
            $table->string('name')->unique(); // ชื่อผู้ผลิต (ต้องไม่ซ้ำกัน)
            $table->string('contact_person')->nullable(); // ผู้ติดต่อ
            $table->string('phone')->nullable(); // เบอร์โทรศัพท์
            $table->string('email')->nullable(); // อีเมล
            $table->text('address')->nullable(); // ที่อยู่
            $table->enum('status', ['active', 'inactive'])->default('active'); // สถานะ (ใช้งาน/ไม่ใช้งาน)
            $table->timestamps(); // created_at และ updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manufacturers');
    }
};
