@extends('layouts.app')

@section('title', 'Shopping Cart')

@push('styles')
    @vite(['resources/css/cart.css'])
@endpush

@section('content')
    <div class="cart-container">
        <div class="cart-main">
            <h1 class="cart-heading">Shopping Cart ({{ count($cartItems) }})</h1>

            @if(session('success'))
                <div class="alert-success">{{ session('success') }}</div>
            @endif

            @if(count($cartItems) > 0)
                <div class="cart-items">
                    @foreach($cartItems as $item)
                        <div class="cart-item">
                            <div class="cart-item-image">
                                @if($item['product']->file_path)
                                    <img src="{{ route('product.image', $item['product']->product_id) }}"
                                         alt="{{ $item['product']->name }}">
                                @else
                                    <div class="cart-item-placeholder"></div>
                                @endif
                            </div>

                            <div class="cart-item-details">
                                <h3 class="cart-item-name">{{ $item['product']->name }}</h3>
                                @if($item['product']->product_type)
                                    <span class="cart-item-badge">{{ $item['product']->product_type }}</span>
                                @endif
                                <p class="cart-item-unit-price">€{{ number_format($item['product']->price, 2) }} / pc</p>
                            </div>

                            <div class="cart-item-price">
                                <p class="cart-item-total">€{{ number_format($item['total'], 2) }}</p>
                            </div>

                            <div class="cart-item-quantity">
                                <form method="POST" action="{{ route('cart.update') }}" class="quantity-form">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $item['product']->product_id }}">
                                    <button type="button" class="qty-btn qty-decrease" data-product-id="{{ $item['product']->product_id }}">−</button>
                                    <input type="number"
                                           name="quantity"
                                           value="{{ $item['quantity'] }}"
                                           min="1"
                                           max="99"
                                           class="qty-input"
                                           data-product-id="{{ $item['product']->product_id }}"
                                           readonly>
                                    <button type="button" class="qty-btn qty-increase" data-product-id="{{ $item['product']->product_id }}">+</button>
                                </form>
                            </div>

                            <div class="cart-item-remove">
                                <form method="POST" action="{{ route('cart.remove') }}">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $item['product']->product_id }}">
                                    <button type="submit" class="remove-btn">
                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                            <path d="M2 4h12M5.333 4V2.667a1.333 1.333 0 0 1 1.334-1.334h2.666a1.333 1.333 0 0 1 1.334 1.334V4m2 0v9.333a1.333 1.333 0 0 1-1.334 1.334H4.667a1.333 1.333 0 0 1-1.334-1.334V4h9.334Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        Remove
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="cart-empty">
                    <p>Your cart is empty.</p>
                    <a href="{{ route('home') }}" class="btn-continue-shopping">Continue Shopping</a>
                </div>
            @endif
        </div>

        @if(count($cartItems) > 0)
            <aside class="cart-sidebar">
                <div class="order-summary">
                    <h2 class="order-summary-title">Order Summary</h2>

                    <div class="order-summary-row">
                        <span>Subtotal</span>
                        <span>€{{ number_format($subtotal, 2) }}</span>
                    </div>

                    <div class="order-summary-row">
                        <span>Shipping</span>
                        <span>€{{ number_format($shipping, 2) }}</span>
                    </div>

                    <div class="order-summary-divider"></div>

                    <div class="order-summary-row order-summary-total">
                        <span>Total</span>
                        <span>€{{ number_format($total, 2) }}</span>
                    </div>

                    <a href="{{ route('checkout.index') }}" class="btn-checkout">Proceed to Checkout</a>
                </div>
            </aside>
        @endif
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/cart.js'])
@endpush
