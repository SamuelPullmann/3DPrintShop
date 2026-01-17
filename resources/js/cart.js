// Cart quantity update with AJAX - updates price without page reload

document.addEventListener('DOMContentLoaded', function () {
    const qtyDecreaseBtns = document.querySelectorAll('.qty-decrease');
    const qtyIncreaseBtns = document.querySelectorAll('.qty-increase');

    // Handle decrease button
    qtyDecreaseBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const input = document.querySelector(`.qty-input[data-product-id="${productId}"]`);
            let currentQty = parseInt(input.value);

            if (currentQty > 1) {
                currentQty--;
                updateQuantity(productId, currentQty);
            }
        });
    });

    // Handle increase button
    qtyIncreaseBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const input = document.querySelector(`.qty-input[data-product-id="${productId}"]`);
            let currentQty = parseInt(input.value);

            if (currentQty < 99) {
                currentQty++;
                updateQuantity(productId, currentQty);
            }
        });
    });

    // AJAX update quantity
    async function updateQuantity(productId, quantity) {
        const input = document.querySelector(`.qty-input[data-product-id="${productId}"]`);
        const cartItem = input.closest('.cart-item');
        const itemTotal = cartItem.querySelector('.cart-item-total');
        const unitPriceText = cartItem.querySelector('.cart-item-unit-price').textContent;
        const unitPrice = parseFloat(unitPriceText.replace('€', '').replace('/ pc', '').trim());

        try {
            const response = await fetch('/cart/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: quantity
                })
            });

            const data = await response.json();

            if (response.ok) {
                // Update quantity input
                input.value = quantity;

                // Update item total price
                const newItemTotal = unitPrice * quantity;
                itemTotal.textContent = '€' + newItemTotal.toFixed(2);

                // Update order summary
                updateOrderSummary(data.subtotal, data.shipping, data.total);
            } else {
                console.error('Failed to update cart');
                alert('Failed to update quantity. Please try again.');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Network error. Please try again.');
        }
    }

    // Update order summary without page reload
    function updateOrderSummary(subtotal, shipping, total) {
        const summaryRows = document.querySelectorAll('.order-summary-row');

        if (summaryRows.length >= 2) {
            // Update subtotal (first row)
            summaryRows[0].querySelector('span:last-child').textContent = '€' + parseFloat(subtotal).toFixed(2);

            // Update shipping (second row)
            summaryRows[1].querySelector('span:last-child').textContent = '€' + parseFloat(shipping).toFixed(2);
        }

        // Update total
        const totalRow = document.querySelector('.order-summary-total span:last-child');
        if (totalRow) {
            totalRow.textContent = '€' + parseFloat(total).toFixed(2);
        }

        // Update cart heading count
        const cartHeading = document.querySelector('.cart-heading');
        if (cartHeading) {
            const newCount = document.querySelectorAll('.cart-item').length;
            cartHeading.textContent = `Shopping Cart (${newCount})`;
        }
    }
});
