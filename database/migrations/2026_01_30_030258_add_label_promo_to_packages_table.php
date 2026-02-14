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
        // GANTI 'packages' JADI 'pakets'
        Schema::table('pakets', function (Blueprint $table) {
            // Kita taruh kolom baru ini setelah kolom 'harga' (sesuai gambar kamu)
            $table->string('label_promo')->nullable()->after('harga');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pakets', function (Blueprint $table) {
            $table->dropColumn('label_promo');
        });
    }
};