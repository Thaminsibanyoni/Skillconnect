<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., Basic, Premium
            $table->string('slug')->unique(); // e.g., basic, premium (used as key)
            $table->text('description')->nullable();
            $table->decimal('price', 8, 2);
            $table->string('currency', 3)->default('ZAR');
            $table->string('interval')->default('month'); // e.g., month, year
            $table->integer('interval_count')->default(1);
            $table->integer('max_cities')->nullable(); // Null for unlimited
            $table->json('features')->nullable(); // Store features as JSON array
            // Add gateway-specific plan IDs if needed (though price ID might suffice)
            // $table->string('payfast_plan_id')->nullable();
            // $table->string('flutterwave_plan_id')->nullable();
            // $table->string('paypal_plan_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
