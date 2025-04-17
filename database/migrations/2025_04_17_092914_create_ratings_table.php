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
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade'); // Link rating to an order
            $table->foreignId('user_id')->constrained('users')->comment('User giving the rating (Seeker)');
            $table->foreignId('provider_id')->constrained('users')->comment('User being rated (Provider)');
            $table->unsignedTinyInteger('rating')->comment('Rating value, e.g., 1-5');
            $table->text('comment')->nullable();
            $table->timestamps();

            // Optional: Add a unique constraint to ensure a user can only rate an order once
            // $table->unique(['order_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
