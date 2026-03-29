<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * OrderItem Model
 *
 * Represents a product in an order
 * Links an order to a product with quantity information
 */
class OrderItem extends Model
{
    /**
     * The attributes that are mass assignable
     */
    protected $fillable = [
        'order_id',     // Which order this item belongs to
        'product_id',   // Which product this is
        'quantity',     // How many of this product (e.g., 2)
        'unit_price',   // Price per item at time of order
        'subtotal',     // Total for this item (quantity × unit_price)
    ];

    /**
     * Type casting - convert decimal fields to proper format
     */
    protected $casts = [
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    // ========== RELATIONSHIPS ==========

    /**
     * Get the order this item belongs to
     * Many items belong to one order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the product this order item refers to
     * Many order items can reference the same product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
