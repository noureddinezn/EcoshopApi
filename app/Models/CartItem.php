<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * CartItem Model
 *
 * Represents a product in a shopping cart
 * Links a cart to a product with quantity information
 */
class CartItem extends Model
{
    /**
     * The attributes that are mass assignable
     */
    protected $fillable = [
        'cart_id',      // Which cart this item belongs to
        'product_id',   // Which product this is
        'quantity',     // How many of this product (e.g., 2)
        'unit_price',   // Price per item when added to cart
    ];

    /**
     * Type casting - convert decimal fields to proper format
     */
    protected $casts = [
        'unit_price' => 'decimal:2',
    ];

    // ========== RELATIONSHIPS ==========

    /**
     * Get the cart this item belongs to
     * Many items belong to one cart
     */
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * Get the product this cart item refers to
     * Many cart items can reference the same product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
