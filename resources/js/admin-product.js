document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.getElementById('toggle-add-product');
    const addProductForm = document.getElementById('add-product-form');
    const cancelBtn = document.getElementById('cancel-add-product');
    const productForm = document.getElementById('product-form');

    if (!toggleBtn || !addProductForm) return;

    // Toggle form visibility
    toggleBtn.addEventListener('click', function () {
        if (addProductForm.classList.contains('show')) {
            addProductForm.classList.remove('show');
            toggleBtn.textContent = '+ Add Product';
            productForm.reset();
            // Clear any edit mode data
            delete productForm.dataset.editMode;
            delete productForm.dataset.productId;
            productForm.action = productForm.dataset.originalAction || '/products';
            productForm.querySelector('[name="_method"]')?.remove();
        } else {
            addProductForm.classList.add('show');
            toggleBtn.textContent = '− Hide Form';
            // Smooth scroll to form
            addProductForm.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    });

    // Cancel button - hide form and reset
    cancelBtn.addEventListener('click', function () {
        addProductForm.classList.remove('show');
        toggleBtn.textContent = '+ Add Product';
        productForm.reset();
        // Clear any edit mode data
        delete productForm.dataset.editMode;
        delete productForm.dataset.productId;
        productForm.action = productForm.dataset.originalAction || '/products';
        productForm.querySelector('[name="_method"]')?.remove();
    });

    // Store original action for resetting
    if (!productForm.dataset.originalAction) {
        productForm.dataset.originalAction = productForm.action;
    }

    // Handle product form submission via AJAX
    productForm.addEventListener('submit', function(e) {
        e.preventDefault(); // PREVENT normal form submit!

        const formData = new FormData(productForm);
        const isEditMode = productForm.dataset.editMode === 'true';
        const productId = productForm.dataset.productId;

        const url = isEditMode ? `/products/${productId}` : '/products';

        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(product => {
            if (isEditMode) {
                // For edit mode, reload to update the product
                window.location.reload();
            } else {
                // For add mode, dynamically add product to grid
                addProductToGrid(product);

                // Reset and hide form
                productForm.reset();
                addProductForm.classList.remove('show');
                toggleBtn.textContent = '+ Add Product';
            }
        })
        .catch(error => {
            console.error('Error saving product:', error);
            if (error.errors) {
                let errorMessage = 'Validation errors:\n';
                Object.keys(error.errors).forEach(key => {
                    errorMessage += `- ${error.errors[key].join('\n- ')}\n`;
                });
                alert(errorMessage);
            } else {
                alert('Error saving product. Please try again.');
            }
        });
    });

    // Function to add product to grid
    function addProductToGrid(product) {
        const productsGrid = document.getElementById('products-grid');

        // Remove "no products" message if exists
        const noProducts = productsGrid.querySelector('.no-products');
        if (noProducts) {
            noProducts.remove();
        }

        const productCard = document.createElement('article');
        productCard.className = 'product-card';
        productCard.setAttribute('data-product-id', product.product_id);

        const isAdmin = document.querySelector('.product-actions-menu') !== null;

        productCard.innerHTML = `
            ${isAdmin ? `
                <div class="product-actions-menu">
                    <button class="product-menu-btn" aria-label="Product options">⋮</button>
                    <div class="product-dropdown">
                        <button class="product-dropdown-item edit-product-btn" data-product-id="${product.product_id}">Edit</button>
                        <button class="product-dropdown-item delete-product-btn" data-product-id="${product.product_id}">Delete</button>
                    </div>
                </div>
            ` : ''}
            <a href="/products/${product.product_id}/details" class="product-link">
                ${product.file_path ?
                    `<img src="/products/${product.product_id}/image" alt="${product.name}" class="product-img" loading="lazy">` :
                    `<div class="product-img-placeholder"></div>`
                }
                <h3 class="product-title">${product.name}</h3>
                <p class="product-price">€${parseFloat(product.price).toFixed(2)}</p>
            </a>
            <button class="add-to-cart-btn" data-product-id="${product.product_id}" aria-label="Add to cart">
                <img src="/images/cart.png" alt="Cart" class="cart-icon">
                Add to Cart
            </button>
        `;

        // Add to beginning of grid
        productsGrid.insertBefore(productCard, productsGrid.firstChild);
    }

    // Dropdown menu functionality
    document.addEventListener('click', function (e) {
        // Close all dropdowns when clicking outside
        if (!e.target.closest('.product-actions-menu')) {
            document.querySelectorAll('.product-dropdown.show').forEach(dropdown => {
                dropdown.classList.remove('show');
            });
        }

        // Toggle dropdown when clicking the menu button
        if (e.target.closest('.product-menu-btn')) {
            e.stopPropagation();
            const menuBtn = e.target.closest('.product-menu-btn');
            const dropdown = menuBtn.nextElementSibling;

            // Close other dropdowns
            document.querySelectorAll('.product-dropdown.show').forEach(d => {
                if (d !== dropdown) d.classList.remove('show');
            });

            // Toggle current dropdown
            dropdown.classList.toggle('show');
        }
    });

    // Edit product button handler
    document.addEventListener('click', function (e) {
        if (e.target.closest('.edit-product-btn')) {
            const btn = e.target.closest('.edit-product-btn');
            const productId = btn.dataset.productId;

            // Fetch product data
            fetch(`/products/${productId}`)
                .then(response => response.json())
                .then(product => {
                    // Populate form with product data
                    document.getElementById('product-name').value = product.name;
                    document.getElementById('product-price').value = product.price;
                    document.getElementById('product-type').value = product.product_type || '';
                    document.getElementById('product-category').value = product.category || '';
                    document.getElementById('product-description').value = product.description || '';

                    // Change form to edit mode
                    productForm.dataset.editMode = 'true';
                    productForm.dataset.productId = productId;
                    productForm.action = `/products/${productId}`;

                    // Add PUT method
                    let methodInput = productForm.querySelector('[name="_method"]');
                    if (!methodInput) {
                        methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        methodInput.value = 'PUT';
                        productForm.appendChild(methodInput);
                    }

                    // Show form and update button text
                    addProductForm.classList.add('show');
                    toggleBtn.textContent = '− Hide Form';
                    productForm.querySelector('.btn-submit').textContent = 'Update Product';

                    // Scroll to form
                    addProductForm.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

                    // Close dropdown
                    document.querySelectorAll('.product-dropdown.show').forEach(dropdown => {
                        dropdown.classList.remove('show');
                    });
                })
                .catch(error => {
                    console.error('Error fetching product:', error);
                    alert('Error loading product data');
                });
        }
    });

    // Delete product button handler
    document.addEventListener('click', function (e) {
        if (e.target.closest('.delete-product-btn')) {
            const btn = e.target.closest('.delete-product-btn');
            const productId = btn.dataset.productId;

            if (confirm('Are you sure you want to delete this product?')) {
                const token = document.querySelector('meta[name="csrf-token"]')?.content;

                fetch(`/products/${productId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Failed to delete product');
                    }
                    return response.json();
                })
                .then(() => {
                    // Remove product card from DOM without reload
                    const productCard = document.querySelector(`.product-card[data-product-id="${productId}"]`);
                    if (productCard) {
                        productCard.remove();
                    }
                })
                .catch(error => {
                    console.error('Error deleting product:', error);
                    alert('Error deleting product');
                });
            }

            document.querySelectorAll('.product-dropdown.show').forEach(dropdown => {
                dropdown.classList.remove('show');
            });
        }
    });
});
