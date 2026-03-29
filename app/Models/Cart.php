<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Cart Model
 *
 * Represents a shopping cart
 * Each user has ONE cart where they store items before checkout
 */
class Cart extends Model
{
    /**
     * The attributes that are mass assignable
     */
    protected $fillable = [
        'user_id',  // Which user owns this cart
    ];

    // ========== RELATIONSHIPS ==========

    /**
     * Get the user that owns this cart
     * Many carts belong to one user (but each user has only one cart)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all items in this cart
     * One cart has many items
     */
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }
}
