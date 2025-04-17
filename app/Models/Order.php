<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'provider_id',
        'service_id',
        'status',
        'scheduled_at',
        'address',
        'latitude',
        'longitude',
        'total_amount',
        'commission_rate',   // Added
        'commission_amount', // Added
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'scheduled_at' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'total_amount' => 'decimal:2',
        'commission_rate' => 'decimal:2',   // Added casting
        'commission_amount' => 'decimal:2', // Added casting
    ];

    // Relationships

    /**
     * Get the user (seeker) who placed the order.
     */
    public function seeker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user (provider) who fulfilled the order.
     */
    public function provider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    /**
     * Get the service associated with the order.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the rating for the order.
     */
    public function rating(): HasOne
    {
        return $this->hasOne(Rating::class);
    }

    /**
     * Get the transactions associated with the order.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
