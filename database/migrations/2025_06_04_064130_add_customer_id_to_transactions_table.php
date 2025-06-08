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
        Schema::table('transactions', function (Blueprint $table) {
            // Tambahkan kolom customer_id setelah user_id
            // Relasi ke tabel 'customers', bisa null jika transaksi tanpa pelanggan
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null')->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Hapus foreign key dan kolom jika migrasi di-rollback
            $table->dropConstrainedForeignId('customer_id');
        });
    }
};
