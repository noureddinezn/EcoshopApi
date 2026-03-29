<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

/**
 * ============================================================
 * ProductController - Product Management
 * ============================================================
 *
 * Think of this as a PRODUCT MANAGER!
 * It helps:
 *   📦 Browse all products (everyone)
 *   🔧 Manage products (admins only: create, edit, delete)
 *
 * ============================================================
 */
class ProductController extends Controller
{
    /**
     * METHOD 1: Show all products
     * EASY EXPLANATION: "Show me all products in the store"
     *
     * URL: GET /api/products
     * NO LOGIN NEEDED (public route)
     * Optional: ?category_id=2&per_page=15
     */
    public function index(Request $request)
    {
        // 🔍 START WITH ALL PRODUCTS
        $productsQuery = Product::query();

        // 🔍 IF USER FILTERED BY CATEGORY, APPLY THAT FILTER
        if ($request->has('category_id')) {
            $productsQuery->where('category_id', $request->category_id);
        }

        // 📄 SPLIT INTO PAGES (show 15 per page by default)
        $allProducts = $productsQuery->paginate($request->input('per_page', 15));

        // ✅ SEND BACK THE LIST
        return response()->json([
            'message' => 'Products retrieved successfully',
            'data' => $allProducts,
        ], 200);
    }

    /**
     * METHOD 2: Show ONE specific product
     * EASY EXPLANATION: "Show me details of product #5"
     *
     * URL: GET /api/products/5
     * NO LOGIN NEEDED (public route)
     */
    public function show($productId)
    {
        // 🔍 FIND THE PRODUCT BY ID
        $product = Product::findOrFail($productId);

        // ✅ SEND BACK THE PRODUCT
        return response()->json([
            'message' => 'Product retrieved successfully',
            'data' => $product,
        ], 200);
    }

    /**
     * METHOD 3: Create a new product (ADMIN ONLY)
     * EASY EXPLANATION: "Add a new product to the store"
     *
     * URL: POST /api/admin/products
     * REQUIRES: You must be logged in AND be an admin
     * SEND: {
     *   "name": "Laptop",
     *   "description": "High-performance laptop",
     *   "category_id": 1,
     *   "price": 999.99,
     *   "stock": 50,
     *   "image": "laptop.jpg",
     *   "is_active": true
     * }
     */
    public function store(Request $request)
    {
        // 👤 GET LOGGED-IN USER
        $user = $request->user();

        // 🔐 CHECK: Are you an admin?
        if ($user->role !== 'admin') {
            return response()->json([
                'message' => 'ERROR: Only admins can create products',
            ], 403);  // 403 = Forbidden
        }

        // ✔️ VALIDATE THE DATA
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],              // Product name
            'description' => ['nullable', 'string'],                  // Optional description
            'category_id' => ['required', 'exists:categories,id'],    // Must be valid category
            'price' => ['required', 'numeric', 'min:0'],              // Price (can't be negative)
            'stock' => ['required', 'integer', 'min:0'],              // Stock quantity (can't be negative)
            'image' => ['nullable', 'string'],                        // Optional image URL
            'is_active' => ['boolean'],                               // Active status
        ]);

        // 📦 CREATE NEW PRODUCT
        $newProduct = Product::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'category_id' => $data['category_id'],
            'price' => $data['price'],
            'stock' => $data['stock'],
            'image' => $data['image'] ?? null,
            'is_active' => $data['is_active'] ?? true,  // Default to active
        ]);

        // ✅ SEND BACK SUCCESS
        return response()->json([
            'message' => 'Product created successfully',
            'data' => $newProduct,
        ], 201);
    }

    /**
     * METHOD 4: Update a product (ADMIN ONLY)
     * EASY EXPLANATION: "Change product details (name, price, stock, etc)"
     *
     * URL: PUT /api/admin/products/5
     * REQUIRES: You must be logged in AND be an admin
     * SEND: {
     *   "name": "Updated Name",
     *   "price": 1299.99,
     *   "stock": 30,
     *   ...
     * }
     */
    public function update(Request $request, $productId)
    {
        // 👤 GET LOGGED-IN USER
        $user = $request->user();

        // 🔐 CHECK: Are you an admin?
        if ($user->role !== 'admin') {
            return response()->json([
                'message' => 'ERROR: Only admins can update products',
            ], 403);
        }

        // 🔍 FIND THE PRODUCT
        $product = Product::findOrFail($productId);

        // ✔️ VALIDATE THE DATA
        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category_id' => ['sometimes', 'required', 'exists:categories,id'],
            'price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'stock' => ['sometimes', 'required', 'integer', 'min:0'],
            'image' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        // 📝 UPDATE THE PRODUCT
        $product->update($data);

        // ✅ SEND BACK SUCCESS
        return response()->json([
            'message' => 'Product updated successfully',
            'data' => $product,
        ], 200);
    }

    /**
     * METHOD 5: Delete a product (ADMIN ONLY)
     * EASY EXPLANATION: "Remove a product from the store"
     *
     * URL: DELETE /api/admin/products/5
     * REQUIRES: You must be logged in AND be an admin
     */
    public function destroy(Request $request, $productId)
    {
        // 👤 GET LOGGED-IN USER
        $user = $request->user();

        // 🔐 CHECK: Are you an admin?
        if ($user->role !== 'admin') {
            return response()->json([
                'message' => 'ERROR: Only admins can delete products',
            ], 403);
        }

        // 🔍 FIND THE PRODUCT
        $product = Product::findOrFail($productId);

        // 🗑️ DELETE THE PRODUCT
        $product->delete();

        // ✅ SEND BACK SUCCESS
        return response()->json([
            'message' => 'Product deleted successfully',
        ], 200);
    }
}
