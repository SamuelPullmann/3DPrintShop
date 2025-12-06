// Admin Product Management - Toggle Add Product Form

document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.getElementById('toggle-add-product');
    const addProductForm = document.getElementById('add-product-form');
    const cancelBtn = document.getElementById('cancel-add-product');
    const productForm = document.getElementById('product-form');

    if (!toggleBtn || !addProductForm) return;

    let editingProductId = null; // Track if we're editing a product

    // Toggle form visibility
    toggleBtn.addEventListener('click', function () {
        if (addProductForm.classList.contains('show')) {
            addProductForm.classList.remove('show');
            toggleBtn.textContent = '+ Add Product';
            editingProductId = null;
            productForm.reset();
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
        editingProductId = null;
        productForm.querySelector('.btn-submit').textContent = 'Add Product';
    });

    // Form submit handler - send to backend
    productForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        const formData = new FormData(productForm);
        const submitBtn = productForm.querySelector('.btn-submit');
        const originalBtnText = submitBtn.textContent;

        // Disable button during submission
        submitBtn.disabled = true;
        submitBtn.textContent = editingProductId ? 'Updating...' : 'Adding...';

        try {
            const url = editingProductId ? `/products/${editingProductId}` : '/products';
            const method = editingProductId ? 'POST' : 'POST';

            // For update, we need to add _method field for Laravel
            if (editingProductId) {
                formData.append('_method', 'PUT');
            }

            const response = await fetch(url, {
                method: method,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: formData
            });

            const data = await response.json();

            if (response.ok) {
                // Success - show message and reset form
                alert(editingProductId ? 'Product updated successfully!' : 'Product added successfully!');
                productForm.reset();
                addProductForm.classList.remove('show');
                toggleBtn.textContent = '+ Add Product';
                editingProductId = null;
                productForm.querySelector('.btn-submit').textContent = 'Add Product';

                // Reload page to show changes
                location.reload();
            } else {
                // Validation errors
                if (data.errors) {
                    const errorMessages = Object.values(data.errors).flat().join('\n');
                    alert('Validation errors:\n' + errorMessages);
                } else {
                    alert('Error saving product. Please try again.');
                }
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Network error. Please check your connection and try again.');
        } finally {
            // Re-enable button
            submitBtn.disabled = false;
            submitBtn.textContent = originalBtnText;
        }
    });

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

    // Edit product button
    document.addEventListener('click', async function (e) {
        if (e.target.closest('.edit-product-btn')) {
            const btn = e.target.closest('.edit-product-btn');
            const productId = btn.dataset.productId;

            try {
                // Fetch product data
                const response = await fetch(`/products/${productId}`, {
                    headers: {
                        'Accept': 'application/json',
                    }
                });

                if (!response.ok) throw new Error('Failed to fetch product');

                const product = await response.json();

                // Populate form with product data
                document.getElementById('product-name').value = product.name || '';
                document.getElementById('product-price').value = product.price || '';
                document.getElementById('product-type').value = product.product_type || '';
                document.getElementById('product-category').value = product.category || '';
                document.getElementById('product-description').value = product.description || '';

                // Set editing mode
                editingProductId = productId;
                productForm.querySelector('.btn-submit').textContent = 'Update Product';

                // Show form
                addProductForm.classList.add('show');
                toggleBtn.textContent = '− Hide Form';
                addProductForm.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

                // Close dropdown
                document.querySelectorAll('.product-dropdown.show').forEach(dropdown => {
                    dropdown.classList.remove('show');
                });
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to load product data. Please try again.');
            }
        }
    });

    // Delete product button
    document.addEventListener('click', async function (e) {
        if (e.target.closest('.delete-product-btn')) {
            const btn = e.target.closest('.delete-product-btn');
            const productId = btn.dataset.productId;

            if (!confirm('Are you sure you want to delete this product?')) {
                return;
            }

            try {
                const response = await fetch(`/products/${productId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    }
                });

                if (response.ok) {
                    alert('Product deleted successfully!');

                    // Remove product card from DOM with animation
                    const productCard = document.querySelector(`[data-product-id="${productId}"]`);
                    if (productCard) {
                        productCard.style.opacity = '0';
                        productCard.style.transform = 'scale(0.8)';
                        productCard.style.transition = 'all 0.3s';

                        setTimeout(() => {
                            productCard.remove();

                            // Check if grid is empty
                            const grid = document.getElementById('products-grid');
                            if (!grid.querySelector('.product-card')) {
                                grid.innerHTML = '<div class="no-products"><p>No products found.</p></div>';
                            }
                        }, 300);
                    }
                } else {
                    const data = await response.json();
                    alert('Error deleting product: ' + (data.message || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Network error. Please check your connection and try again.');
            }

            // Close dropdown
            document.querySelectorAll('.product-dropdown.show').forEach(dropdown => {
                dropdown.classList.remove('show');
            });
        }
    });
});
