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
        // Pivot table for many-to-many relationship between users (providers) and services
        Schema::create('service_user', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Provider ID
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->primary(['user_id', 'service_id']); // Composite primary key

            // Add any additional pivot data if needed, e.g., provider's rate for this specific service
            // $table->decimal('rate', 8, 2)->nullable();

            // Timestamps are usually not needed on basic pivot tables
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_user');
    }
};
