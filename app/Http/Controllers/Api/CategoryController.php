<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

/**
 * ============================================================
 * CategoryController - Product Category Management
 * ============================================================
 *
 * Think of this as a STORE ORGANIZER!
 * It helps:
 *   📦 Browse product categories (everyone)
 *   🔧 Manage categories (admins only: create, edit, delete)
 *
 * ============================================================
 */
class CategoryController extends Controller
{
    /**
     * METHOD 1: Show all product categories
     * EASY EXPLANATION: "Show me all product types in the store"
     *
     * URL: GET /api/categories
     * NO LOGIN NEEDED (public route)
     */
    public function index()
    {
        // 🔍 GET ALL CATEGORIES
        $allCategories = Category::all();

        // ✅ SEND BACK THE LIST
        return response()->json([
            'message' => 'Categories retrieved successfully',
            'data' => $allCategories,
        ], 200);
    }

    /**
     * METHOD 2: Show ONE category with its products
     * EASY EXPLANATION: "Show me all products in the 'Electronics' category"
     *
     * URL: GET /api/categories/3
     * NO LOGIN NEEDED (public route)
     */
    public function show($categoryId)
    {
        // 🔍 FIND THE CATEGORY
        $category = Category::findOrFail($categoryId);

        // 📦 GET ALL PRODUCTS IN THIS CATEGORY
        $products = $category->products;

        // ✅ SEND BACK
        return response()->json([
            'message' => 'Category retrieved successfully',
            'data' => [
                'category' => $category,
                'products' => $products,
            ],
        ], 200);
    }

    /**
     * METHOD 3: Create a new category (ADMIN ONLY)
     * EASY EXPLANATION: "Add a new product category to the store"
     *
     * URL: POST /api/categories
     * REQUIRES: You must be logged in AND be an admin
     * SEND: { "name": "Electronics", "slug": "electronics" }
     */
    public function store(Request $request)
    {
        // 👤 GET LOGGED-IN USER
        $user = $request->user();

        // 🔐 CHECK: Are you an admin?
        if ($user->role !== 'admin') {
            return response()->json([
                'message' => 'ERROR: Only admins can create categories',
            ], 403);  // 403 = Forbidden
        }

        // ✔️ VALIDATE THE DATA
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],           // Category name
            'slug' => ['required', 'string', 'unique:categories'],  // Unique URL-friendly name
        ]);

        // 📦 CREATE NEW CATEGORY
        $newCategory = Category::create([
            'name' => $data['name'],
            'slug' => $data['slug'],
        ]);

        // ✅ SEND BACK SUCCESS
        return response()->json([
            'message' => 'Category created successfully',
            'data' => $newCategory,
        ], 201);
    }

    /**
     * METHOD 4: Update a category (ADMIN ONLY)
     * EASY EXPLANATION: "Change the category name or URL slug"
     *
     * URL: PUT /api/categories/3
     * REQUIRES: You must be logged in AND be an admin
     * SEND: { "name": "New Name", "slug": "new-slug" }
     */
    public function update(Request $request, $categoryId)
    {
        // 👤 GET LOGGED-IN USER
        $user = $request->user();

        // 🔐 CHECK: Are you an admin?
        if ($user->role !== 'admin') {
            return response()->json([
                'message' => 'ERROR: Only admins can update categories',
            ], 403);
        }

        // 🔍 FIND THE CATEGORY
        $category = Category::findOrFail($categoryId);

        // ✔️ VALIDATE THE DATA
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'unique:categories,slug,' . $categoryId],  // Allow same slug for this category
        ]);

        // 📝 UPDATE THE CATEGORY
        $category->update([
            'name' => $data['name'],
            'slug' => $data['slug'],
        ]);

        // ✅ SEND BACK SUCCESS
        return response()->json([
            'message' => 'Category updated successfully',
            'data' => $category,
        ], 200);
    }

    /**
     * METHOD 5: Delete a category (ADMIN ONLY)
     * EASY EXPLANATION: "Remove a category from the store"
     *
     * URL: DELETE /api/categories/3
     * REQUIRES: You must be logged in AND be an admin
     * NOTE: You can't delete a category that has products in it!
     */
    public function destroy(Request $request, $categoryId)
    {
        // 👤 GET LOGGED-IN USER
        $user = $request->user();

        // 🔐 CHECK: Are you an admin?
        if ($user->role !== 'admin') {
            return response()->json([
                'message' => 'ERROR: Only admins can delete categories',
            ], 403);
        }

        // 🔍 FIND THE CATEGORY
        $category = Category::findOrFail($categoryId);

        // ⚠️ CHECK: Does this category have products?
        $productCount = $category->products()->count();
        if ($productCount > 0) {
            return response()->json([
                'message' => "ERROR: Cannot delete! This category has $productCount products. Remove them first.",
                'data' => [
                    'product_count' => $productCount,
                ],
            ], 400);  // 400 = Bad Request
        }

        // 🗑️ DELETE THE CATEGORY
        $category->delete();

        // ✅ SEND BACK SUCCESS
        return response()->json([
            'message' => 'Category deleted successfully',
        ], 200);
    }
}
