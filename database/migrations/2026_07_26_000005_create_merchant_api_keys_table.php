<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('merchant_api_keys', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('merchant_id');
            $table->string('name')->default('Default Key');
            $table->string('api_key_hash')->unique();
            $table->string('api_key_preview');
            $table->text('secret_key_encrypted');
            $table->text('webhook_secret_encrypted');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            
            $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('merchant_api_keys');
    }
};
