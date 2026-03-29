<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Product Model
 *
 * Represents products available for sale
 * Products belong to a category and can be added to carts/orders
 */
class Product extends Model
{
    /**
     * The attributes that are mass assignable
     * These fields can be set when creating/updating a product
     */
    protected $fillable = [
        'category_id',  // Which category this product belongs to
        'name',         // Product name (e.g., "Organic Apples")
        'slug',         // URL-friendly name (e.g., "organic-apples")
        'description',  // Product description
        'price',        // Price in dollars (e.g., 9.99)
        'stock',        // Number of items available
        'image',        // Image file name or URL
        'is_active',    // Whether product is for sale (true/false)
    ];

    /**
     * Type casting - convert decimal fields to proper format
     */
    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // ========== RELATIONSHIPS ==========

    /**
     * Get the category this product belongs to
     * Many products belong to one category
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get cart items for this product
     * A product can appear in many cart items
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get order items for this product
     * A product can appear in many order items
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
