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
        Schema::table('orders', function (Blueprint $table) {
            // Store the commission rate applied (e.g., 10.5 for 10.5% or a fixed amount)
            $table->decimal('commission_rate', 8, 2)->nullable()->after('total_amount');
            // Store the calculated commission amount
            $table->decimal('commission_amount', 10, 2)->nullable()->after('commission_rate');
            // Maybe add a column for commission type ('percentage', 'fixed') later if needed
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['commission_rate', 'commission_amount']);
        });
    }
};
