<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'currency',
        'interval',
        'interval_count',
        'max_cities',
        'features',
        'is_active',
        // Add gateway plan IDs if needed
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'max_cities' => 'integer',
        'features' => 'array',
        'is_active' => 'boolean',
    ];

    // Relationship to users (optional, if needed)
    // public function users() { ... }
}
