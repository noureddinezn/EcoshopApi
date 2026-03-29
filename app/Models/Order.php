<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Order Model
 *
 * Represents a customer purchase
 * An order is created when a customer checks out their cart
 */
class Order extends Model
{
    /**
     * The attributes that are mass assignable
     */
    protected $fillable = [
        'user_id',       // Which user placed this order
        'total_amount',  // Total cost of all items (with taxes/shipping)
        'status',        // Order status: "pending", "processing", "completed", "cancelled"
    ];

    /**
     * Type casting - convert decimal fields to proper format
     */
    protected $casts = [
        'total_amount' => 'decimal:2',
    ];

    // ========== RELATIONSHIPS ==========

    /**
     * Get the user that placed this order
     * Many orders belong to one user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all items in this order
     * One order has many items
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
