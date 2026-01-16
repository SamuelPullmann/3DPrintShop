// Add to Cart functionality for Home page and Product Details page

document.addEventListener('DOMContentLoaded', function () {
    // Handle all "Add to Cart" buttons
    const addToCartButtons = document.querySelectorAll('.add-to-cart-btn, .product-details-add-to-cart');

    addToCartButtons.forEach(button => {
        button.addEventListener('click', async function (e) {
            e.preventDefault();

            const productId = this.dataset.productId;
            const originalText = this.innerHTML;

            // Disable button during request
            this.disabled = true;
            this.innerHTML = 'Adding...';

            try {
                const response = await fetch('/cart/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        quantity: 1
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    // Show success feedback
                    this.innerHTML = '✓ Added!';
                    this.style.background = '#4caf50';

                    // Update cart badge in navigation if it exists
                    updateCartBadge(data.cart_count);

                    // Reset button after 2 seconds
                    setTimeout(() => {
                        this.innerHTML = originalText;
                        this.style.background = '';
                        this.disabled = false;
                    }, 2000);
                } else {
                    console.error('Failed to add product to cart');
                    this.innerHTML = originalText;
                    this.disabled = false;
                }
            } catch (error) {
                console.error('Error:', error);
                this.innerHTML = originalText;
                this.disabled = false;
            }
        });
    });

    // Function to update cart badge count in navigation
    function updateCartBadge(count) {
        const cartLink = document.querySelector('.nav-link-cart');
        if (!cartLink) return;

        let badge = cartLink.querySelector('.cart-badge');

        if (count > 0) {
            if (!badge) {
                badge = document.createElement('span');
                badge.className = 'cart-badge';
                cartLink.appendChild(badge);
            }
            badge.textContent = count;
        } else {
            if (badge) {
                badge.remove();
            }
        }
    }
});
