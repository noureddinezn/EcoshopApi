<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function getCart(Request $request)
    {

        $me = $request->user();


        $myCart = Cart::firstOrCreate(['user_id' => $me->id]);


        $itemsInCart = CartItem::where('cart_id', $myCart->id)
            ->with('product')
            ->get();


        $total = 0;
        foreach ($itemsInCart as $item) {

            $total += $item->quantity * $item->unit_price;
        }


        return response()->json([
            'message' => 'Cart retrieved successfully',
            'data' => [
                'cart_id' => $myCart->id,
                'items' => $itemsInCart,
                'total' => $total,
                'items_count' => $itemsInCart->count(),
            ],
        ], 200);
    }

    public function addToCart(Request $request)
    {

        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $me = $request->user();


        $product = Product::findOrFail($data['product_id']);


        if ($product->stock < $data['quantity']) {
            return response()->json([
                'message' => 'Not enough stock! We only have ' . $product->stock,
            ], 400);
        }


        $myCart = Cart::firstOrCreate(['user_id' => $me->id]);


        $item = CartItem::updateOrCreate(
            ['cart_id' => $myCart->id, 'product_id' => $data['product_id']],
            [
                'quantity' => $data['quantity'],
                'unit_price' => $product->price,
            ]
        );


        return response()->json([
            'message' => 'Product added to cart!',
            'data' => $item->load('product'),
        ], 201);
    }


    public function updateCartItem(Request $request, $cartItemId)
    {

        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);


        $me = $request->user();


        $cartItem = CartItem::with('cart', 'product')->findOrFail($cartItemId);

        if ($cartItem->cart->user_id !== $me->id) {
            return response()->json([
                'message' => 'ERROR: This is not your cart item!',
            ], 403);
        }


        if ($cartItem->product->stock < $data['quantity']) {
            return response()->json([
                'message' => 'Not enough stock! We only have ' . $cartItem->product->stock,
            ], 400);
        }


        $cartItem->update(['quantity' => $data['quantity']]);

        return response()->json([
            'message' => 'Quantity updated!',
            'data' => $cartItem->load('product'),
        ], 200);
    }


    public function removeFromCart(Request $request, $cartItemId)
    {

        $me = $request->user();


        $cartItem = CartItem::with('cart')->findOrFail($cartItemId);


        if ($cartItem->cart->user_id !== $me->id) {
            return response()->json([
                'message' => 'ERROR: This is not your cart item!',
            ], 403);
        }


        $cartItem->delete();


        return response()->json([
            'message' => 'Item removed from cart!',
        ], 200);
    }

    // ╔═══════════════════════════════════════════════════════╗
    // ║ SECTION 2: ORDERS (Checkout, View Orders)             ║
    // ╚═══════════════════════════════════════════════════════╝

    /**
     * METHOD 5: CHECKOUT - Place an order
     * EASY EXPLANATION: "I'm done shopping! Buy everything in my cart!"
     *
     * URL: POST /api/orders
     * REQUIRES: You must be logged in
     * NO BODY NEEDED
     *
     * This does MANY things:
     *   1. Check cart has items
     *   2. Calculate total
     *   3. Create order
     *   4. Add items from cart to order
     *   5. Lower product stock
     *   6. Clear the cart
     */
    public function placeOrder(Request $request)
    {
        // 👤 WHO AM I?
        $me = $request->user();

        // 🛒 GET MY SHOPPING CART
        $myCart = Cart::where('user_id', $me->id)->firstOrFail();

        // 📦 GET ALL ITEMS IN MY CART
        $itemsInCart = CartItem::where('cart_id', $myCart->id)->get();

        // ⚠️ DO I HAVE ITEMS TO BUY?
        if ($itemsInCart->isEmpty()) {
            return response()->json([
                'message' => 'ERROR: Your cart is empty!',
            ], 400);
        }

        // 💰 CALCULATE TOTAL PRICE
        $totalPrice = 0;
        foreach ($itemsInCart as $item) {
            $totalPrice += $item->quantity * $item->unit_price;
        }

        // 💳 CREATE THE ORDER IN DATABASE
        $order = Order::create([
            'user_id' => $me->id,
            'total_amount' => $totalPrice,
            'status' => 'pending',  // Waiting to be processed
        ]);

        // 📦 FOR EACH ITEM IN CART, ADD IT TO THE ORDER
        foreach ($itemsInCart as $item) {
            // Create order item
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'subtotal' => $item->quantity * $item->unit_price,
            ]);

            // REDUCE THE STOCK (we sold some!)
            $product = $item->product;
            $product->stock = $product->stock - $item->quantity;
            $product->save();
        }

        // 🗑️ CLEAR THE CART (delete all items)
        CartItem::where('cart_id', $myCart->id)->delete();

        // ✅ SEND BACK SUCCESS
        return response()->json([
            'message' => 'Order placed! Your order is being processed.',
            'data' => $order->load('orderItems'),
        ], 201);
    }

    /**
     * METHOD 6: View all MY orders
     * EASY EXPLANATION: "Show me all orders I've ever placed"
     *
     * URL: GET /api/orders
     * REQUIRES: You must be logged in
     * Optional: ?per_page=5
     */
    public function getOrders(Request $request)
    {
        // 👤 WHO AM I?
        $me = $request->user();

        // 📋 GET ALL MY ORDERS
        // Include items in each order
        // Show newest first
        // Split into pages (10 per page)
        $myOrders = Order::where('user_id', $me->id)
            ->with('orderItems')
            ->latest()
            ->paginate($request->input('per_page', 10));

        // ✅ SEND BACK
        return response()->json([
            'message' => 'Orders retrieved successfully',
            'data' => $myOrders,
        ], 200);
    }

    /**
     * METHOD 7: View ONE specific order
     * EASY EXPLANATION: "Show me details of order #42"
     *
     * URL: GET /api/orders/42
     * REQUIRES: You must be logged in
     */
    public function getOrder(Request $request, $orderId)
    {
        // 👤 WHO AM I?
        $me = $request->user();

        // 📋 GET THAT ORDER (make sure it's mine)
        $order = Order::where('user_id', $me->id)
            ->with('orderItems')
            ->findOrFail($orderId);

        // ✅ SEND BACK
        return response()->json([
            'message' => 'Order retrieved successfully',
            'data' => $order,
        ], 200);
    }
}

    /**
     * METHOD 4: View my shopping cart
     * EASY EXPLANATION: "Show me what's in my cart and the total price"
     *
     * URL: GET /api/cart
     * REQUIRES: You must be logged in
     */
    public function getCart(Request $request)
    {
        // 👤 WHO AM I? Get the logged-in person
        $me = $request->user();

        // 🛒 GET MY CART (or create one if I don't have one)
        $myCart = Cart::firstOrCreate(['user_id' => $me->id]);

        // 📦 GET ALL ITEMS IN MY CART
        $itemsInCart = CartItem::where('cart_id', $myCart->id)
            ->with('product')  // Also get product info
            ->get();

        // 💰 CALCULATE TOTAL PRICE
        $total = 0;
        foreach ($itemsInCart as $item) {
            // For each item: quantity × price per item
            $total += $item->quantity * $item->unit_price;
        }

        // ✅ SEND BACK CART INFO
        return response()->json([
            'message' => 'Cart retrieved successfully',
            'data' => [
                'cart_id' => $myCart->id,
                'items' => $itemsInCart,
                'total' => $total,
                'items_count' => $itemsInCart->count(),
            ],
        ], 200);
    }



    public function addToCart(Request $request)
    {

        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);


        $me = $request->user();


        $product = Product::findOrFail($data['product_id']);


        if ($product->stock < $data['quantity']) {
            return response()->json([
                'message' => 'Not enough stock! We only have ' . $product->stock,
            ], 400);
        }


        $myCart = Cart::firstOrCreate(['user_id' => $me->id]);


        $item = CartItem::updateOrCreate(
            ['cart_id' => $myCart->id, 'product_id' => $data['product_id']],
            [
                'quantity' => $data['quantity'],
                'unit_price' => $product->price,
            ]
        );


        return response()->json([
            'message' => 'Product added to cart!',
            'data' => $item->load('product'),
        ], 201);
    }

    /**
     * METHOD 6: Change quantity of item in cart
     * EASY EXPLANATION: "I want 5 of this instead of 2"
     *
     * URL: PUT /api/cart-items/12
     * REQUIRES: You must be logged in
     * SEND: { "quantity": 5 }
     */
    public function updateCartItem(Request $request, $cartItemId)
    {
        // ✔️ CHECK THE NEW QUANTITY IS VALID
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        // 👤 WHO AM I?
        $me = $request->user();

        // 🔍 FIND THE CART ITEM
        $cartItem = CartItem::with('cart', 'product')->findOrFail($cartItemId);

        // 🔐 SECURITY: IS THIS MY ITEM?
        // Block someone changing another person's cart!
        if ($cartItem->cart->user_id !== $me->id) {
            return response()->json([
                'message' => 'ERROR: This is not your cart item!',
            ], 403);
        }

        // ⚠️ DO WE HAVE ENOUGH STOCK?
        if ($cartItem->product->stock < $data['quantity']) {
            return response()->json([
                'message' => 'Not enough stock! We only have ' . $cartItem->product->stock,
            ], 400);
        }

        // 📦 UPDATE THE QUANTITY
        $cartItem->update(['quantity' => $data['quantity']]);

        // ✅ SEND BACK SUCCESS
        return response()->json([
            'message' => 'Quantity updated!',
            'data' => $cartItem->load('product'),
        ], 200);
    }


    public function removeFromCart(Request $request, $cartItemId)
    {

        $me = $request->user();


        $cartItem = CartItem::with('cart')->findOrFail($cartItemId);


        if ($cartItem->cart->user_id !== $me->id) {
            return response()->json([
                'message' => 'ERROR: This is not your cart item!',
            ], 403);
        }


        $cartItem->delete();


        return response()->json([
            'message' => 'Item removed from cart!',
        ], 200);
    }


    public function placeOrder(Request $request)
    {

        $me = $request->user();


        $myCart = Cart::where('user_id', $me->id)->firstOrFail();


        $itemsInCart = CartItem::where('cart_id', $myCart->id)->get();


        if ($itemsInCart->isEmpty()) {
            return response()->json([
                'message' => 'ERROR: Your cart is empty!',
            ], 400);
        }


        $totalPrice = 0;
        foreach ($itemsInCart as $item) {
            $totalPrice += $item->quantity * $item->unit_price;
        }


        $order = Order::create([
            'user_id' => $me->id,
            'total_amount' => $totalPrice,
            'status' => 'pending',
        ]);


        foreach ($itemsInCart as $item) {

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'subtotal' => $item->quantity * $item->unit_price,
            ]);


            $product = $item->product;
            $product->stock = $product->stock - $item->quantity;
            $product->save();
        }


        CartItem::where('cart_id', $myCart->id)->delete();

        return response()->json([
            'message' => 'Order placed! Your order is being processed.',
            'data' => $order->load('orderItems'),
        ], 201);
    }


    public function getOrders(Request $request)
    {
        // 👤 WHO AM I?
        $me = $request->user();


        $myOrders = Order::where('user_id', $me->id)
            ->with('orderItems')
            ->latest()
            ->paginate($request->input('per_page', 10));


        return response()->json([
            'message' => 'Orders retrieved successfully',
            'data' => $myOrders,
        ], 200);
    }


    public function getOrder(Request $request, $orderId)
    {

        $me = $request->user();


        $order = Order::where('user_id', $me->id)
            ->with('orderItems')
            ->findOrFail($orderId);

      
        return response()->json([
            'message' => 'Order retrieved successfully',
            'data' => $order,
        ], 200);
    }

