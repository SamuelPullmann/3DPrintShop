@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <div class="home-layout">
        <button id="mobile-filter-toggle" class="mobile-filter-toggle">Filter</button>

        <aside class="filters-sidebar" id="filters-sidebar">
            <div class="filters-card">
                <h3 class="filters-title">Filters</h3>

                <div class="filters-section">
                    <h4 class="filters-section-title">Product Type</h4>
                    <label class="checkbox-row"><input type="checkbox" name="type[]" value="digital" checked> Digital Model</label>
                    <label class="checkbox-row"><input type="checkbox" name="type[]" value="physical" checked> Physical Model</label>
                </div>

                <div class="filters-divider"></div>

                <div class="filters-section">
                    <h4 class="filters-section-title">Categories</h4>
                    <label class="checkbox-row"><input type="checkbox" name="cat[]" value="miniatures"> Miniatures</label>
                    <label class="checkbox-row"><input type="checkbox" name="cat[]" value="architecture"> Architecture</label>
                    <label class="checkbox-row"><input type="checkbox" name="cat[]" value="art"> Art &amp; Sculptures</label>
                    <label class="checkbox-row"><input type="checkbox" name="cat[]" value="functional"> Functional Items</label>
                    <label class="checkbox-row"><input type="checkbox" name="cat[]" value="toys"> Toys &amp; Figurines</label>
                </div>

                <div class="filters-divider"></div>

                <div class="filters-section">
                    <h4 class="filters-section-title">Price Range</h4>
                    <div class="price-range">
                        <div id="price-slider"></div>
                        <div class="price-values">
                            <span id="price-min-label">€0</span>
                            <span id="price-max-label">€100</span>
                        </div>
                    </div>
                </div>

                <button id="apply-filters" class="apply-filters-btn">Apply Filters</button>
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
                        <form id="product-form" enctype="multipart/form-data">
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
                                <label for="product-category">Category</label>
                                <select id="product-category" name="category">
                                    <option value="">Select Category (Optional)</option>
                                    <option value="miniatures">Miniatures</option>
                                    <option value="architecture">Architecture</option>
                                    <option value="art">Art & Sculptures</option>
                                    <option value="functional">Functional Items</option>
                                    <option value="toys">Toys & Figurines</option>
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
                        <span class="product-type" style="display:none;">{{ $product->product_type }}</span>
                        @if($product->category)
                            <span class="product-category" style="display:none;">{{ $product->category }}</span>
                        @endif
                        <span class="product-description" style="display:none;">{{ $product->description }}</span>
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
                    {{ $products->links() }}
                </div>
            @endif
        </section>
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/filter.js'])
    @auth
        @if(Auth::user()->role === 'admin')
            @vite(['resources/js/admin-product.js'])
        @endif
    @endauth
@endpush
