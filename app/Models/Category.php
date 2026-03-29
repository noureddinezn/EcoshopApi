<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Category Model
 *
 * Represents product categories
 * Each category can have many products
 */
class Category extends Model
{
    /**
     * The attributes that are mass assignable
     * These fields can be set when creating/updating a category
     */
    protected $fillable = [
        'name',       // Category name (e.g., "Organic Food")
        'slug',       // URL-friendly name (e.g., "organic-food")
    ];

    // ========== RELATIONSHIPS ==========

    /**
     * Get all products in this category
     * One category has many products
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
