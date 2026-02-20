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
            if (Schema::hasColumn('members', 'actual_package_price')) {
                $table->dropColumn('actual_package_price');
            }
            if (Schema::hasColumn('members', 'actual_registration_fee')) {
                $table->dropColumn('actual_registration_fee');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->decimal('actual_package_price', 15, 2)->nullable();
            $table->decimal('actual_registration_fee', 15, 2)->nullable();
        });
    }
};
