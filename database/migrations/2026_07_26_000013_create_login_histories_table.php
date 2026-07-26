<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('login_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('user_type'); // admin, merchant_user
            $table->uuid('user_id')->nullable();
            $table->string('email');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->decimal('accuracy', 8, 2)->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('timezone')->nullable();
            $table->string('ip_address');
            $table->string('browser')->nullable();
            $table->string('operating_system')->nullable();
            $table->string('device_type')->nullable(); // desktop, mobile, tablet
            $table->string('screen_resolution')->nullable();
            $table->string('language')->nullable();
            $table->enum('status', ['success', 'failed'])->default('success');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_histories');
    }
};
