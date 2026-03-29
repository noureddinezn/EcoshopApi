<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * User Model
 *
 * Represents an application user (customer or admin)
 * Users can shop, place orders, and manage their account
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable
     * These fields can be set when creating/updating a user
     */
    protected $fillable = [
        'name',         // User's full name
        'email',        // User's email address (unique)
        'password',     // Hashed password
        'role',         // User role: "user" or "admin"
    ];

    /**
     * The attributes that should be hidden from JSON responses
     * Sensitive data that won't be returned to API clients
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Type casting - convert fields to proper data types
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ========== RELATIONSHIPS ==========

    /**
     * Get this user's shopping cart
     * One user has ONE cart
     */
    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    /**
     * Get all orders placed by this user
     * One user has MANY orders
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
