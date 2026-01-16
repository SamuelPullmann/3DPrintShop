// Simple filter UI interactions for the Home page
// - Toggle collapse on filter sections when their title is clicked
// - noUiSlider for price range selection
// - Mobile filter toggle (fullscreen overlay)
// - Auto-reset mobile state on window resize
// - Update hidden inputs for price range before form submission

import noUiSlider from 'nouislider';
import 'nouislider/dist/nouislider.css';

document.addEventListener('DOMContentLoaded', function () {
    // Mobile filter toggle
    const mobileToggleBtn = document.getElementById('mobile-filter-toggle');
    const filtersSidebar = document.getElementById('filters-sidebar');
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
    const priceMinInput = document.getElementById('price-min-input');
    const priceMaxInput = document.getElementById('price-max-input');

    if (priceSlider && priceMinLabel && priceMaxLabel) {
        // Get max price from data attribute (set by backend)
        const maxPrice = parseInt(priceSlider.getAttribute('data-max-price')) || 100;
        const currentMin = parseInt(priceMinInput?.value || 0);
        const currentMax = parseInt(priceMaxInput?.value || maxPrice);

        noUiSlider.create(priceSlider, {
            start: [currentMin, currentMax],
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

        // Update labels AND hidden inputs when slider changes
        priceSlider.noUiSlider.on('update', function (values) {
            priceMinLabel.textContent = values[0] + '€';
            priceMaxLabel.textContent = values[1] + '€';
            if (priceMinInput) priceMinInput.value = values[0];
            if (priceMaxInput) priceMaxInput.value = values[1];
        });
    }
});
