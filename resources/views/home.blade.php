@extends('layouts.app')

@section('title', 'Home')

@push('styles')
    @vite(['resources/css/home.css'])
@endpush

@section('content')
    <div class="home-layout">
        <button id="mobile-filter-toggle" class="mobile-filter-toggle">Filter</button>

        <aside class="filters-sidebar" id="filters-sidebar">
            <div class="filters-card">
                <h3 class="filters-title">Filters</h3>

                <form method="GET" action="{{ route('home') }}" id="filter-form">
                    <div class="filters-section">
                        <h4 class="filters-section-title">Product Type</h4>
                        <label class="checkbox-row">
                            <input type="checkbox" name="type[]" value="digital" {{ in_array('digital', request('type', [])) ? 'checked' : '' }}> Digital Model
                        </label>
                        <label class="checkbox-row">
                            <input type="checkbox" name="type[]" value="physical" {{ in_array('physical', request('type', [])) ? 'checked' : '' }}> Physical Model
                        </label>
                    </div>

                    <div class="filters-divider"></div>

                    <div class="filters-section">
                        <h4 class="filters-section-title">Categories</h4>
                        <label class="checkbox-row">
                            <input type="checkbox" name="cat[]" value="Miniatures" {{ in_array('Miniatures', request('cat', [])) ? 'checked' : '' }}> Miniatures
                        </label>
                        <label class="checkbox-row">
                            <input type="checkbox" name="cat[]" value="Architecture" {{ in_array('Architecture', request('cat', [])) ? 'checked' : '' }}> Architecture
                        </label>
                        <label class="checkbox-row">
                            <input type="checkbox" name="cat[]" value="Art & Sculptures" {{ in_array('Art & Sculptures', request('cat', [])) ? 'checked' : '' }}> Art &amp; Sculptures
                        </label>
                        <label class="checkbox-row">
                            <input type="checkbox" name="cat[]" value="Functional Items" {{ in_array('Functional Items', request('cat', [])) ? 'checked' : '' }}> Functional Items
                        </label>
                        <label class="checkbox-row">
                            <input type="checkbox" name="cat[]" value="Toys & Figurines" {{ in_array('Toys & Figurines', request('cat', [])) ? 'checked' : '' }}> Toys &amp; Figurines
                        </label>
                    </div>

                    <div class="filters-divider"></div>

                    <div class="filters-section">
                        <h4 class="filters-section-title">Price Range</h4>
                        <div class="price-range">
                            <div id="price-slider" data-max-price="{{ $maxPrice }}"></div>
                            <div class="price-values">
                                <span id="price-min-label">€{{ request('price_min', 0) }}</span>
                                <span id="price-max-label">€{{ request('price_max', $maxPrice) }}</span>
                            </div>
                            <input type="hidden" name="price_min" id="price-min-input" value="{{ request('price_min', 0) }}">
                            <input type="hidden" name="price_max" id="price-max-input" value="{{ request('price_max', $maxPrice) }}">
                        </div>
                    </div>

                    <button type="submit" class="apply-filters-btn">Apply Filters</button>
                </form>
            </div>
        </aside>

        <section class="products-area" id="products-area">
            <div class="products-header">
                <h2>Products</h2>
                @auth
                    @if(Auth::user()->role === 'admin')
                        <button id="toggle-add-product" class="add-product-btn">+ Add Product</button>
                    @endif
                @endauth
            </div>

            <!-- Add Product Form (hidden by default) -->
            @auth
                @if(Auth::user()->role === 'admin')
                    <div id="add-product-form" class="add-product-form">
                        <form id="product-form" method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="product-name">Product Name *</label>
                                    <input type="text" id="product-name" name="name" required>
                                </div>
                                <div class="form-group">
                                    <label for="product-price">Price (€) *</label>
                                    <input type="number" id="product-price" name="price" step="0.01" min="0" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="product-type">Product Type *</label>
                                <select id="product-type" name="product_type" required>
                                    <option value="">Select Type</option>
                                    <option value="Digital">Digital Model</option>
                                    <option value="Physical">Physical Model</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="product-category">Category *</label>
                                <select id="product-category" name="category" required>
                                    <option value="" disabled selected hidden>Select Category</option>
                                    <option value="Miniatures">Miniatures</option>
                                    <option value="Architecture">Architecture</option>
                                    <option value="Art & Sculptures">Art & Sculptures</option>
                                    <option value="Functional Items">Functional Items</option>
                                    <option value="Toys & Figurines">Toys & Figurines</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="product-description">Description</label>
                                <textarea id="product-description" name="description" rows="4"></textarea>
                            </div>

                            <div class="form-group">
                                <label for="product-image">Product Image</label>
                                <input type="file" id="product-image" name="image" accept="image/jpeg,image/png,image/jpg,image/gif">
                                <small>Max 2MB, formats: JPG, PNG, GIF</small>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn-submit">Add Product</button>
                                <button type="button" id="cancel-add-product" class="btn-cancel">Cancel</button>
                            </div>
                        </form>
                    </div>
                @endif
            @endauth

            <div class="products-grid" id="products-grid">
                @forelse ($products as $product)
                    <article class="product-card" data-product-id="{{ $product->product_id }}">
                        @auth
                            @if(Auth::user()->role === 'admin')
                                <div class="product-actions-menu">
                                    <button class="product-menu-btn" aria-label="Product options">⋮</button>
                                    <div class="product-dropdown">
                                        <button class="product-dropdown-item edit-product-btn" data-product-id="{{ $product->product_id }}">Edit</button>
                                        <button class="product-dropdown-item delete-product-btn" data-product-id="{{ $product->product_id }}">Delete</button>
                                    </div>
                                </div>
                            @endif
                        @endauth
                        <a href="{{ route('product.details', $product->product_id) }}" class="product-link">
                            @if($product->file_path)
                                <img src="{{ route('product.image', $product->product_id) }}"
                                     alt="{{ $product->name }}"
                                     class="product-img"
                                     loading="lazy">
                            @else
                                <div class="product-img-placeholder"></div>
                            @endif
                            <h3 class="product-title">{{ $product->name }}</h3>
                            <p class="product-price">€{{ number_format($product->price, 2) }}</p>
                        </a>
                        <button class="add-to-cart-btn" data-product-id="{{ $product->product_id }}" aria-label="Add to cart">
                            <img src="{{ asset('images/cart.png') }}" alt="Cart" class="cart-icon">
                            Add to Cart
                        </button>
                    </article>
                @empty
                    <div class="no-products">
                        <p>No products found.</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($products->hasPages())
                <div class="products-paging">
                    {{ $products->appends(request()->query())->links() }}
                </div>
            @endif
        </section>
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/filter.js', 'resources/js/add-to-cart.js'])
    @auth
        @if(Auth::user()->role === 'admin')
            @vite(['resources/js/admin-product.js'])
        @endif
    @endauth
@endpush
