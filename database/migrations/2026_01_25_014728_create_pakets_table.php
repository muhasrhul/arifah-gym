<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pakets', function (Blueprint $table) {
            $table->id();
            $table->string('nama_paket'); // Contoh: Member Gold 1 Bulan
            $table->decimal('harga', 12, 2); // Contoh: 150000.00
            $table->integer('durasi_hari'); // Contoh: 30
            $table->text('fasilitas')->nullable(); // Contoh: Free WiFi, Personal Trainer, Air Mineral
            $table->boolean('is_active')->default(true); // Untuk aktif/nonaktifkan paket
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pakets');
    }
};