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
        Schema::table('members', function (Blueprint $table) {
            // Ubah email menjadi nullable dan hapus unique constraint
            $table->dropUnique(['email']);
            $table->string('email')->nullable()->change();
            $table->unique('email', 'members_email_unique_nullable')->where('email', '!=', null);
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
            // Kembalikan email menjadi required dan unique
            $table->dropIndex('members_email_unique_nullable');
            $table->string('email')->nullable(false)->change();
            $table->unique('email');
        });
    }
};
