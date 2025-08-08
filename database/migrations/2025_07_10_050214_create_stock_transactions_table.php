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
        Schema::create('stock_transactions', function (Blueprint $table) {
            $table->id(); // transaction_id (PK, AUTO_INCREMENT)
            $table->foreignId('product_id')->constrained('products'); // FK to products
            $table->foreignId('batch_id')->nullable()->constrained('batches'); // FK to batches (nullable เพราะบาง transaction อาจไม่เจาะจง batch)
            $table->enum('transaction_type', ['in', 'out', 'adjustment_in', 'adjustment_out', 'return_to_supplier']);
            $table->integer('quantity');
            $table->dateTime('transaction_date')->useCurrent();
            $table->string('reference_doc')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->constrained('users'); // FK to users (ผู้ทำรายการ)
            $table->foreignId('department_id')->nullable()->constrained('departments'); // FK to departments (สำหรับรายการเบิกออก)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_transactions');
    }
};
