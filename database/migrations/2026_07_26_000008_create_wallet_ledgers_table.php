<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallet_ledgers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('wallet_id');
            $table->enum('type', ['credit', 'debit']);
            $table->decimal('amount', 15, 4);
            $table->decimal('opening_balance', 15, 4);
            $table->decimal('closing_balance', 15, 4);
            $table->string('description');
            $table->string('reference_type')->nullable(); // transaction, manual, settlement
            $table->uuid('reference_id')->nullable();
            $table->timestamps();
            
            $table->uuid('created_by')->nullable();
            
            $table->foreign('wallet_id')->references('id')->on('wallets')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_ledgers');
    }
};
