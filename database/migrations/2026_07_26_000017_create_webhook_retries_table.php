<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webhook_retries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('merchant_id');
            $table->uuid('transaction_id')->nullable();
            $table->string('url');
            $table->json('payload');
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->integer('attempts')->default(0);
            $table->timestamp('last_attempt_at')->nullable();
            $table->timestamp('next_attempt_at')->nullable();
            $table->integer('response_status')->nullable();
            $table->text('response_body')->nullable();
            $table->timestamps();
            
            $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('cascade');
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_retries');
    }
};
