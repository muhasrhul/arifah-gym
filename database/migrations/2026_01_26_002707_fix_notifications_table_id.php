<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Kita hanya ubah tipenya saja, tidak perlu tambah ->primary() lagi
            $table->uuid('id')->change();
        });
    }

    public function down(): void
    {
        //
    }
};