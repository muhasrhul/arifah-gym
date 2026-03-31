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
            $table->longText('digital_signature')->nullable()->after('payment_method');
            $table->timestamp('signature_timestamp')->nullable()->after('digital_signature');
            $table->timestamp('terms_accepted_at')->nullable()->after('signature_timestamp');
            $table->string('agreement_version')->default('1.0')->after('terms_accepted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn(['digital_signature', 'signature_timestamp', 'terms_accepted_at', 'agreement_version']);
        });
    }
};