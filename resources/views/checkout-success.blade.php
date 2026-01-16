@extends('layouts.app')

@section('title', 'Order Confirmed')

@push('styles')
    @vite(['resources/css/checkout-success.css'])
@endpush

@section('content')
    <div class="success-container">
        <div class="success-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>

        <h1 class="success-heading">Order Confirmed!</h1>
        <p class="success-message">
            Thank you for your order. We've received your order and will process it shortly.
        </p>

        <div class="order-details">
            <h3>Order Details</h3>

            <div class="order-info">
                <div class="order-info-row">
                    <span class="order-info-label">Order Number:</span>
                    <span class="order-info-value">#{{ $order->order_id }}</span>
                </div>
                <div class="order-info-row">
                    <span class="order-info-label">Customer Name:</span>
                    <span class="order-info-value">{{ $order->customer_name }}</span>
                </div>
                <div class="order-info-row">
                    <span class="order-info-label">Shipping Address:</span>
                    <span class="order-info-value">{{ $order->customer_address }}</span>
                </div>
                <div class="order-info-row">
                    <span class="order-info-label">Order Status:</span>
                    <span class="order-info-value">{{ $order->status }}</span>
                </div>
                <div class="order-info-row">
                    <span class="order-info-label">Total Amount:</span>
                    <span class="order-info-value">€{{ number_format($order->total_amount, 2) }}</span>
                </div>
            </div>

            <div class="order-items">
                <h3>Items Ordered</h3>
                @foreach($order->orderItems as $item)
                    <div class="order-item">
                        <div class="order-item-image">
                            @if($item->product->file_path)
                                <img src="{{ route('product.image', $item->product->product_id) }}"
                                     alt="{{ $item->product->name }}">
                            @endif
                        </div>
                        <div class="order-item-details">
                            <div class="order-item-name">{{ $item->product->name }}</div>
                            <div class="order-item-quantity">Quantity: {{ $item->quantity }}</div>
                        </div>
                        <div class="order-item-price">
                            €{{ number_format($item->price * $item->quantity, 2) }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="success-actions">
            <a href="{{ route('home') }}" class="btn-primary">Continue Shopping</a>
            @auth
                <a href="{{ route('profile.show') }}" class="btn-secondary">View Profile</a>
            @endauth
        </div>
    </div>
@endsection

