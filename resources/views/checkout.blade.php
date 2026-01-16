@extends('layouts.app')

@section('title', 'Checkout')

@push('styles')
    @vite(['resources/css/checkout.css'])
@endpush

@section('content')
    <div class="checkout-container">
        <div class="checkout-main">
            <h1 class="checkout-heading">Checkout</h1>

            @if(session('error'))
                <div class="alert-error">{{ session('error') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert-error">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('checkout.store') }}" class="checkout-form">
                @csrf

                <div class="shipping-info">
                    <div class="shipping-header">
                        <h2>Shipping Information</h2>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="first_name">First Name</label>
                            <input type="text"
                                   id="first_name"
                                   name="first_name"
                                   value="{{ old('first_name', Auth::check() ? explode(' ', Auth::user()->name)[0] : '') }}"
                                   required>
                        </div>
                        <div class="form-group">
                            <label for="last_name">Last Name</label>
                            <input type="text"
                                   id="last_name"
                                   name="last_name"
                                   value="{{ old('last_name', Auth::check() && str_word_count(Auth::user()->name) > 1 ? substr(Auth::user()->name, strpos(Auth::user()->name, ' ') + 1) : '') }}"
                                   required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email"
                                   id="email"
                                   name="email"
                                   value="{{ old('email', Auth::user()->email ?? '') }}"
                                   required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="tel"
                                   id="phone"
                                   name="phone"
                                   value="{{ old('phone', Auth::user()->phone ?? '') }}"
                                   required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="street_address">Street Address</label>
                        <input type="text"
                               id="street_address"
                               name="street_address"
                               value="{{ old('street_address', Auth::user()->street_address ?? '') }}"
                               required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="city">City</label>
                            <input type="text"
                                   id="city"
                                   name="city"
                                   value="{{ old('city', Auth::user()->city ?? '') }}"
                                   required>
                        </div>
                        <div class="form-group">
                            <label for="postal_code">Postal Code</label>
                            <input type="text"
                                   id="postal_code"
                                   name="postal_code"
                                   value="{{ old('postal_code', Auth::user()->postal_code ?? '') }}"
                                   required>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-submit-order">Submit Order</button>
            </form>
        </div>

        <aside class="checkout-sidebar">
            <div class="order-summary">
                <h2 class="order-summary-title">Order Summary</h2>

                <div class="order-items">
                    @foreach($cartItems as $item)
                        <div class="order-item">
                            <div class="order-item-image">
                                @if($item['product']->file_path)
                                    <img src="{{ route('product.image', $item['product']->product_id) }}"
                                         alt="{{ $item['product']->name }}">
                                @else
                                    <div class="order-item-placeholder"></div>
                                @endif
                            </div>
                            <div class="order-item-details">
                                <p class="order-item-name">{{ $item['product']->name }}</p>
                                <p class="order-item-quantity">Qty: {{ $item['quantity'] }}</p>
                            </div>
                            <p class="order-item-price">€{{ number_format($item['total'], 2) }}</p>
                        </div>
                    @endforeach
                </div>

                <div class="order-summary-divider"></div>

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
            </div>
        </aside>
    </div>
@endsection

