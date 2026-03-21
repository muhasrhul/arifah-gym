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
        // Fix foreign key constraints - hapus CASCADE DELETE
        
        // 1. Fix transactions table
        Schema::table('transactions', function (Blueprint $table) {
            // Cek dan drop semua foreign key yang ada untuk member_id
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'transactions' 
                AND COLUMN_NAME = 'member_id' 
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");
            
            foreach ($foreignKeys as $fk) {
                try {
                    DB::statement("ALTER TABLE transactions DROP FOREIGN KEY {$fk->CONSTRAINT_NAME}");
                } catch (\Exception $e) {
                    // Ignore if foreign key doesn't exist
                }
            }
            
            // Ubah kolom member_id jadi nullable
            $table->unsignedBigInteger('member_id')->nullable()->change();
            
            // Tambah foreign key baru tanpa cascade (SET NULL)
            $table->foreign('member_id')
                  ->references('id')
                  ->on('members')
                  ->onDelete('set null');
        });
        
        // 2. Fix attendances table
        Schema::table('attendances', function (Blueprint $table) {
            // Cek dan drop semua foreign key yang ada untuk member_id
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'attendances' 
                AND COLUMN_NAME = 'member_id' 
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");
            
            foreach ($foreignKeys as $fk) {
                try {
                    DB::statement("ALTER TABLE attendances DROP FOREIGN KEY {$fk->CONSTRAINT_NAME}");
                } catch (\Exception $e) {
                    // Ignore if foreign key doesn't exist
                }
            }
            
            // Ubah kolom member_id jadi nullable
            $table->unsignedBigInteger('member_id')->nullable()->change();
            
            // Tambah foreign key baru tanpa cascade (SET NULL)
            $table->foreign('member_id')
                  ->references('id')
                  ->on('members')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Rollback: kembalikan ke cascade delete
        
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['member_id']);
            $table->foreign('member_id')
                  ->references('id')
                  ->on('members')
                  ->onDelete('cascade');
        });
        
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['member_id']);
            $table->foreign('member_id')
                  ->references('id')
                  ->on('members')
                  ->onDelete('cascade');
        });
    }
};
