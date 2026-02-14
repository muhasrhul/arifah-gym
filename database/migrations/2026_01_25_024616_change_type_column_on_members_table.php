<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi untuk memperlebar kolom type.
     */
    public function up()
    {
        Schema::table('members', function (Blueprint $table) {
            // Kita ubah kolom 'type' agar bisa menampung nama paket yang panjang (Varchar 255)
            $table->string('type', 255)->change();
        });
    }

    /**
     * Balikkan perubahan jika diperlukan.
     */
    public function down()
    {
        Schema::table('members', function (Blueprint $table) {
            // Kembalikan ke ukuran semula (misal 50 karakter)
            $table->string('type', 50)->change();
        });
    }
};