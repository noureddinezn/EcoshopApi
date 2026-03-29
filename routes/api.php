<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\OrderItemController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

// ========== PUBLIC ROUTES (Anyone can access) ==========

// Authentication routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Product browsing routes (public)
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);

// Category browsing routes (public)
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);

// ========== PROTECTED ROUTES (Login required) ==========

Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'show']);

    // Cart routes
    Route::get('/cart', [UserController::class, 'getCart']);
    Route::post('/cart', [UserController::class, 'addToCart']);
    Route::put('/cart-items/{cartItemId}', [UserController::class, 'updateCartItem']);
    Route::delete('/cart-items/{cartItemId}', [UserController::class, 'removeFromCart']);

    // Order routes (User - own orders)
    Route::post('/orders', [UserController::class, 'placeOrder']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{orderId}', [OrderController::class, 'show']);

    // Order items routes (User - view own order items)
    Route::get('/orders/{orderId}/items', [OrderItemController::class, 'index']);
    Route::get('/orders/{orderId}/items/{itemId}', [OrderItemController::class, 'show']);

    // ========== ADMIN ONLY ROUTES (Login + Admin role required) ==========

    // Product management
    Route::post('/admin/products', [ProductController::class, 'store']);
    Route::put('/admin/products/{id}', [ProductController::class, 'update']);
    Route::delete('/admin/products/{id}', [ProductController::class, 'destroy']);

    // Category management
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

    // Order management (Admin)
    Route::get('/admin/orders', [OrderController::class, 'allOrders']);
    Route::put('/admin/orders/{orderId}/status', [OrderController::class, 'updateStatus']);
    Route::get('/admin/orders/stats', [OrderController::class, 'getOrderStats']);
});




