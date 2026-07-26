<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('merchants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('company_name');
            $table->string('business_name');
            $table->string('business_type');
            $table->string('website')->nullable();
            $table->string('phone');
            $table->string('email')->unique();
            $table->string('country');
            $table->string('monthly_volume');
            $table->enum('status', ['pending', 'active', 'suspended'])->default('pending');
            $table->enum('kyc_status', ['pending', 'submitted', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
            $table->softDeletes();
            
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->uuid('deleted_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('merchants');
    }
};
