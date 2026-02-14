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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->string('order_id')->unique(); // ID dari Midtrans
            $table->decimal('amount', 15, 2);
            $table->string('type'); // Contoh: 'Pendaftaran', 'Perpanjangan', 'Kantin'
            $table->string('payment_method')->nullable(); // Contoh: 'QRIS', 'BCA VA'
            $table->timestamp('payment_date');
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
        Schema::dropIfExists('transactions');
    }
};
