<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('merchants', function (Blueprint $table) {
            $table->string('transaction_pin')->nullable()->after('kyc_status');
            $table->integer('pin_failed_attempts')->default(0)->after('transaction_pin');
            $table->timestamp('pin_locked_until')->nullable()->after('pin_failed_attempts');
        });
    }

    public function down(): void
    {
        Schema::table('merchants', function (Blueprint $table) {
            $table->dropColumn(['transaction_pin', 'pin_failed_attempts', 'pin_locked_until']);
        });
    }
};
