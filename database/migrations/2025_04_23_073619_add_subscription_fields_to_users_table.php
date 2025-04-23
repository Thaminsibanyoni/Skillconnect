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
            // Store the key of the subscribed plan (e.g., 'basic', 'premium' from config/skillconnect.php)
            $table->string('subscription_plan')->nullable()->after('longitude');
            // Store the status of the subscription
            $table->string('subscription_status')->default('inactive')->after('subscription_plan'); // e.g., inactive, active, cancelled, expired
            // Store when the current subscription period ends
            $table->timestamp('subscription_expires_at')->nullable()->after('subscription_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['subscription_plan', 'subscription_status', 'subscription_expires_at']);
        });
    }
};
