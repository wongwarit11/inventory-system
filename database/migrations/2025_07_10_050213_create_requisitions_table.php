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
        Schema::create('requisitions', function (Blueprint $table) {
            $table->id(); // requisition_id (PK, AUTO_INCREMENT)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // <-- เพิ่มบรรทัดนี้
            $table->string('requisition_number')->unique();
            $table->dateTime('request_date')->useCurrent();
            $table->foreignId('department_id')->constrained('departments'); // FK to departments
            $table->date('requisition_date');
            $table->enum('status', ['pending', 'approved', 'issued', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users'); // FK to users (ผู้อนุมัติ)
            $table->dateTime('approved_date')->nullable();
            $table->foreignId('issued_by')->nullable()->constrained('users'); // FK to users (ผู้จ่ายของ)
            $table->dateTime('issued_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requisitions');
    }
};
