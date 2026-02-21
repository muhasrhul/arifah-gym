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
        Schema::table('members', function (Blueprint $table) {
            if (Schema::hasColumn('members', 'actual_biaya_paket')) {
                $table->dropColumn('actual_biaya_paket');
            }
            if (Schema::hasColumn('members', 'actual_biaya_registrasi')) {
                $table->dropColumn('actual_biaya_registrasi');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->decimal('actual_biaya_paket', 15, 2)->nullable();
            $table->decimal('actual_biaya_registrasi', 15, 2)->nullable();
        });
    }
};
