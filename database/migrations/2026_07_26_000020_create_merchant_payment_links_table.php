<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('merchant_payment_links', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('merchant_id');
            $table->string('reference_id')->unique();
            $table->decimal('amount', 16, 4);
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('description')->nullable();
            $table->string('status')->default('pending'); // pending, paid, expired
            $table->timestamps();

            $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('merchant_payment_links');
    }
};
