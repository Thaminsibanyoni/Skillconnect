<?php

namespace App\Traits;

use App\Models\AdminActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request; // Import Request facade

trait LogsAdminActivity
{
    /**
     * Log an admin activity.
     *
     * @param string $action The action performed (e.g., 'created_user', 'updated_order').
     * @param Model|null $target The model instance that was affected.
     * @param array|null $details Additional details to log as JSON.
     */
    protected function logAdminActivity(string $action, ?Model $target = null, ?array $details = null): void
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            // Don't log if not an authenticated admin
            return;
        }

        try {
            AdminActivityLog::create([
                'admin_user_id' => Auth::id(),
                'action' => $action,
                'target_type' => $target ? $target->getMorphClass() : null,
                'target_id' => $target ? $target->getKey() : null,
                'details' => $details,
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ]);
        } catch (\Exception $e) {
            // Log error but don't break the main action
            Log::error('Failed to log admin activity:', [
                'action' => $action,
                'admin_user_id' => Auth::id(),
                'target_type' => $target ? $target->getMorphClass() : null,
                'target_id' => $target ? $target->getKey() : null,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
