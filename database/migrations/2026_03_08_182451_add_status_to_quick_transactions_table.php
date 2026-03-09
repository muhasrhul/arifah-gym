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
        Schema::table('quick_transactions', function (Blueprint $table) {
            $table->enum('status', ['paid', 'pending'])->default('paid')->after('payment_method');
            $table->string('customer_phone')->nullable()->after('guest_name'); // Untuk nomor telepon pelanggan hutang
            $table->text('notes')->nullable()->after('customer_phone'); // Untuk catatan hutang
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quick_transactions', function (Blueprint $table) {
            $table->dropColumn(['status', 'customer_phone', 'notes']);
        });
    }
};
