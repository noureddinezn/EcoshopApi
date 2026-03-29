<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

/**
 * ============================================================
 * OrderController - Order Management
 * ============================================================
 *
 * Think of this as an ORDER MANAGER!
 * It helps:
 *   👥 Customers view their own orders
 *   👨‍💼 Admins view all orders and manage them
 *   📊 Admins see sales statistics
 *
 * ============================================================
 */
class OrderController extends Controller
{
    /**
     * METHOD 1: View my orders (customer)
     * EASY EXPLANATION: "Show me all orders I've placed"
     *
     * URL: GET /api/orders
     * REQUIRES: You must be logged in
     * Optional: ?per_page=5
     */
    public function index(Request $request)
    {
        // 👤 WHO AM I?
        $user = $request->user();

        // 📋 GET ALL MY ORDERS
        // Include items in each order
        // Show newest first
        // Split into pages (10 per page by default)
        $orders = Order::where('user_id', $user->id)
            ->with('orderItems')
            ->latest()
            ->paginate($request->input('per_page', 10));

        // ✅ SEND BACK
        return response()->json([
            'message' => 'Orders retrieved successfully',
            'data' => $orders,
        ], 200);
    }

    /**
     * METHOD 2: View ONE of my orders (customer)
     * EASY EXPLANATION: "Show me the details of order #42"
     *
     * URL: GET /api/orders/42
     * REQUIRES: You must be logged in
     */
    public function show(Request $request, $orderId)
    {
        // 👤 WHO AM I?
        $user = $request->user();

        // 📋 GET THAT ORDER (but only if it's MINE!)
        // If order doesn't exist or isn't mine, return 404
        $order = Order::where('user_id', $user->id)
            ->with('orderItems')
            ->findOrFail($orderId);

        // ✅ SEND BACK
        return response()->json([
            'message' => 'Order retrieved successfully',
            'data' => $order,
        ], 200);
    }

    /**
     * METHOD 3: Update order status (ADMIN ONLY)
     * EASY EXPLANATION: "Change the order status (pending → processing → completed)"
     *
     * URL: PUT /api/admin/orders/42/status
     * REQUIRES: You must be logged in AND be an admin
     * SEND: { "status": "processing" }
     * Allowed status: pending, processing, completed, cancelled
     */
    public function updateStatus(Request $request, $orderId)
    {
        // 👤 WHO AM I?
        $user = $request->user();

        // 🔐 CHECK: Are you an admin?
        if ($user->role !== 'admin') {
            return response()->json([
                'message' => 'ERROR: Only admins can update order status',
            ], 403);  // 403 = Forbidden
        }

        // 📋 FIND THE ORDER
        $order = Order::findOrFail($orderId);

        // ✔️ VALIDATE STATUS
        $data = $request->validate([
            'status' => ['required', 'string', 'in:pending,processing,completed,cancelled'],
        ]);

        // 📝 UPDATE THE STATUS
        $order->update(['status' => $data['status']]);

        // ✅ SEND BACK SUCCESS
        return response()->json([
            'message' => 'Order status updated successfully',
            'data' => $order,
        ], 200);
    }

    /**
     * METHOD 4: View ALL orders (ADMIN ONLY)
     * EASY EXPLANATION: "Show me all orders from all customers"
     *
     * URL: GET /api/admin/orders
     * REQUIRES: You must be logged in AND be an admin
     * Optional: ?per_page=5
     */
    public function allOrders(Request $request)
    {
        // 👤 WHO AM I?
        $user = $request->user();

        // 🔐 CHECK: Are you an admin?
        if ($user->role !== 'admin') {
            return response()->json([
                'message' => 'ERROR: Only admins can view all orders',
            ], 403);
        }

        // 📋 GET ALL ORDERS (from all customers)
        // Show newest first
        // Split into pages (10 per page by default)
        $orders = Order::with('user', 'orderItems')
            ->latest()
            ->paginate($request->input('per_page', 10));

        // ✅ SEND BACK
        return response()->json([
            'message' => 'All orders retrieved successfully',
            'data' => $orders,
        ], 200);
    }

    /**
     * METHOD 5: Get order statistics (ADMIN ONLY)
     * EASY EXPLANATION: "Show me sales numbers: total orders, total revenue, pending orders..."
     *
     * URL: GET /api/admin/orders/stats
     * REQUIRES: You must be logged in AND be an admin
     */
    public function getOrderStats(Request $request)
    {
        // 👤 WHO AM I?
        $user = $request->user();

        // 🔐 CHECK: Are you an admin?
        if ($user->role !== 'admin') {
            return response()->json([
                'message' => 'ERROR: Only admins can view statistics',
            ], 403);
        }

        // 📊 CALCULATE ALL STATISTICS
        $totalOrders = Order::count();                                    // How many orders total?
        $totalRevenue = Order::sum('total_amount');                       // How much money in total?
        $pendingOrders = Order::where('status', 'pending')->count();      // How many waiting?
        $completedOrders = Order::where('status', 'completed')->count();  // How many finished?

        // ✅ SEND BACK STATS
        return response()->json([
            'message' => 'Order statistics retrieved successfully',
            'data' => [
                'total_orders' => $totalOrders,
                'total_revenue' => $totalRevenue,
                'pending_orders' => $pendingOrders,
                'completed_orders' => $completedOrders,
            ],
        ], 200);
    }
}
