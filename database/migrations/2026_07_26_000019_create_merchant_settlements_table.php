<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('merchant_settlements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('merchant_id');
            $table->string('reference_id')->unique();
            $table->decimal('amount', 16, 4);
            $table->decimal('fee', 16, 4)->default(0);
            $table->string('bank_name');
            $table->string('account_number');
            $table->string('ifsc');
            $table->string('status')->default('pending'); // pending, success, failed
            $table->timestamps();

            $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('merchant_settlements');
    }
};
