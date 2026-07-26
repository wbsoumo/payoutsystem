<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('merchant_id')->nullable(); // Null indicates global commission
            $table->string('name');
            $table->enum('type', ['fixed', 'percentage', 'slab'])->default('percentage');
            $table->decimal('fixed_charge', 15, 4)->default(0.0000);
            $table->decimal('percentage_charge', 5, 2)->default(0.00);
            $table->json('slab_rates')->nullable();
            $table->decimal('min_charge', 15, 4)->nullable();
            $table->decimal('max_charge', 15, 4)->nullable();
            $table->decimal('gst_rate', 5, 2)->default(18.00); // 18% GST default
            $table->date('effective_date');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            
            $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commissions');
    }
};
