<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Insert default Jiopay credentials settings
        DB::table('settings')->insert([
            ['key' => 'jiopay_mid', 'value' => 'YOUR_BHARAT_MID', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'jiopay_key', 'value' => 'YOUR_BHARAT_KEY', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'jiopay_entity_id', 'value' => '3173ad0e-xxxx-xxxxxx-9c57830b2d07', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'jiopay_customer_id', 'value' => 'CUST10001', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
