<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany; // Added BelongsToMany
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        // 'category', // Removed old string category
        'service_category_id', // Added foreign key
    ];

    /**
     * Get the orders for the service.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the category that the service belongs to.
     */
    public function serviceCategory(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class);
    }

    /**
     * The providers that offer this service.
     */
    public function providers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'service_user');
        // If pivot table has extra columns like 'rate', add ->withPivot('rate');
    }
}
