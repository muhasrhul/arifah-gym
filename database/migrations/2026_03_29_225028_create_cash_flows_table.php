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
        Schema::create('cash_flows', function (Blueprint $table) {
            $table->id();
            $table->datetime('date');
            $table->enum('type', ['income', 'expense']);
            $table->enum('source', ['member', 'kasir', 'pengeluaran']);
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('description');
            $table->decimal('amount', 15, 2);
            $table->timestamps();

            $table->index(['date', 'type']);
            $table->index('source');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_flows');
    }
};