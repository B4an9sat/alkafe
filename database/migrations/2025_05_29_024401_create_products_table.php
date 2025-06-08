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
            $table->id();
            $table->string('name')->unique(); // Nama produk, harus unik
            $table->string('sku')->unique()->nullable(); // Stock Keeping Unit, bisa kosong
            $table->text('description')->nullable(); // Deskripsi produk
            $table->decimal('price', 10, 2); // Harga jual, 10 digit total, 2 di belakang koma
            $table->integer('stock')->default(0); // Jumlah stok
            $table->string('image')->nullable(); // Path gambar produk
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null'); // Kategori produk
            $table->timestamps(); // created_at dan updated_at
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