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
        Schema::create('products', function (Blueprint $table) {
            $table->id(); // product_id (PK, AUTO_INCREMENT)
            $table->string('product_code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('category_id')->constrained('categories'); // FK to categories
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade'); // Foreign Key ไปยังตาราง suppliers
            $table->string('unit');
            $table->decimal('cost_price', 10, 2);
            $table->integer('current_stock')->default(0);
            $table->integer('min_stock_level')->default(0);
            $table->integer('minimum_stock_level')->default(0);
            $table->string('location')->nullable();
            $table->string('image_path')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
