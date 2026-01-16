@extends('layouts.app')

@section('title', $product->name)

@push('styles')
    @vite(['resources/css/product-details.css'])
@endpush

@section('content')
    <div class="product-details-container">
        <div class="product-details-card">
            <div class="product-details-image-section">
                @if($product->file_path)
                    <img src="{{ route('product.image', $product->product_id) }}"
                         alt="{{ $product->name }}"
                         class="product-details-img">
                @else
                    <div class="product-details-img-placeholder"></div>
                @endif
            </div>

            <div class="product-details-info-section">
                @if($product->product_type)
                    <span class="product-badge">{{ $product->product_type }}</span>
                @endif

                <h1 class="product-details-title">{{ $product->name }}</h1>

                @if($reviewsCount > 0)
                    <div class="product-rating">
                        <div class="stars">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= round($averageRating))
                                    ⭐
                                @else
                                    ☆
                                @endif
                            @endfor
                        </div>
                        <span class="reviews-count">{{ $reviewsCount }} {{ $reviewsCount == 1 ? 'review' : 'reviews' }}</span>
                    </div>
                @endif

                <div class="product-details-price">€{{ number_format($product->price, 2) }}</div>

                <button class="product-details-add-to-cart" data-product-id="{{ $product->product_id }}">
                    <img src="{{ asset('images/cart.png') }}" alt="Cart" class="cart-icon">
                    Add to Cart
                </button>

                @if($product->description)
                    <div class="product-description">
                        <p>{{ $product->description }}</p>
                    </div>
                @endif

                @if($product->category)
                    <div class="product-categories">
                        <h3>Category</h3>
                        <div class="category-tags">
                            @php
                                $categoryNames = [
                                    'miniatures' => 'Miniatures',
                                    'architecture' => 'Architecture',
                                    'art' => 'Art & Sculptures',
                                    'functional' => 'Functional Items',
                                    'toys' => 'Toys & Figurines'
                                ];
                                $displayName = $categoryNames[$product->category] ?? ucfirst($product->category);
                            @endphp
                            <span class="category-tag">{{ $displayName }}</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        @if($reviewsCount > 0)
            <div class="customer-reviews-section">
                <h2>Customer Reviews</h2>

                <div class="reviews-summary">
                    <div class="reviews-score">
                        <div class="score-number">{{ number_format($averageRating, 1) }}</div>
                        <div class="score-stars">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= round($averageRating))
                                    ⭐
                                @else
                                    ☆
                                @endif
                            @endfor
                        </div>
                        <div class="score-count">{{ $reviewsCount }} {{ $reviewsCount == 1 ? 'review' : 'reviews' }}</div>
                    </div>
                </div>

                <div class="reviews-list">
                    @foreach($product->reviews->sortByDesc('created_at') as $review)
                        <div class="review-card" data-review-id="{{ $review->review_id }}">
                            <div class="review-header">
                                <div class="reviewer-avatar">{{ strtoupper(substr($review->user->name, 0, 1)) }}</div>
                                <div class="reviewer-info">
                                    <div class="reviewer-name">{{ $review->user->name }}</div>
                                    <div class="review-date">{{ $review->created_at->format('M d, Y') }}</div>
                                </div>
                                @auth
                                    @if(Auth::user()->role === 'admin')
                                        <button class="delete-review-btn" data-review-id="{{ $review->review_id }}" title="Delete review">
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                <path d="M2 4h12M5.333 4V2.667a1.333 1.333 0 0 1 1.334-1.334h2.666a1.333 1.333 0 0 1 1.334 1.334V4m2 0v9.333a1.333 1.333 0 0 1-1.334 1.334H4.667a1.333 1.333 0 0 1-1.334-1.334V4h9.334Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </button>
                                    @endif
                                @endauth
                            </div>
                            <div class="review-stars">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $review->rating)
                                        ★
                                    @else
                                        ☆
                                    @endif
                                @endfor
                            </div>
                            <div class="review-text">
                                {{ $review->review_text }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Add Review Form Section - MOVED ABOVE existing reviews list -->
        <div class="add-review-section">
            <h2>Write a Review</h2>

            @auth
                <form id="add-review-form" class="review-form">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->product_id }}">

                    <div class="form-group">
                        <label for="rating">Rating *</label>
                        <div class="star-rating-input">
                            <input type="radio" name="rating" id="star1" value="1" required>
                            <label for="star1" title="1 star">★</label>
                            <input type="radio" name="rating" id="star2" value="2">
                            <label for="star2" title="2 stars">★</label>
                            <input type="radio" name="rating" id="star3" value="3">
                            <label for="star3" title="3 stars">★</label>
                            <input type="radio" name="rating" id="star4" value="4">
                            <label for="star4" title="4 stars">★</label>
                            <input type="radio" name="rating" id="star5" value="5">
                            <label for="star5" title="5 stars">★</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="review_text">Your Review *</label>
                        <textarea
                            id="review_text"
                            name="review_text"
                            rows="5"
                            placeholder="Share your experience with this product..."
                            required
                        ></textarea>
                    </div>

                    <button type="submit" class="submit-review-btn">Submit Review</button>
                </form>
            @else
                <div class="login-prompt">
                    <p>Please <a href="{{ route('auth.show') }}">log in</a> to write a review.</p>
                </div>
            @endauth
        </div>

        @if($reviewsCount == 0)
            <div class="customer-reviews-section">
                <h2>Customer Reviews</h2>
                <p class="no-reviews">No reviews yet. Be the first to review this product!</p>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/product-details.js', 'resources/js/add-to-cart.js'])
@endpush
