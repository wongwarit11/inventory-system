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
        Schema::table('batches', function (Blueprint $table) {
            $table->decimal('purchase_price', 10, 2)->nullable()->after('quantity');
            $table->decimal('sale_price', 10, 2)->nullable()->after('purchase_price');
            $table->dateTime('received_date')->nullable()->after('expiration_date');
            $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('set null')->after('received_date');
            $table->text('notes')->nullable()->after('supplier_id');

            // หากคุณต้องการเปลี่ยนชื่อ manufacture_date เป็น production_date
            // ต้องตรวจสอบว่าคอลัมน์ manufacture_date มีอยู่จริงใน DB ก่อน
            // $table->renameColumn('manufacture_date', 'production_date');
            // แต่ถ้า production_date มีอยู่แล้วใน migration เดิม และ manufacture_date เป็นแค่ชื่อใน DB
            // อาจจะต้องจัดการด้วยมือในฐานข้อมูล หรือสร้าง migration แยกเพื่อ rename
            // ในกรณีนี้ ผมจะถือว่า production_date ใน migration เดิมถูกต้อง และเพิ่มเฉพาะที่หายไป
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('batches', function (Blueprint $table) {
            $table->dropColumn('purchase_price');
            $table->dropColumn('sale_price');
            $table->dropColumn('received_date');
            $table->dropConstrainedForeignId('supplier_id'); // ลบ FK ก่อน drop column
            $table->dropColumn('notes');
            // $table->renameColumn('production_date', 'manufacture_date'); // หากมีการ rename
        });
    }
};