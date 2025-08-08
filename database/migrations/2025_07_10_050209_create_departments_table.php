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
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // ชื่อแผนก, ต้องไม่ซ้ำ
            $table->text('description')->nullable(); // คำอธิบาย, อนุญาตให้ว่างได้
            $table->enum('status', ['active', 'inactive'])->default('active'); // สถานะ, มีค่า active หรือ inactive
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};