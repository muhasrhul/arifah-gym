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
            // Drop unique constraint dulu
            $table->dropUnique(['fingerprint_id']);
        });
        
        Schema::table('members', function (Blueprint $table) {
            // Ubah kolom fingerprint_id dari integer ke string
            $table->string('fingerprint_id', 50)->nullable()->change();
        });
        
        Schema::table('members', function (Blueprint $table) {
            // Tambahkan unique constraint kembali
            $table->unique('fingerprint_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            // Drop unique constraint
            $table->dropUnique(['fingerprint_id']);
        });
        
        Schema::table('members', function (Blueprint $table) {
            // Kembalikan ke integer jika rollback
            $table->integer('fingerprint_id')->nullable()->change();
        });
        
        Schema::table('members', function (Blueprint $table) {
            // Tambahkan unique constraint kembali
            $table->unique('fingerprint_id');
        });
    }
};