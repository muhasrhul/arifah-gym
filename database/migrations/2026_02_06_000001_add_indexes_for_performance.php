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
        // Index untuk tabel members
        Schema::table('members', function (Blueprint $table) {
            $table->index('is_active');
            $table->index('expiry_date');
            $table->index(['is_active', 'expiry_date']); // Composite index
        });

        // Index untuk tabel transactions
        Schema::table('transactions', function (Blueprint $table) {
            $table->index('payment_date');
            $table->index('member_id');
        });

        // Index untuk tabel attendances
        Schema::table('attendances', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('member_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
            $table->dropIndex(['expiry_date']);
            $table->dropIndex(['is_active', 'expiry_date']);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['payment_date']);
            $table->dropIndex(['member_id']);
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['member_id']);
        });
    }
};
