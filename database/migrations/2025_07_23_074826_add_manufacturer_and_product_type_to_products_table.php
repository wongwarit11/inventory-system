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
        Schema::table('products', function (Blueprint $table) {
            // เพิ่ม manufacturer_id
            $table->foreignId('manufacturer_id')
                  ->nullable() // กำหนดให้เป็น nullable หากสินค้าบางรายการอาจไม่มีผู้ผลิต
                  ->constrained('manufacturers') // เชื่อมโยงกับตาราง manufacturers
                  ->onDelete('set null'); // หากผู้ผลิตถูกลบ ให้ตั้งค่า manufacturer_id เป็น null

            // เพิ่ม product_type_id
            $table->foreignId('product_type_id')
                  ->nullable() // กำหนดให้เป็น nullable หากสินค้าบางรายการอาจไม่มีประเภทสินค้า
                  ->constrained('product_types') // เชื่อมโยงกับตาราง product_types
                  ->onDelete('set null'); // หากประเภทสินค้าถูกลบ ให้ตั้งค่า product_type_id เป็น null
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // ลบ Foreign Key constraint ก่อน
            $table->dropConstrainedForeignId('manufacturer_id');
            $table->dropConstrainedForeignId('product_type_id');
            // ลบคอลัมน์
            // $table->dropColumn('manufacturer_id'); // dropConstrainedForeignId จะลบคอลัมน์ให้ด้วย
            // $table->dropColumn('product_type_id'); // dropConstrainedForeignId จะลบคอลัมน์ให้ด้วย
        });
    }
};
