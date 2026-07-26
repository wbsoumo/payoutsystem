<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('merchant_id');
            $table->string('reference_id')->unique();
            $table->string('client_reference_id')->nullable();
            $table->enum('type', ['payout', 'payin', 'refund', 'chargeback'])->default('payout');
            $table->decimal('amount', 15, 4);
            $table->decimal('fee', 15, 4)->default(0.0000);
            $table->decimal('commission', 15, 4)->default(0.0000);
            $table->decimal('gst', 15, 4)->default(0.0000);
            $table->decimal('total_charges', 15, 4)->default(0.0000);
            $table->decimal('opening_balance', 15, 4)->default(0.0000);
            $table->decimal('closing_balance', 15, 4)->default(0.0000);
            $table->enum('status', ['pending', 'processing', 'success', 'failed', 'reversed'])->default('pending');
            $table->string('provider_name')->default('mock');
            $table->string('provider_reference_id')->nullable();
            $table->integer('response_time_ms')->nullable();
            $table->string('ip_address')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('browser')->nullable();
            $table->integer('risk_score')->default(0);
            $table->json('api_request_payload')->nullable();
            $table->json('api_response_payload')->nullable();
            $table->timestamps();
            
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            
            $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('cascade');
            $table->unique(['merchant_id', 'client_reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
