// Shopping Cart - Quantity Controls

document.addEventListener('DOMContentLoaded', function () {
    // Quantity increase/decrease buttons
    const increaseButtons = document.querySelectorAll('.qty-increase');
    const decreaseButtons = document.querySelectorAll('.qty-decrease');

    increaseButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const productId = this.dataset.productId;
            const input = document.querySelector(`.qty-input[data-product-id="${productId}"]`);
            const form = this.closest('.quantity-form');

            let currentValue = parseInt(input.value);
            if (currentValue < 99) {
                input.value = currentValue + 1;
                form.submit();
            }
        });
    });

    decreaseButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const productId = this.dataset.productId;
            const input = document.querySelector(`.qty-input[data-product-id="${productId}"]`);
            const form = this.closest('.quantity-form');

            let currentValue = parseInt(input.value);
            if (currentValue > 1) {
                input.value = currentValue - 1;
                form.submit();
            }
        });
    });
});

