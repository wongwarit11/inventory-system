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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('password'); // Laravel จะจัดการ hash ให้เอง
            $table->string('fullname')->nullable();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->enum('role', ['admin', 'manager', 'staff'])->default('staff'); // เพิ่ม role
            $table->enum('status', ['active', 'inactive'])->default('active'); // เพิ่ม status
            $table->rememberToken();
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
