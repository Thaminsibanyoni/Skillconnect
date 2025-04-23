<?php

namespace App\Models;

use App\Models\City; // Import City model
use Illuminate\Database\Eloquent\Factories\HasFactory; // Keep only one
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
        'city_id', // Added city relation
        'total_amount',
        'commission_rate',
        'commission_amount',
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

    /**
     * Get the city associated with the order.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}
