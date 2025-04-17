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
        Schema::table('services', function (Blueprint $table) {
            $table->foreignId('service_category_id')
                  ->nullable()
                  ->after('category') // Place after the old string 'category' column
                  ->constrained('service_categories')
                  ->onDelete('set null'); // Set to null if category is deleted
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['service_category_id']);
            // Then drop the column
            $table->dropColumn('service_category_id');
        });
    }
};
