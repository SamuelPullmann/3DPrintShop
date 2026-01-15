// Simple filter UI interactions for the Home page
// - Toggle collapse on filter sections when their title is clicked
// - noUiSlider for price range selection
// - Preserve filter state in localStorage
// - Mobile filter toggle (fullscreen overlay)
// - Auto-reset mobile state on window resize
// - Apply filters and fetch filtered products

import noUiSlider from 'nouislider';
import 'nouislider/dist/nouislider.css';

document.addEventListener('DOMContentLoaded', function () {
    // Mobile filter toggle
    const mobileToggleBtn = document.getElementById('mobile-filter-toggle');
    const filtersSidebar = document.getElementById('filters-sidebar');
    const applyFiltersBtn = document.getElementById('apply-filters');
    const productsArea = document.getElementById('products-area');
    const productsGrid = document.getElementById('products-grid');

    // Function to collect filter values
    function getFilterData() {
        const filterData = {
            type: [],
            cat: [],
            price_min: null,
            price_max: null
        };

        // Collect checked product types
        document.querySelectorAll('input[name="type[]"]:checked').forEach(function(cb) {
            filterData.type.push(cb.value);
        });

        // Collect checked categories
        document.querySelectorAll('input[name="cat[]"]:checked').forEach(function(cb) {
            filterData.cat.push(cb.value);
        });

        // Get price range from slider
        if (priceSlider && priceSlider.noUiSlider) {
            const values = priceSlider.noUiSlider.get();
            filterData.price_min = values[0];
            filterData.price_max = values[1];
        }

        return filterData;
    }

    // Function to apply filters and fetch products
    function applyFilters() {
        const filterData = getFilterData();

        // Get max price from slider element
        const maxPrice = priceSlider ? (priceSlider.maxPriceValue || 100) : 100;

        // Build query string
        const params = new URLSearchParams();

        if (filterData.type.length > 0) {
            filterData.type.forEach(t => params.append('type[]', t));
        }

        if (filterData.cat.length > 0) {
            filterData.cat.forEach(c => params.append('cat[]', c));
        }

        // Only send price params if they differ from default values
        if (filterData.price_min !== null && filterData.price_min > 0) {
            params.append('price_min', filterData.price_min);
        }

        if (filterData.price_max !== null && filterData.price_max < maxPrice) {
            params.append('price_max', filterData.price_max);
        }

        // Fetch filtered products
        fetch('/?'+ params.toString(), {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            renderProducts(data.data);
        })
        .catch(error => {
            console.error('Error fetching filtered products:', error);
        });
    }

    // Function to render products in the grid
    function renderProducts(products) {
        if (!productsGrid) return;

        if (products.length === 0) {
            productsGrid.innerHTML = '<div class="no-products"><p>No products found.</p></div>';
            return;
        }

        productsGrid.innerHTML = products.map(product => {
            const isAdmin = document.querySelector('.add-product-btn') !== null;

            let adminActions = '';
            if (isAdmin) {
                adminActions = `
                    <div class="product-actions-menu">
                        <button class="product-menu-btn" aria-label="Product options">⋮</button>
                        <div class="product-dropdown">
                            <button class="product-dropdown-item edit-product-btn" data-product-id="${product.product_id}">Edit</button>
                            <button class="product-dropdown-item delete-product-btn" data-product-id="${product.product_id}">Delete</button>
                        </div>
                    </div>
                `;
            }

            const imageHtml = product.file_path
                ? `<img src="/products/${product.product_id}/image" alt="${product.name}" class="product-img" loading="lazy">`
                : `<div class="product-img-placeholder"></div>`;

            return `
                <article class="product-card" data-product-id="${product.product_id}">
                    ${adminActions}
                    <a href="/products/${product.product_id}" class="product-link">
                        ${imageHtml}
                        <h3 class="product-title">${product.name}</h3>
                        <p class="product-price">€${parseFloat(product.price).toFixed(2)}</p>
                    </a>
                    <button class="add-to-cart-btn" data-product-id="${product.product_id}" aria-label="Add to cart">
                        <img src="/images/cart.png" alt="Cart" class="cart-icon">
                        Add to Cart
                    </button>
                </article>
            `;
        }).join('');

        // Re-attach event listeners for admin actions if needed
        if (window.attachAdminProductListeners) {
            window.attachAdminProductListeners();
        }
    }

    // Function to close mobile filter overlay
    function closeMobileFilter() {
        if (filtersSidebar) filtersSidebar.classList.remove('open');
        if (productsArea) productsArea.classList.remove('hidden');
    }

    // Apply filters button - works on both mobile and desktop
    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', function () {
            closeMobileFilter();
            applyFilters();
        });
    }

    if (mobileToggleBtn && filtersSidebar && productsArea) {
        // Open filter overlay on mobile
        mobileToggleBtn.addEventListener('click', function () {
            filtersSidebar.classList.add('open');
            productsArea.classList.add('hidden');
        });
    }

    // Handle window resize - reset mobile filter state when resizing to desktop
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            // If window is wider than 640px (desktop), ensure mobile overlay is closed
            if (window.innerWidth > 640) {
                closeMobileFilter();
            }
        }, 100); // Debounce resize events
    });

    // Collapse/expand filter sections
    document.querySelectorAll('.filters-section-title').forEach(function (titleEl) {
        titleEl.addEventListener('click', function () {
            const section = titleEl.closest('.filters-section');
            if (!section) return;
            section.classList.toggle('collapsed');
        });
    });

    // Price range slider using noUiSlider
    const priceSlider = document.getElementById('price-slider');
    const priceMinLabel = document.getElementById('price-min-label');
    const priceMaxLabel = document.getElementById('price-max-label');

    if (priceSlider && priceMinLabel && priceMaxLabel) {
        // Get max price from data attribute (set by backend)
        const maxPrice = parseInt(priceSlider.getAttribute('data-max-price')) || 100;

        noUiSlider.create(priceSlider, {
            start: [0, maxPrice],
            connect: true,
            range: {
                'min': 0,
                'max': maxPrice
            },
            step: 1,
            tooltips: false,
            format: {
                to: function (value) {
                    return Math.round(value);
                },
                from: function (value) {
                    return Number(value);
                }
            }
        });

        // Update labels when slider changes
        priceSlider.noUiSlider.on('update', function (values) {
            priceMinLabel.textContent = values[0] + '€';
            priceMaxLabel.textContent = values[1] + '€';
        });

        // Store maxPrice for filter comparisons
        priceSlider.maxPriceValue = maxPrice;
    }

    // Optional: preserve simple filter state in localStorage (checkboxes)
    const FILTER_STATE_KEY = '3dps_filter_state_v1';
    function saveCheckboxState() {
        const data = {};
        document.querySelectorAll('.filters-card input[type="checkbox"]').forEach(function (cb) {
            data[cb.name || cb.id || cb.value] = cb.checked;
        });
        try { localStorage.setItem(FILTER_STATE_KEY, JSON.stringify(data)); } catch (e) {}
    }
    function restoreCheckboxState() {
        try {
            const raw = localStorage.getItem(FILTER_STATE_KEY);
            if (!raw) return;
            const data = JSON.parse(raw);
            document.querySelectorAll('.filters-card input[type="checkbox"]').forEach(function (cb) {
                const key = cb.name || cb.id || cb.value;
                if (key in data) cb.checked = !!data[key];
            });
        } catch (e) { /* ignore */ }
    }

    document.querySelectorAll('.filters-card input[type="checkbox"]').forEach(function (cb) {
        cb.addEventListener('change', saveCheckboxState);
    });

    restoreCheckboxState();
});
