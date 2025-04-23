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
        Schema::create('disputes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade'); // Dispute is about an order
            $table->foreignId('reporter_id')->constrained('users')->comment('User who reported the dispute');
            $table->foreignId('reported_user_id')->constrained('users')->comment('User being reported');
            $table->text('reason');
            $table->enum('status', ['open', 'under_review', 'resolved', 'closed'])->default('open');
            $table->text('resolution_details')->nullable();
            $table->foreignId('resolved_by_admin_id')->nullable()->constrained('users'); // Admin who resolved it
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disputes');
    }
};
