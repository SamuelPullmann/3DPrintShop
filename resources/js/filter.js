// Simple filter UI interactions for the Home page
// - Toggle collapse on filter sections when their title is clicked
// - noUiSlider for price range selection
// - Preserve filter state in localStorage
// - Mobile filter toggle (fullscreen overlay)
// - Auto-reset mobile state on window resize

import noUiSlider from 'nouislider';
import 'nouislider/dist/nouislider.css';

document.addEventListener('DOMContentLoaded', function () {
    // Mobile filter toggle
    const mobileToggleBtn = document.getElementById('mobile-filter-toggle');
    const filtersSidebar = document.getElementById('filters-sidebar');
    const applyFiltersBtn = document.getElementById('apply-filters');
    const productsArea = document.getElementById('products-area');

    // Function to close mobile filter overlay
    function closeMobileFilter() {
        if (filtersSidebar) filtersSidebar.classList.remove('open');
        if (productsArea) productsArea.classList.remove('hidden');
    }

    if (mobileToggleBtn && filtersSidebar && productsArea) {
        // Open filter overlay on mobile
        mobileToggleBtn.addEventListener('click', function () {
            filtersSidebar.classList.add('open');
            productsArea.classList.add('hidden');
        });

        // Close filter overlay and show products
        if (applyFiltersBtn) {
            applyFiltersBtn.addEventListener('click', function () {
                closeMobileFilter();
                // TODO: Here you can add logic to fetch filtered products from API
            });
        }
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
        noUiSlider.create(priceSlider, {
            start: [0, 100],
            connect: true,
            range: {
                'min': 0,
                'max': 100
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
