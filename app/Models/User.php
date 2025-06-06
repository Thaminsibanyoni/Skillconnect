<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany; // Added BelongsToMany
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Laravel\Cashier\Billable; // Removed Billable trait import
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles; // Import HasRoles trait

class User extends Authenticatable
{
    // use Billable; // Removed Billable trait
    use HasApiTokens;
    use HasRoles; // Add HasRoles trait

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // Re-added role
        'status', // Added status
        'is_online', // Added online status
        'bio', // Added bio
        'latitude', // Added latitude
        'longitude', // Added longitude
        'subscription_plan', // Added subscription plan key
        'subscription_status', // Added subscription status
        'subscription_expires_at', // Added subscription expiry
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_online' => 'boolean',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'subscription_expires_at' => 'datetime', // Added cast
        ];
    }

    // Relationships (Re-added)

    /**
     * Get the orders placed by the user (as a seeker).
     */
    public function ordersAsSeeker(): HasMany
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    /**
     * Get the orders fulfilled by the user (as a provider).
     */
    public function ordersAsProvider(): HasMany
    {
        return $this->hasMany(Order::class, 'provider_id');
    }

    /**
     * Get the wallet associated with the user.
     */
    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }

    /**
     * Get the ratings given by the user.
     */
    public function ratingsGiven(): HasMany
    {
        return $this->hasMany(Rating::class, 'user_id');
    }

    /**
     * Get the ratings received by the user (as a provider).
     */
    public function ratingsReceived(): HasMany
    {
        return $this->hasMany(Rating::class, 'provider_id');
    }

    /**
     * Get the transactions associated with the user.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * The services that the user (provider) offers.
     */
    public function services(): BelongsToMany
    {
        // Assuming 'user_id' refers to the provider in the pivot table
        return $this->belongsToMany(Service::class, 'service_user');
        // If pivot table has extra columns like 'rate', add ->withPivot('rate');
    }

    /**
     * The cities that the user (provider) serves.
     */
    public function cities(): BelongsToMany
    {
        return $this->belongsToMany(City::class, 'city_user');
    }
}
