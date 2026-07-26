<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('user_type'); // admin, merchant_user, system
            $table->uuid('user_id')->nullable();
            $table->uuid('merchant_id')->nullable();
            $table->string('action');
            $table->text('description');
            $table->string('ip_address');
            $table->string('user_agent')->nullable();
            $table->json('payload')->nullable();
            $table->timestamp('created_at')->useCurrent();
            
            $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
