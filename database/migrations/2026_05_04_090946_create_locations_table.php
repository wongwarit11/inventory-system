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
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('zone')->comment('โซน เช่น A, B, C');
            $table->string('shelf')->comment('ชั้น เช่น 1, 2, 3');
            $table->string('slot')->comment('ช่อง เช่น 1, 2, 3');
            $table->text('description')->nullable()->comment('คำอธิบายตำแหน่ง');
            $table->boolean('is_active')->default(true)->comment('สถานะการใช้งาน');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
