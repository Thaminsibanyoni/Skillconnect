<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany; // Added

class City extends Model
{
    use HasFactory;
    public $timestamps = false; // No timestamps needed

    protected $fillable = ['name', 'province_id'];

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    /**
     * The providers that serve this city.
     */
    public function providers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'city_user');
    }
}
