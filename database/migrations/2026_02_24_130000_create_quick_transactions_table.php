<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quick_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('guest_name')->default('Tamu Harian'); // Nama tamu
            $table->string('product_name'); // Nama produk yang dibeli
            $table->string('order_id')->unique(); // ID transaksi unik
            $table->decimal('amount', 15, 2); // Nominal pembayaran
            $table->string('payment_method')->default('Cash'); // Metode bayar
            $table->string('type'); // Kategori: Latihan Harian, Minuman, dll
            $table->timestamp('payment_date'); // Tanggal bayar
            $table->timestamps();
            
            // Index untuk performa
            $table->index('payment_date');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quick_transactions');
    }
};