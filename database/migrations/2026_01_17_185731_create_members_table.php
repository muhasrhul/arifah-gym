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
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama Member
            $table->string('email')->unique(); // Email
            $table->string('phone'); // No HP
            // Kita pakai ENUM untuk pilihan, bukan select
            $table->enum('type', ['daily', 'monthly']); 
            $table->boolean('is_active')->default(true); // Status Aktif?
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
        Schema::dropIfExists('members');
    }
};