<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subject',
        'message',
        'status',
        'priority',
        'assigned_admin_id',
        'resolved_at',
    ];

     protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedAdmin(): BelongsTo
    {
         // Use withDefault to avoid errors if admin is deleted
        return $this->belongsTo(User::class, 'assigned_admin_id')->withDefault([
            'name' => 'Unassigned'
        ]);
    }

    // TODO: Add relationship for ticket replies/messages later
}
