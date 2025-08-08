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
        Schema::create('product_types', function (Blueprint $table) {
            $table->id(); // Primary Key, Auto-increment
            $table->string('name')->unique(); // ชื่อประเภทสินค้า (ต้องไม่ซ้ำกัน)
            $table->text('description')->nullable(); // คำอธิบาย
            $table->enum('status', ['active', 'inactive'])->default('active'); // สถานะ (ใช้งาน/ไม่ใช้งาน)
            $table->timestamps(); // created_at และ updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_types');
    }
};
