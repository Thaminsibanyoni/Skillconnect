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
        Schema::table('users', function (Blueprint $table) {
            // Precision and scale suitable for geographic coordinates
            $table->decimal('latitude', 10, 8)->nullable()->after('is_online');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            // Consider adding a spatial index later if doing complex geo-queries
            // $table->spatialIndex(['latitude', 'longitude']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Consider dropping spatial index if added
            $table->dropColumn(['latitude', 'longitude']);
        });
    }
};
