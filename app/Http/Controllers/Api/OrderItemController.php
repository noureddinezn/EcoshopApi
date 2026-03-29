<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

/**
 * ============================================================
 * OrderItemController - Order Items Management
 * ============================================================
 *
 * Think of this as an ORDER LINE ITEM MANAGER!
 * It helps:
 *   📦 View items in an order
 *   📝 Update order item details
 *   🗑️ Remove items from an order (if needed)
 *
 * ============================================================
 */
class OrderItemController extends Controller
{
    /**
     * METHOD 1: Get all items in an order
     * EASY EXPLANATION: "Show me all products in order #42"
     *
     * URL: GET /api/orders/42/items
     * REQUIRES: You must be logged in (and it must be your order)
     */
    public function index(Request $request, $orderId)
    {
        // 👤 WHO AM I?
        $user = $request->user();

        // 📋 FIND THE ORDER (make sure it's MINE!)
        $order = Order::where('user_id', $user->id)
            ->findOrFail($orderId);

        // 📦 GET ALL ITEMS IN THIS ORDER
        $orderItems = OrderItem::where('order_id', $order->id)
            ->with('product')  // Get product info too
            ->get();

        // ✅ SEND BACK
        return response()->json([
            'message' => 'Order items retrieved successfully',
            'data' => [
                'order_id' => $order->id,
                'items' => $orderItems,
                'items_count' => $orderItems->count(),
            ],
        ], 200);
    }

    /**
     * METHOD 2: Get ONE specific item from an order
     * EASY EXPLANATION: "Show me details of item #5 from order #42"
     *
     * URL: GET /api/orders/42/items/5
     * REQUIRES: You must be logged in (and it must be your order)
     */
    public function show(Request $request, $orderId, $itemId)
    {
        // 👤 WHO AM I?
        $user = $request->user();

        // 📋 FIND THE ORDER (make sure it's MINE!)
        $order = Order::where('user_id', $user->id)
            ->findOrFail($orderId);

        // 📦 FIND THE ITEM IN THIS ORDER
        $orderItem = OrderItem::where('order_id', $order->id)
            ->with('product')
            ->findOrFail($itemId);

        // ✅ SEND BACK
        return response()->json([
            'message' => 'Order item retrieved successfully',
            'data' => $orderItem,
        ], 200);
    }

    /**
     * METHOD 3: Update order item (Admin only)
     * EASY EXPLANATION: "Change the quantity or price of an item in an order"
     *
     * URL: PUT /api/admin/order-items/5
     * REQUIRES: You must be logged in AND be an admin
     * SEND: { "quantity": 5, "unit_price": 29.99 }
     */
    public function update(Request $request, $itemId)
    {
        // 👤 WHO AM I?
        $user = $request->user();

        // 🔐 CHECK: Are you an admin?
        if ($user->role !== 'admin') {
            return response()->json([
                'message' => 'ERROR: Only admins can update order items',
            ], 403);  // 403 = Forbidden
        }

        // 📦 FIND THE ORDER ITEM
        $orderItem = OrderItem::findOrFail($itemId);

        // ✔️ VALIDATE THE DATA
        $data = $request->validate([
            'quantity' => ['sometimes', 'required', 'integer', 'min:1'],
            'unit_price' => ['sometimes', 'required', 'numeric', 'min:0'],
        ]);

        // 📝 UPDATE THE ITEM
        if (isset($data['quantity']) || isset($data['unit_price'])) {
            $quantity = $data['quantity'] ?? $orderItem->quantity;
            $unitPrice = $data['unit_price'] ?? $orderItem->unit_price;

            // RECALCULATE SUBTOTAL
            $subtotal = $quantity * $unitPrice;

            $orderItem->update([
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'subtotal' => $subtotal,
            ]);
        }

        // ✅ SEND BACK SUCCESS
        return response()->json([
            'message' => 'Order item updated successfully',
            'data' => $orderItem,
        ], 200);
    }

    /**
     * METHOD 4: Delete an item from an order (Admin only)
     * EASY EXPLANATION: "Remove an item from an order"
     *
     * URL: DELETE /api/admin/order-items/5
     * REQUIRES: You must be logged in AND be an admin
     * NOTE: This is usually not recommended for completed orders!
     */
    public function destroy(Request $request, $itemId)
    {
        // 👤 WHO AM I?
        $user = $request->user();

        // 🔐 CHECK: Are you an admin?
        if ($user->role !== 'admin') {
            return response()->json([
                'message' => 'ERROR: Only admins can delete order items',
            ], 403);
        }

        // 📦 FIND THE ORDER ITEM
        $orderItem = OrderItem::findOrFail($itemId);

        // 📋 GET THE PARENT ORDER
        $order = $orderItem->order;

        // ⚠️ WARNING: Check if order is already completed/shipped
        if (in_array($order->status, ['completed', 'shipped'])) {
            return response()->json([
                'message' => 'ERROR: Cannot delete items from completed orders',
            ], 400);
        }

        // 🗑️ DELETE THE ITEM
        $itemId = $orderItem->id;
        $orderItem->delete();

        // ✅ SEND BACK SUCCESS
        return response()->json([
            'message' => 'Order item deleted successfully',
        ], 200);
    }

    /**
     * METHOD 5: Get order item statistics (Admin only)
     * EASY EXPLANATION: "Show me stats about items across all orders"
     *
     * URL: GET /api/admin/order-items/stats
     * REQUIRES: You must be logged in AND be an admin
     */
    public function getStats(Request $request)
    {
        // 👤 WHO AM I?
        $user = $request->user();

        // 🔐 CHECK: Are you an admin?
        if ($user->role !== 'admin') {
            return response()->json([
                'message' => 'ERROR: Only admins can view statistics',
            ], 403);
        }

        // 📊 CALCULATE STATISTICS
        $totalItems = OrderItem::count();                                  // How many items sold total?
        $totalRevenue = OrderItem::sum('subtotal');                        // How much money from items?
        $averageItemPrice = OrderItem::avg('unit_price');                  // Average item price
        $mostSoldProduct = OrderItem::groupBy('product_id')
            ->selectRaw('product_id, SUM(quantity) as total_quantity')
            ->orderByDesc('total_quantity')
            ->with('product')
            ->first();

        // ✅ SEND BACK STATS
        return response()->json([
            'message' => 'Order item statistics retrieved successfully',
            'data' => [
                'total_items_sold' => $totalItems,
                'total_revenue' => $totalRevenue,
                'average_item_price' => round($averageItemPrice, 2),
                'most_sold_product' => $mostSoldProduct ? [
                    'product' => $mostSoldProduct->product,
                    'quantity_sold' => $mostSoldProduct->total_quantity,
                ] : null,
            ],
        ], 200);
    }
}
