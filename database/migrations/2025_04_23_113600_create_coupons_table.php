<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->enum('type', ['fixed', 'percent'])->default('percent');
            $table->decimal('value', 10, 2); // Amount for fixed, percentage for percent
            $table->decimal('min_order_amount', 10, 2)->nullable(); // Minimum order value to apply
            $table->integer('usage_limit')->nullable(); // Max times coupon can be used overall
            $table->integer('usage_limit_per_user')->nullable(); // Max times a single user can use it
            $table->integer('usage_count')->default(0); // How many times it has been used
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
