<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('company_name');
            $table->string('business_name');
            $table->string('full_name');
            $table->string('email');
            $table->string('phone');
            $table->string('country');
            $table->string('monthly_volume');
            $table->string('business_type');
            $table->string('website')->nullable();
            $table->text('message')->nullable();
            $table->enum('status', ['pending', 'converted', 'rejected'])->default('pending');
            $table->uuid('converted_merchant_id')->nullable();
            $table->timestamps();
            
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            
            $table->foreign('converted_merchant_id')->references('id')->on('merchants')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_requests');
    }
};
