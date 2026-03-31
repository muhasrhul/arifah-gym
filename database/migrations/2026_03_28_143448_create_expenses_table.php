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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->date('expense_date'); // Tanggal pengeluaran
            $table->string('category'); // Kategori pengeluaran
            $table->string('item'); // Item/Barang
            $table->integer('quantity')->nullable(); // Jumlah (optional)
            $table->decimal('amount', 15, 2); // Total pengeluaran
            $table->string('receipt_number')->nullable(); // Nomor nota/kwitansi (optional)
            $table->text('notes')->nullable(); // Catatan tambahan (optional)
            $table->foreignId('created_by')->constrained('users'); // User yang mencatat
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('expenses');
    }
};
