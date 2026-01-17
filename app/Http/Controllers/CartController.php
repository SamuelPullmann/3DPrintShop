<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\UpdateCartRequest;
use App\Http\Requests\RemoveFromCartRequest;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController
{
    /**
     * Display the shopping cart
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        $cartItems = [];
        $subtotal = 0;

        // Fetch product details for each cart item
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

        // Calculate totals
        $shipping = $subtotal > 0 ? 4.99 : 0;
        $total = $subtotal + $shipping;

        return view('cart', compact('cartItems', 'subtotal', 'shipping', 'total'));
    }

    /**
     * Add item to cart
     */
    public function add(AddToCartRequest $request)
    {
        $productId = $request->product_id;
        $quantity = $request->quantity ?? 1;

        // Get current cart from session
        $cart = session()->get('cart', []);

        // Add or update quantity
        if (isset($cart[$productId])) {
            $cart[$productId] += $quantity;
        } else {
            $cart[$productId] = $quantity;
        }

        // Save back to session
        session()->put('cart', $cart);

        // Return JSON for AJAX or redirect for regular form
        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Product added to cart!',
                'cart_count' => count($cart)
            ]);
        }

        return redirect()->back()->with('success', 'Product added to cart!');
    }

    /**
     * Update item quantity in cart
     */
    public function update(UpdateCartRequest $request)
    {
        $productId = $request->product_id;
        $quantity = $request->quantity;

        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            $cart[$productId] = $quantity;
            session()->put('cart', $cart);
        }

        // Calculate new totals
        $subtotal = 0;
        foreach ($cart as $pid => $qty) {
            $product = Product::find($pid);
            if ($product) {
                $subtotal += $product->price * $qty;
            }
        }
        $shipping = $subtotal > 0 ? 4.99 : 0;
        $total = $subtotal + $shipping;

        // Return JSON for AJAX or redirect for regular form
        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Cart updated!',
                'subtotal' => $subtotal,
                'shipping' => $shipping,
                'total' => $total
            ]);
        }

        return redirect()->route('cart.show')->with('success', 'Cart updated!');
    }

    /**
     * Remove item from cart
     */
    public function remove(RemoveFromCartRequest $request)
    {
        $productId = $request->product_id;
        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            session()->put('cart', $cart);
        }

        return redirect()->route('cart.show')->with('success', 'Item removed from cart!');
    }
}
