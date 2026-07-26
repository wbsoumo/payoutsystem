<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('merchant_id')->nullable();
            $table->string('endpoint');
            $table->string('method', 10);
            $table->integer('status_code');
            $table->json('headers')->nullable();
            $table->json('body')->nullable();
            $table->json('response')->nullable();
            $table->integer('execution_time_ms');
            $table->string('source_ip');
            $table->string('user_agent')->nullable();
            $table->boolean('signature_result')->default(false);
            $table->boolean('timestamp_validation')->default(false);
            $table->boolean('nonce_validation')->default(false);
            $table->timestamp('created_at')->useCurrent();
            
            $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_logs');
    }
};
