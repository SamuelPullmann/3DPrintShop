<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController
{
    /**
     * Show the checkout form
     */
    public function index()
    {
        $cart = session()->get('cart', []);

        // If cart is empty, redirect to cart page
        if (empty($cart)) {
            return redirect()->route('cart.show')->with('error', 'Your cart is empty!');
        }

        // Calculate totals
        $cartItems = [];
        $subtotal = 0;

        foreach ($cart as $productId => $quantity) {
            $product = Product::find($productId);
            if ($product) {
                $itemTotal = $product->price * $quantity;
                $cartItems[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'total' => $itemTotal
                ];
                $subtotal += $itemTotal;
            }
        }

        $shipping = $subtotal > 0 ? 4.99 : 0;
        $total = $subtotal + $shipping;

        return view('checkout', compact('cartItems', 'subtotal', 'shipping', 'total'));
    }

    /**
     * Process the checkout and create order
     */
    public function store(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'street_address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.show')->with('error', 'Your cart is empty!');
        }

        // Calculate total
        $subtotal = 0;
        foreach ($cart as $productId => $quantity) {
            $product = Product::find($productId);
            if ($product) {
                $subtotal += $product->price * $quantity;
            }
        }
        $shipping = 4.99;
        $total = $subtotal + $shipping;

        // Create order using transaction
        DB::beginTransaction();
        try {
            // Create the order
            $order = Order::create([
                'user_id' => Auth::id(),
                'customer_name' => $validated['first_name'] . ' ' . $validated['last_name'],
                'customer_address' => $validated['street_address'] . ', ' .
                                      $validated['city'] . ' ' .
                                      $validated['postal_code'],
                'total_amount' => $total,
                'status' => 'Processing'
            ]);

            // Create order items
            foreach ($cart as $productId => $quantity) {
                $product = Product::find($productId);
                if ($product) {
                    OrderItem::create([
                        'order_id' => $order->order_id,
                        'product_id' => $product->product_id,
                        'quantity' => $quantity,
                        'price' => $product->price
                    ]);
                }
            }

            // Clear the cart
            session()->forget('cart');

            DB::commit();

            // Redirect to success page
            return redirect()->route('checkout.success', ['order' => $order->order_id]);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    /**
     * Show order confirmation page
     */
    public function success($orderId)
    {
        $order = Order::with('orderItems.product')->findOrFail($orderId);
        return view('checkout-success', compact('order'));
    }
}
